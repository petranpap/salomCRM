<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalonController extends Controller
{
    public function index()
    {
        $salons = Salon::withCount([
                'users as staff_count'    => fn($q) => $q->whereIn('role', User::SALON_ROLES),
                'customers as client_count',
                'appointments as appts_count',
            ])
            ->with('owner')
            ->orderBy('name')
            ->paginate(20);

        return view('salons.index', compact('salons'));
    }

    public function create()
    {
        return view('salons.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:120',
            'phone'     => 'nullable|string|max:30',
            'email'     => 'nullable|email|max:120',
            'address'   => 'nullable|string|max:255',
            'timezone'  => 'nullable|string|max:60',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $salon = Salon::create($data);

        return redirect()->route('salons.show', $salon)
            ->with('success', "Salon \"{$salon->name}\" created.");
    }

    public function show(Salon $salon)
    {
        $salon->loadCount([
            'users as staff_count'    => fn($q) => $q->whereIn('role', User::SALON_ROLES),
            'customers as client_count',
            'appointments as appts_count',
        ])->load('owner', 'users');

        $revenueTotal = Payment::where('salon_id', $salon->id)
            ->where('status', 'paid')
            ->sum('amount');

        $revenueToday = Payment::where('salon_id', $salon->id)
            ->where('status', 'paid')
            ->whereDate('paid_at', today())
            ->sum('amount');

        return view('salons.show', compact('salon', 'revenueTotal', 'revenueToday'));
    }

    public function edit(Salon $salon)
    {
        return view('salons.edit', compact('salon'));
    }

    public function update(Request $request, Salon $salon)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:120',
            'phone'     => 'nullable|string|max:30',
            'email'     => 'nullable|email|max:120',
            'address'   => 'nullable|string|max:255',
            'timezone'  => 'nullable|string|max:60',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $salon->update($data);

        return redirect()->route('salons.show', $salon)
            ->with('success', 'Salon updated.');
    }

    public function destroy(Salon $salon)
    {
        $name = $salon->name;
        $salon->delete();

        return redirect()->route('salons.index')
            ->with('success', "Salon \"{$name}\" deleted.");
    }

    /**
     * Clears onboarding_completed_at so the salon's owner is walked through the full
     * setup wizard again from step one on their next request — same gate a brand-new
     * salon starts behind (see EnsureSalonOnboarded).
     */
    public function resetOnboarding(Salon $salon)
    {
        $salon->update(['onboarding_completed_at' => null]);

        return redirect()->route('salons.show', $salon)
            ->with('success', "Onboarding reset for \"{$salon->name}\" — the owner will be walked through setup again on next login.");
    }
}
