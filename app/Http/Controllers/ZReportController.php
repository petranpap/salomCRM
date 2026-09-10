<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Payment;
use App\Models\ZReportClosure;
use App\Support\TempPdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ZReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('z-report.index', $this->gatherReportData($request));
    }

    public function print(Request $request): View
    {
        $data = $this->gatherReportData($request);

        $pdf = Pdf::loadView('pdf.z-report', $data)->setPaper('a4');
        $token = TempPdf::store($pdf->output());

        return view('pdf.print-wrapper', [
            'title' => 'Z Report — ' . $data['reportDate']->format('d/m/Y'),
            'token' => $token,
        ]);
    }

    public function close(Request $request): RedirectResponse
    {
        $authUser = auth()->user();
        $salonId  = $authUser->isSuperAdmin() ? null : $authUser->salon_id;

        abort_unless($salonId, 422, 'A specific salon must be selected before closing its day.');

        $reportDate = $request->filled('date')
            ? Carbon::parse($request->input('date'))->startOfDay()
            : today();

        if (ZReportClosure::isLocked($salonId, $reportDate)) {
            return redirect()->route('z-report.index', ['date' => $reportDate->toDateString()])
                ->with('error', 'This day has already been closed.');
        }

        $data = $this->gatherReportData($request);

        try {
            $closure = DB::transaction(function () use ($salonId, $reportDate, $data, $authUser) {
                $nextSequence = (int) ZReportClosure::withoutGlobalScopes()
                    ->where('salon_id', $salonId)
                    ->lockForUpdate()
                    ->max('sequence_number') + 1;

                return ZReportClosure::create([
                    'salon_id'        => $salonId,
                    'sequence_number' => $nextSequence,
                    'report_date'     => $reportDate->toDateString(),
                    'total_revenue'   => $data['totalRevenue'],
                    'cash_revenue'    => $data['cashRevenue'],
                    'card_revenue'    => $data['cardRevenue'],
                    'retail_revenue'  => $data['retailRevenue'],
                    'vat_collected'   => $data['vatCollected'],
                    'refunds_total'   => $data['refundsTotal'],
                    'receipt_count'   => $data['receiptCount'],
                    'closed_by'       => $authUser->id,
                    'closed_at'       => now(),
                ]);
            });
        } catch (\Illuminate\Database\QueryException) {
            return redirect()->route('z-report.index', ['date' => $reportDate->toDateString()])
                ->with('error', 'This day has already been closed.');
        }

        return redirect()->route('z-report.index', ['date' => $reportDate->toDateString()])
            ->with('success', "Z-{$closure->sequence_number} closed for {$reportDate->format('d/m/Y')}. Records for that day are now locked.");
    }

    protected function gatherReportData(Request $request): array
    {
        $authUser = auth()->user();
        $reportDate = $request->filled('date')
            ? Carbon::parse($request->input('date'))->startOfDay()
            : today();

        $paymentsQuery = Payment::query()
            ->whereDate('paid_at', $reportDate)
            ->where('status', 'paid')
            ->when(! $authUser->isSuperAdmin(), fn ($query) => $query->where('salon_id', $authUser->salon_id));

        $totalRevenue = (clone $paymentsQuery)->sum('amount');
        $cashRevenue  = (clone $paymentsQuery)->where('method', 'cash')->sum('amount');
        $cardRevenue  = (clone $paymentsQuery)->where('method', 'card')->sum('amount');
        $receiptCount = (clone $paymentsQuery)->count();

        // Refunded payments are never dated by paid_at (that's only stamped when a payment
        // goes through as 'paid'), so a refund is attributed to the day it was recorded.
        $refundsTotal = Payment::query()
            ->whereDate('created_at', $reportDate)
            ->where('status', 'refunded')
            ->when(! $authUser->isSuperAdmin(), fn ($query) => $query->where('salon_id', $authUser->salon_id))
            ->sum('amount');

        $retailRevenue = DB::table('payment_products')
            ->join('payments', 'payment_products.payment_id', '=', 'payments.id')
            ->whereDate('payments.paid_at', $reportDate)
            ->where('payments.status', 'paid')
            ->when(! $authUser->isSuperAdmin(), fn ($query) => $query->where('payments.salon_id', $authUser->salon_id))
            ->sum(DB::raw('payment_products.quantity * payment_products.unit_price'));

        // Each salon may have its own VAT rate (or none), so this is computed per-row rather than a flat percentage.
        $vatCollected = DB::table('payments')
            ->join('salons', 'payments.salon_id', '=', 'salons.id')
            ->whereDate('payments.paid_at', $reportDate)
            ->where('payments.status', 'paid')
            ->whereNotNull('salons.vat_rate')
            ->when(! $authUser->isSuperAdmin(), fn ($query) => $query->where('payments.salon_id', $authUser->salon_id))
            ->selectRaw('SUM(payments.amount - payments.amount / (1 + salons.vat_rate / 100)) as total')
            ->value('total') ?? 0;

        $appointmentsQuery = Appointment::query()
            ->whereDate('start', $reportDate)
            ->where('status', '!=', 'canceled')
            ->when(! $authUser->isSuperAdmin(), function ($query) use ($authUser) {
                $query->whereHas('customer', fn ($q) => $q->where('salon_id', $authUser->salon_id));
            });

        $totalAppointments     = (clone $appointmentsQuery)->count();
        $completedAppointments = (clone $appointmentsQuery)->where('status', 'completed')->count();

        $breakdownByStaff = Payment::query()
            ->selectRaw('staff_id, COUNT(DISTINCT appointment_id) as appointment_count, SUM(amount) as revenue')
            ->whereDate('paid_at', $reportDate)
            ->where('status', 'paid')
            ->when(! $authUser->isSuperAdmin(), fn ($query) => $query->where('salon_id', $authUser->salon_id))
            ->whereNotNull('staff_id')
            ->groupBy('staff_id')
            ->with('staff')
            ->get();

        $breakdownByCategory = Payment::query()
            ->selectRaw('service_categories.name as category_name, SUM(payments.amount) as revenue')
            ->join('appointments', 'payments.appointment_id', '=', 'appointments.id')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->join('service_categories', 'services.category_id', '=', 'service_categories.id')
            ->whereDate('payments.paid_at', $reportDate)
            ->where('payments.status', 'paid')
            ->when(! $authUser->isSuperAdmin(), fn ($query) => $query->where('payments.salon_id', $authUser->salon_id))
            ->groupBy('service_categories.id', 'service_categories.name')
            ->get();

        $breakdownByProduct = DB::table('payment_products')
            ->join('payments', 'payment_products.payment_id', '=', 'payments.id')
            ->join('products', 'payment_products.product_id', '=', 'products.id')
            ->selectRaw('products.name as product_name, SUM(payment_products.quantity) as quantity, SUM(payment_products.quantity * payment_products.unit_price) as revenue')
            ->whereDate('payments.paid_at', $reportDate)
            ->where('payments.status', 'paid')
            ->when(! $authUser->isSuperAdmin(), fn ($query) => $query->where('payments.salon_id', $authUser->salon_id))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('revenue')
            ->get();

        $closure = $authUser->isSuperAdmin()
            ? null
            : ZReportClosure::where('report_date', $reportDate->toDateString())->first();

        return [
            'salon'                 => $authUser->isSuperAdmin() ? null : $authUser->salon,
            'reportDate'            => $reportDate,
            'totalRevenue'          => $totalRevenue,
            'cashRevenue'           => $cashRevenue,
            'cardRevenue'           => $cardRevenue,
            'retailRevenue'         => $retailRevenue,
            'vatCollected'          => $vatCollected,
            'refundsTotal'          => $refundsTotal,
            'receiptCount'          => $receiptCount,
            'totalAppointments'     => $totalAppointments,
            'completedAppointments' => $completedAppointments,
            'breakdownByStaff'      => $breakdownByStaff,
            'breakdownByCategory'   => $breakdownByCategory,
            'breakdownByProduct'    => $breakdownByProduct,
            'closure'               => $closure,
        ];
    }
}
