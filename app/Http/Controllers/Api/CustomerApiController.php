<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    public function index(): JsonResponse
    {
        $customers = Customer::paginate(20);
        return response()->json($customers);
    }

    public function show($id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        return response()->json($customer);
    }

    public function store(CustomerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $data['salon_id'] = $user->isSuperAdmin()
            ? ($request->input('salon_id') ?: null)
            : $user->salon_id;

        $customer = Customer::create($data);
        return response()->json($customer, 201);
    }

    public function update(CustomerRequest $request, $id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $customer->update($request->validated());
        return response()->json($customer);
    }

    public function destroy($id): JsonResponse
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return response()->json(null, 204);
    }
}