<?php

namespace App\Console\Commands;

use App\Services\PrestamoService;
use Illuminate\Console\Command;

class MarcarPrestamosVencidos extends Command
{
    protected $signature = 'prestamos:marcar-vencidos';
    protected $description = 'Marca los préstamos vencidos automáticamente';

    public function handle(PrestamoService $prestamoService)
    {
        $this->info('Marcando préstamos vencidos...');
        
        $marcados = $prestamoService->marcarPrestamosVencidos();
        
        $this->info("{$marcados} préstamos marcados como vencidos.");
    }
}