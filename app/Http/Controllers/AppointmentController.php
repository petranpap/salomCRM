<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Salon;
use App\Models\Service;
use App\Models\StaffProfile;
use App\Models\ZReportClosure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();

        $appointments = Appointment::with(['customer', 'service', 'staffProfile.user', 'salon'])
            ->when(!$isSuperAdmin, fn($q) => $q->where('salon_id', $user->salon_id))
            ->latest('start')
            ->get();

        $calendarEvents = $appointments->filter(fn($a) => $a->customer && $a->service)->map(fn($a) => [
            'id'    => $a->id,
            'title' => $a->customer->name . ' — ' . $a->service->name,
            'start' => \Carbon\Carbon::parse($a->start)->format('Y-m-d\TH:i:s'),
            'end'   => $a->end ? \Carbon\Carbon::parse($a->end)->format('Y-m-d\TH:i:s') : null,
            'color' => $a->staffProfile?->color ?? '#C9907A',
        ])->values();

        $businessHours = $isSuperAdmin ? [] : ($user->salon?->fullCalendarBusinessHours() ?? []);

        return view('appointments.calendar', compact('appointments', 'calendarEvents', 'businessHours'));
    }

    public function create()
    {
        $user         = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();
        $salonId      = $user->salon_id;

        $customers = Customer::with('salon')->orderBy('name')
            ->when(!$isSuperAdmin, fn($q) => $q->where('salon_id', $salonId))
            ->get();

        $services = Service::with('salon')->orderBy('name')
            ->when(!$isSuperAdmin, fn($q) => $q->where('salon_id', $salonId))
            ->get();

        $staff = StaffProfile::with(['user.salon'])
            ->when(!$isSuperAdmin, fn($q) => $q->whereHas('user', fn($u) => $u->where('salon_id', $salonId)))
            ->get();

        $salons = $isSuperAdmin ? Salon::orderBy('name')->get() : collect();

        return view('appointments.create', compact('customers', 'services', 'staff', 'salons', 'isSuperAdmin'));
    }

    public function store(AppointmentRequest $request)
    {
        $user = Auth::user();
        $data = array_merge($request->validated(), ['status' => 'booked']);

        if (!$user->isSuperAdmin()) {
            $data['salon_id'] = $user->salon_id;
        } else {
            $data['salon_id'] = Customer::find($data['customer_id'])?->salon_id;
        }

        Appointment::create($data);

        return redirect()->route('appointments.index')->with('success', 'Appointment booked.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['customer', 'service', 'staffProfile.user', 'salon', 'payments', 'reminders']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $user         = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();
        $salonId      = $isSuperAdmin ? $appointment->salon_id : $user->salon_id;

        $customers = Customer::with('salon')->orderBy('name')
            ->when(!$isSuperAdmin, fn($q) => $q->where('salon_id', $user->salon_id))
            ->get();

        $services = Service::with('salon')->orderBy('name')
            ->when(!$isSuperAdmin, fn($q) => $q->where('salon_id', $user->salon_id))
            ->get();

        $staff = StaffProfile::with(['user.salon'])
            ->when(!$isSuperAdmin, fn($q) => $q->whereHas('user', fn($u) => $u->where('salon_id', $user->salon_id)))
            ->get();

        $salons = $isSuperAdmin ? Salon::orderBy('name')->get() : collect();

        return view('appointments.edit', compact('appointment', 'customers', 'services', 'staff', 'salons', 'isSuperAdmin'));
    }

    public function update(AppointmentRequest $request, Appointment $appointment)
    {
        // Locked-day guard lives in AppointmentRequest::withValidator() — it must run
        // as part of validation, not after, since a FormRequest resolves (and can
        // redirect back on failure) before this method body ever executes.
        $data = $request->validated();

        if (Auth::user()->isSuperAdmin() && !isset($data['salon_id'])) {
            $data['salon_id'] = Customer::find($data['customer_id'])?->salon_id;
        }

        $appointment->update($data);

        return redirect()->route('appointments.index')->with('success', 'Appointment updated.');
    }

    public function destroy(Appointment $appointment)
    {
        if (ZReportClosure::isLocked($appointment->salon_id, $appointment->start)) {
            return redirect()->route('appointments.index')
                ->with('error', 'This appointment falls on a day that has already been closed and can no longer be deleted.');
        }

        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted.');
    }
}
