<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeasiswaProgram extends Model
{
    protected $table = 'beasiswa_program';

    protected $fillable = [
        'nama',
        'deskripsi',
        'syarat',
        'jalur',
        'id_kategori',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriBeasiswa::class, 'id_kategori');
    }
}