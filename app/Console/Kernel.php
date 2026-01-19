<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Ejecutar diariamente a la medianoche
        $schedule->command('prestamos:marcar-vencidos')->daily();
        
        // Ejecutar reportes cada lunes a las 8:00 AM
        $schedule->command('reportes:generar')->weeklyOn(1, '8:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}