<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesApprovals;
use App\Http\Requests\ServiceRequest;
use App\Models\Salon;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    use HandlesApprovals;

    public function index()
    {
        $user = Auth::user();

        $services = Service::with('category')
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('salon_id', $user->salon_id))
            ->orderBy('name')
            ->get();

        $categories = ServiceCategory::when(!$user->isSuperAdmin(), fn($q) => $q->where('salon_id', $user->salon_id))
            ->orderBy('sort_order')->orderBy('name')->get();

        return view('services.index', compact('services', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();

        $categories = ServiceCategory::when(!$user->isSuperAdmin(), fn($q) => $q->where('salon_id', $user->salon_id))
            ->orderBy('name')->get();

        $salons = $user->isSuperAdmin() ? Salon::orderBy('name')->get() : collect();

        return view('services.create', compact('categories', 'salons'));
    }

    public function store(ServiceRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $data['salon_id'] = $user->isSuperAdmin()
            ? $request->input('salon_id')
            : $user->salon_id;

        $result = $this->applyOrQueue(Service::class, $data, null, $user);

        return redirect()->route('services.index')->with('success', $result === 'queued'
            ? 'Service submitted for owner approval.'
            : 'Service created.');
    }

    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $user = Auth::user();

        $categories = ServiceCategory::when(!$user->isSuperAdmin(), fn($q) => $q->where('salon_id', $user->salon_id))
            ->orderBy('name')->get();

        $salons = $user->isSuperAdmin() ? Salon::orderBy('name')->get() : collect();

        return view('services.edit', compact('service', 'categories', 'salons'));
    }

    public function update(ServiceRequest $request, Service $service)
    {
        $user = Auth::user();
        $data = $request->validated();

        if ($user->isSuperAdmin() && $request->has('salon_id')) {
            $data['salon_id'] = $request->input('salon_id');
        }

        $result = $this->applyOrQueue(Service::class, $data, $service, $user);

        return redirect()->route('services.index')->with('success', $result === 'queued'
            ? 'Change submitted for owner approval.'
            : 'Service updated.');
    }

    public function destroy(Service $service)
    {
        $result = $this->deleteOrQueue($service, Auth::user());

        return redirect()->route('services.index')->with('success', $result === 'queued'
            ? 'Deletion submitted for owner approval.'
            : 'Service deleted.');
    }
}
