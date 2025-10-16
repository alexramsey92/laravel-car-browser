<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('cars:scrape')->daily();
