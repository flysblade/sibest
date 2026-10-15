<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeasiswaOffering extends Model
{
    protected $table = 'beasiswa_offering';

    protected $fillable = [
        'id_program',
        'id_periode',
        'link_portal',
        'kontak_pic',
        'tanggal_buka',
        'tanggal_tutup',
        'status',
        'view_count',
    ];

    protected $casts = [
        'tanggal_buka' => 'date',
        'tanggal_tutup' => 'date',
        'view_count' => 'integer',
    ];
    
    public function program(): BelongsTo
    {
        return $this->belongsTo(BeasiswaProgram::class, 'id_program');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class, 'id_periode');
    }
}