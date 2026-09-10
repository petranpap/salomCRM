<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Salon;
use App\Http\Requests\CustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $customers = Customer::with('salon')
            ->orderBy('name')
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('salon_id', $user->salon_id))
            ->paginate(20);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $salons = Auth::user()->isSuperAdmin() ? Salon::orderBy('name')->get() : collect();
        return view('customers.create', compact('salons'));
    }

    public function store(CustomerRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $data['salon_id'] = $user->isSuperAdmin()
            ? ($request->input('salon_id') ?: null)
            : $user->salon_id;

        $customer = Customer::create($data);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $salons = Auth::user()->isSuperAdmin() ? Salon::orderBy('name')->get() : collect();
        return view('customers.edit', compact('customer', 'salons'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();

        if (Auth::user()->isSuperAdmin() && $request->has('salon_id')) {
            $data['salon_id'] = $request->input('salon_id') ?: null;
        }

        $customer->update($data);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated.');
    }

    public function flag(Request $request, Customer $customer)
    {
        abort_unless(Auth::user()->hasRole('owner', 'super_admin'), 403);

        $request->validate([
            'flag_type'  => ['nullable', 'in:vip,late,no_show,flagged'],
            'flag_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $customer->update($request->only('flag_type', 'flag_notes'));

        return redirect()->route('customers.show', $customer)->with('success', 'Client flag updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}
