<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'tanggal',
        'deskripsi',
        'kategori',
        'nominal',
        'operator',
        'id_user',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'nominal' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
