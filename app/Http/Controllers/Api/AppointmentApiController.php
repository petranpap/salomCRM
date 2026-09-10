<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentApiController extends Controller
{
    public function index(): JsonResponse
    {
        $appointments = Appointment::with(['customer', 'service', 'staffProfile.user'])->paginate(30);
        return response()->json($appointments);
    }

    public function store(AppointmentRequest $request): JsonResponse
    {
        $data = array_merge($request->validated(), ['status' => 'booked']);
        $user = $request->user();

        $data['salon_id'] = $user->isSuperAdmin()
            ? Customer::find($data['customer_id'])?->salon_id
            : $user->salon_id;

        $appointment = Appointment::create($data);
        return response()->json($appointment, 201);
    }

    public function show(Appointment $appointment): JsonResponse
    {
        return response()->json($appointment);
    }

    public function update(AppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        $appointment->update($request->validated());
        return response()->json($appointment);
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        $appointment->delete();
        return response()->json(null, 204);
    }
}