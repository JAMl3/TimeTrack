<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class CarbonMacrosServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Carbon::macro('diffInDaysExcludingWeekends', function ($dt) {
            $days = 0;
            $start = $this->copy();
            $end = Carbon::parse($dt);

            while ($start->lte($end)) {
                if (!$start->isWeekend()) {
                    $days++;
                }
                $start->addDay();
            }

            return $days - 1;
        });
    }
}
