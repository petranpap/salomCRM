<?php

return [

    // How far ahead of an appointment's start time to send its reminder.
    'hours_before' => (int) env('REMINDER_HOURS_BEFORE', 24),

];
