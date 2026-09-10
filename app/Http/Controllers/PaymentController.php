<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductMovement;
use App\Models\User;
use App\Models\ZReportClosure;
use App\Support\TempPdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $authUser = auth()->user();

        $payments = Payment::with(['appointment.customer', 'customer', 'staff'])
            ->when(! $authUser->isSuperAdmin(), fn ($query) => $query->where('salon_id', $authUser->salon_id))
            ->latest()
            ->paginate(20);

        return view('payments.index', compact('payments'));
    }

    public function create(Request $request): View
    {
        $authUser = auth()->user();

        $customers = Customer::query()
            ->when(! $authUser->isSuperAdmin(), fn ($query) => $query->where('salon_id', $authUser->salon_id))
            ->orderBy('name')
            ->get();

        // A rolling window (recent past + near future) rather than just "today" —
        // covers late payment for a past visit and prepayment for an upcoming one.
        $appointments = Appointment::query()
            ->whereBetween('start', [now()->subDays(30), now()->addDays(14)])
            ->where('status', '!=', 'canceled')
            ->when(! $authUser->isSuperAdmin(), function ($query) use ($authUser) {
                $query->whereHas('customer', fn ($q) => $q->where('salon_id', $authUser->salon_id));
            })
            ->with('customer', 'service')
            ->orderByDesc('start')
            ->get();

        // Deep-link support: e.g. "Record Payment" from an appointment outside that window,
        // or re-rendering after a validation error. Make sure it stays selectable.
        $requestedApptId = $request->input('appointment_id', old('appointment_id'));

        if ($requestedApptId && ! $appointments->contains('id', (int) $requestedApptId)) {
            $linkedAppointment = Appointment::query()
                ->when(! $authUser->isSuperAdmin(), function ($query) use ($authUser) {
                    $query->whereHas('customer', fn ($q) => $q->where('salon_id', $authUser->salon_id));
                })
                ->with('customer', 'service')
                ->find($requestedApptId);

            if ($linkedAppointment) {
                $appointments->prepend($linkedAppointment);
            }
        }

        $appointmentOptions = $appointments->map(fn ($a) => [
            'id'          => $a->id,
            'customer_id' => $a->customer_id,
            'amount'      => $a->service?->base_price,
            'label'       => trim(($a->customer?->name ?? 'Unknown') . ' — ' . ($a->service?->name ?? 'Service') . ' (' . $a->start->format('d/m H:i') . ')'),
        ])->values();

        $staffMembers = User::query()
            ->when(! $authUser->isSuperAdmin(), fn ($query) => $query->where('salon_id', $authUser->salon_id))
            ->whereIn('role', User::SALON_ROLES)
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->where('status', 'active')
            ->when(! $authUser->isSuperAdmin(), fn ($query) => $query->where('salon_id', $authUser->salon_id))
            ->orderBy('name')
            ->get(['id', 'name', 'sell_price', 'stock_qty']);

        return view('payments.create', compact('customers', 'appointmentOptions', 'staffMembers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount'                  => ['required', 'numeric', 'min:0.01'],
            'method'                  => ['required', 'in:cash,card,bank_transfer,other'],
            'appointment_id'          => ['nullable', 'exists:appointments,id'],
            'customer_id'             => ['nullable', 'exists:customers,id'],
            'guest_name'              => ['nullable', 'string', 'max:255'],
            'notes'                   => ['nullable', 'string'],
            'status'                  => ['nullable', 'in:pending,paid,refunded'],
            'products'                => ['nullable', 'array'],
            'products.*.product_id'   => ['nullable', 'exists:products,id'],
            'products.*.quantity'     => ['nullable', 'integer', 'min:1'],
        ]);

        // A named guest is only meaningful when there's no actual customer record linked.
        if (! empty($validated['customer_id']) || ! empty($validated['appointment_id'])) {
            $validated['guest_name'] = null;
        }

        $authUser = auth()->user();

        $validated['salon_id'] = $authUser->isSuperAdmin()
            ? $request->input('salon_id')
            : $authUser->salon_id;

        $validated['staff_id'] = auth()->id();

        $resolvedStatus = $validated['status'] ?? 'pending';
        $validated['status'] = $resolvedStatus;
        $validated['paid_at'] = $resolvedStatus === 'paid' ? now() : null;

        if ($resolvedStatus === 'paid' && ZReportClosure::isLocked($validated['salon_id'], now())) {
            return back()->withInput()
                ->with('error', "Today's Z-report has already been closed — this payment can't be recorded against a locked day.");
        }

        // Rows where the staff added a product line but left it blank — ignore them.
        $productLines = collect($validated['products'] ?? [])->filter(fn ($line) => ! empty($line['product_id']));

        $payment = DB::transaction(function () use ($validated, $productLines, $authUser) {
            $payment = Payment::create(collect($validated)->except('products')->all());

            $products = Product::whereIn('id', $productLines->pluck('product_id'))->get()->keyBy('id');

            foreach ($productLines as $line) {
                $product  = $products->get($line['product_id']);
                $quantity = (int) ($line['quantity'] ?? 1);

                if (! $product) {
                    continue;
                }

                $payment->products()->attach($product->id, [
                    'quantity'   => $quantity,
                    'unit_price' => $product->sell_price,
                ]);

                $product->decrement('stock_qty', $quantity);

                ProductMovement::create([
                    'product_id' => $product->id,
                    'qty_change' => -$quantity,
                    'reason'     => 'sale',
                    'ref_type'   => Payment::class,
                    'ref_id'     => $payment->id,
                    'user_id'    => $authUser->id,
                ]);
            }

            return $payment;
        });

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment): View
    {
        $payment->load(['appointment.customer', 'appointment.service', 'customer', 'staff', 'salon', 'products']);

        return view('payments.show', compact('payment'));
    }

    public function print(Payment $payment): View
    {
        $payment->load(['appointment.customer', 'appointment.service', 'customer', 'staff', 'salon', 'products']);

        // Printing the receipt is the real-world signal that the visit is done —
        // this is also what snapshots the visit into the customer's treatment history.
        // Skipped once the appointment's day has a closed Z-report: reprinting an old
        // receipt must never silently mutate a locked day's records.
        if ($payment->appointment
            && $payment->appointment->status !== 'completed'
            && ! ZReportClosure::isLocked($payment->salon_id, $payment->appointment->start)) {
            $payment->appointment->update(['status' => 'completed']);
        }

        $template = in_array($payment->salon?->receipt_template, \App\Models\Salon::RECEIPT_TEMPLATES, true)
            ? $payment->salon->receipt_template
            : 'classic';

        $pdf = Pdf::loadView("pdf.receipts.{$template}", compact('payment'))->setPaper('a4');
        $token = TempPdf::store($pdf->output());

        return view('pdf.print-wrapper', [
            'title' => 'Receipt #' . str_pad($payment->id, 6, '0', STR_PAD_LEFT),
            'token' => $token,
        ]);
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $relevantDate = $payment->paid_at ?? $payment->created_at;

        if (ZReportClosure::isLocked($payment->salon_id, $relevantDate)) {
            return redirect()->route('payments.index')
                ->with('error', 'This payment falls on a day that has already been closed and can no longer be deleted.');
        }

        DB::transaction(function () use ($payment) {
            foreach ($payment->products as $product) {
                $product->increment('stock_qty', $product->pivot->quantity);

                ProductMovement::create([
                    'product_id' => $product->id,
                    'qty_change' => $product->pivot->quantity,
                    'reason'     => 'adjustment',
                    'ref_type'   => Payment::class,
                    'ref_id'     => $payment->id,
                    'user_id'    => auth()->id(),
                ]);
            }

            $payment->delete();
        });

        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
}
