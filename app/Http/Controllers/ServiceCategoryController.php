<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesApprovals;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceCategoryController extends Controller
{
    use HandlesApprovals;

    public function index(): View
    {
        $authUser = auth()->user();

        $categories = ServiceCategory::query()
            ->when(! $authUser->isSuperAdmin(), fn ($query) => $query->where('salon_id', $authUser->salon_id))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('service-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('service-categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'color'       => ['nullable', 'string', 'size:7'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['nullable', 'integer'],
            'is_active'   => ['boolean'],
        ]);

        $authUser = auth()->user();

        $validated['salon_id'] = $authUser->isSuperAdmin()
            ? $request->input('salon_id')
            : $authUser->salon_id;

        $result = $this->applyOrQueue(ServiceCategory::class, $validated, null, $authUser);

        return redirect()->route('service-categories.index')->with('success', $result === 'queued'
            ? 'Category submitted for owner approval.'
            : 'Category created successfully.');
    }

    public function edit(ServiceCategory $serviceCategory): View
    {
        return view('service-categories.edit', compact('serviceCategory'));
    }

    public function update(Request $request, ServiceCategory $serviceCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'color'       => ['nullable', 'string', 'size:7'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order'  => ['nullable', 'integer'],
            'is_active'   => ['boolean'],
        ]);

        $result = $this->applyOrQueue(ServiceCategory::class, $validated, $serviceCategory, auth()->user());

        return redirect()->route('service-categories.index')->with('success', $result === 'queued'
            ? 'Change submitted for owner approval.'
            : 'Category updated successfully.');
    }

    public function destroy(ServiceCategory $serviceCategory): RedirectResponse
    {
        $result = $this->deleteOrQueue($serviceCategory, auth()->user());

        return redirect()->route('service-categories.index')->with('success', $result === 'queued'
            ? 'Deletion submitted for owner approval.'
            : 'Category deleted successfully.');
    }
}
