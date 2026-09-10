<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('temp-pdfs:prune')->hourly();
Schedule::command('appointments:send-reminders')->everyFifteenMinutes();
