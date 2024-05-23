<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_pesanan', 'metode_pembayaran', 'total_pembayaran', 'tanggal_pembayaran', 'jumlah_pembayaran', 'jumlah_kembalian', 'bukti_pembayaran'
    ];
}
