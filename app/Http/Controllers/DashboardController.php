<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Salon;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $today   = today();
        $isSuperAdmin = $user->isSuperAdmin();
        $salonId = $user->salon_id;

        // ── Appointments ──────────────────────────────────────────────
        $appointmentsToday = Appointment::with(['customer', 'service', 'staffProfile'])
            ->whereDate('start', $today)
            ->when(!$isSuperAdmin, fn($q) => $q->where('salon_id', $salonId))
            ->get();

        $completedToday      = $appointmentsToday->where('status', 'completed');
        $noShowAppointments  = $appointmentsToday->where('status', 'no-show');
        $pendingAppointments = $appointmentsToday->whereIn('status', ['booked', 'confirmed']);

        // ── Payments ──────────────────────────────────────────────────
        $todayRevenue    = 0;
        $recentPayments  = collect();
        $pendingPayments = 0;

        $todayRevenue = Payment::where('status', 'paid')
            ->whereDate('paid_at', $today)
            ->when(!$isSuperAdmin, fn($q) => $q->where('salon_id', $salonId))
            ->sum('amount');

        $recentPayments = Payment::with(['customer', 'appointment.service', 'appointment.customer'])
            ->where('status', 'paid')
            ->when(!$isSuperAdmin, fn($q) => $q->where('salon_id', $salonId))
            ->latest('paid_at')
            ->limit(6)
            ->get();

        $pendingPayments = Payment::where('status', 'pending')
            ->when(!$isSuperAdmin, fn($q) => $q->where('salon_id', $salonId))
            ->count();

        // ── Staff ──────────────────────────────────────────────────────
        $activeStaff = User::with('staffProfile')
            ->whereNotNull('salon_id')
            ->whereIn('role', User::SALON_ROLES)
            ->when(!$isSuperAdmin, fn($q) => $q->where('salon_id', $salonId))
            ->whereHas('staffProfile', fn($q) => $q->where('is_active', true))
            ->get();

        // ── Other stats ────────────────────────────────────────────────
        $lowStockProducts = Product::whereColumn('stock_qty', '<=', 'threshold_qty')
            ->when(!$isSuperAdmin, fn($q) => $q->where('salon_id', $salonId))
            ->get();

        $openTodos = Todo::where('status', 'open')
            ->when(!$isSuperAdmin, fn($q) => $q->where(
                fn($q2) => $q2->whereNull('assigned_to')->orWhere('assigned_to', $user->id)
            ))
            ->count();

        // ── Calendar events ─────────────────────────────────────────────
        $calendarEvents = $appointmentsToday->map(fn($a) => [
            'id'    => $a->id,
            'title' => ($a->customer?->name ?? 'Client') . ' — ' . ($a->service?->name ?? ''),
            'start' => $a->start,
            'end'   => $a->end,
            'color' => $a->staffProfile?->color ?? '#7d523c',
        ]);

        $businessHours = $isSuperAdmin ? [] : ($user->salon?->fullCalendarBusinessHours() ?? []);

        // ── Staff timeline ──────────────────────────────────────────────
        $staffTimeline = $activeStaff->map(function ($member) use ($appointmentsToday) {
            return [
                'id'           => $member->id,
                'name'         => $member->name,
                'specialty'    => $member->staffProfile?->specialty ?? '',
                'color'        => $member->staffProfile?->color ?? '#7d523c',
                'appointments' => $appointmentsToday
                    ->where('staff_id', $member->staffProfile?->id)
                    ->values(),
            ];
        })->filter(fn($m) => $m['appointments']->isNotEmpty())->values();

        // ── Super admin: per-salon breakdown ────────────────────────────
        $salonBreakdown = collect();
        $totalSalons    = 0;

        if ($isSuperAdmin) {
            $totalSalons = Salon::count();

            $salonBreakdown = Salon::withCount([
                    'users as staff_count'     => fn($q) => $q->whereIn('role', User::SALON_ROLES),
                    'customers as client_count',
                    'appointments as appts_today' => fn($q) => $q->whereDate('start', $today)->where('status', '!=', 'canceled'),
                ])
                ->with('owner')
                ->orderBy('name')
                ->get()
                ->map(function ($salon) use ($today) {
                    $salon->revenue_today = Payment::where('salon_id', $salon->id)
                        ->where('status', 'paid')
                        ->whereDate('paid_at', $today)
                        ->sum('amount');
                    return $salon;
                });
        }

        return view('dashboard', compact(
            'appointmentsToday', 'completedToday', 'noShowAppointments', 'pendingAppointments',
            'todayRevenue', 'recentPayments', 'pendingPayments',
            'activeStaff', 'lowStockProducts', 'openTodos',
            'calendarEvents', 'staffTimeline', 'businessHours',
            'isSuperAdmin', 'salonBreakdown', 'totalSalons'
        ));
    }
}
