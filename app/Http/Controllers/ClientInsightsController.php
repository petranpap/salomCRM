<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ClientInsightsController extends Controller
{
    public function index(Request $request)
    {
        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : $to->copy()->subDays(90)->startOfDay();

        $appointments = Appointment::whereBetween('start', [$from, $to])->get(['status', 'was_late']);

        $totals = [
            'completed' => $appointments->where('status', 'completed')->count(),
            'no_show'   => $appointments->where('status', 'no-show')->count(),
            'late'      => $appointments->where('was_late', true)->count(),
            'canceled'  => $appointments->where('status', 'canceled')->count(),
        ];

        $clients = Customer::withCount([
                'appointments as no_show_count' => fn ($q) => $q->whereBetween('start', [$from, $to])->where('status', 'no-show'),
                'appointments as late_count'    => fn ($q) => $q->whereBetween('start', [$from, $to])->where('was_late', true),
                'appointments as total_count'   => fn ($q) => $q->whereBetween('start', [$from, $to]),
            ])
            ->get()
            ->filter(fn ($customer) => $customer->no_show_count > 0 || $customer->late_count > 0)
            ->sortByDesc(fn ($customer) => $customer->no_show_count * 2 + $customer->late_count)
            ->values();

        return view('client-insights.index', [
            'totals'  => $totals,
            'clients' => $clients,
            'from'    => $from,
            'to'      => $to,
        ]);
    }
}
