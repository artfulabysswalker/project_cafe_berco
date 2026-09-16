<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_shift';

    protected $fillable = [
        'id_user',
        'nama_pegawai',
        'modal_awal',
        'total_penjualan_tunai',
        'total_penjualan_nontunai',
        'kas_akhir_aktual',
        'selisih',
        'status',
        'opened_at',
        'closed_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_shift', 'id_shift');
    }
}
