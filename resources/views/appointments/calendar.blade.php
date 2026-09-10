@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <p class="section-label">Schedule</p>
            <h1 class="page-title">Appointments</h1>
        </div>
        <a href="{{ route('appointments.create') }}" class="btn-primary">+ New Appointment</a>
    </div>

    <div class="card !p-4">
        <div id="appointments-calendar"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    window.SalonCalendar.init('appointments-calendar', @json($calendarEvents), {
        businessHours: @json($businessHours),
    });
});
</script>
@endpush
