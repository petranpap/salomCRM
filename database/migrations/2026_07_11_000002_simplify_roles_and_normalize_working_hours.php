<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected array $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    public function up(): void
    {
        // Collapse 'manager'/'reception' into 'staff', preserving the old label as a
        // free-text job title so that information isn't lost when the system role narrows.
        foreach (['manager', 'reception'] as $legacyRole) {
            $userIds = DB::table('users')->where('role', $legacyRole)->pluck('id');

            foreach ($userIds as $userId) {
                DB::table('staff_profiles')
                    ->where('user_id', $userId)
                    ->whereNull('job_title')
                    ->update(['job_title' => ucfirst($legacyRole)]);
            }

            DB::table('users')->where('role', $legacyRole)->update(['role' => 'staff']);
        }

        // Fix a pre-existing bug: the demo seeder passed an already-json_encode()'d string
        // into a cast-to-array attribute, so Eloquent encoded it a second time — the column
        // held a JSON string containing escaped JSON, not a usable object. Normalize every
        // row into the shape the new working-hours editor expects: all 7 days present, each
        // with active/start/end.
        foreach (DB::table('staff_profiles')->get(['id', 'working_hours']) as $profile) {
            DB::table('staff_profiles')
                ->where('id', $profile->id)
                ->update(['working_hours' => json_encode($this->normalize($profile->working_hours))]);
        }
    }

    public function down(): void
    {
        // Role/label consolidation and the malformed legacy JSON are not meaningfully reversible.
    }

    protected function normalize(?string $raw): array
    {
        $decoded = json_decode((string) $raw, true);

        // Double-encoded legacy value: first decode yields a string, decode again for the real data.
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        $decoded = is_array($decoded) ? $decoded : [];

        $result = [];

        foreach ($this->days as $day) {
            $value = $decoded[$day] ?? null;

            if (is_string($value) && str_contains($value, '-')) {
                // Legacy shape: "09:00-17:00"
                [$start, $end] = array_pad(explode('-', $value, 2), 2, null);
                $result[$day] = ['active' => true, 'start' => $start, 'end' => $end];
            } elseif (is_array($value) && ($value['active'] ?? false)) {
                $result[$day] = [
                    'active' => true,
                    'start'  => $value['start'] ?? null,
                    'end'    => $value['end'] ?? null,
                ];
            } else {
                $result[$day] = ['active' => false, 'start' => null, 'end' => null];
            }
        }

        return $result;
    }
};
