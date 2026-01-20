<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestamo extends Model
{
    use HasFactory;
    
    protected $table = 'table_prestamos';

    protected $fillable = [
        'usuario_id',
        'libro_id',
        'fecha_prestamo',
        'fecha_devolucion_estimada',
        'fecha_devolucion_real',
        'estado',
    ];
    protected $casts = [
        'fecha_prestamo' => 'date',
        'fecha_devolucion_estimada' => 'date',
        'fecha_devolucion_real' => 'date',
    ];

    protected $appends = ['esta_vencido'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function libro(): BelongsTo
    {
        return $this->belongsTo(Libro::class);
    }

    // Accessor para verificar si está vencido
    public function getEstaVencidoAttribute(): bool
    {
        if ($this->estado !== 'activo') {
            return false;
        }
        
        return now()->greaterThan($this->fecha_devolucion_estimada);
    }

    // Calcular días de retraso
    public function getDiasRetrasoAttribute(): int
    {
        if (!$this->esta_vencido) {
            return 0;
        }
        
        return now()->diffInDays($this->fecha_devolucion_estimada);
    }
}
