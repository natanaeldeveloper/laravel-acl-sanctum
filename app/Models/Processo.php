<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Processo extends Model
{
    use HasFactory;

    protected $table = 'processos';

    protected $fillable = [
        'valor_estimado',
        'objeto',
        'solicitante_id',
    ];

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id', 'id');
    }

    public function valorEstimado(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => str_replace(',', '.', str_replace('.', '', $value)),
            get: fn (string $value) => number_format($value, 2, ',', '.'),
        );
    }
}

