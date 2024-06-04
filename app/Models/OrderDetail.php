<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'detail_pesanan';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'id_pesanan', 'id_produk', 'jumlah_produk', 'harga_satuan', 'level'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_pesanan');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_produk');
    }

    public static function countProductsSold($productId = null)
    {
        $query = static::whereHas('order', function ($query) {
            $query->where('status_pesanan', '3'); // Selesai
        });

        if ($productId !== null) {
            $query->where('id_produk', $productId);
        }

        return $query->sum('jumlah_produk');
    }


    public static function countMonthlyProductsSold($productId)
    {
        return static::whereHas('order', function ($query) {
            $query->whereYear('tanggal_pesanan', now()->year)
                ->whereMonth('tanggal_pesanan', now()->month)
                ->where('status_pesanan', '3'); // Selesai
        })
            ->where('id_produk', $productId)
            ->sum('jumlah_produk');
    }

    public static function getProductsSold($productId, $tanggal_mulai = null, $tanggal_selesai = null)
    {
        $query = static::join('pesanan', 'pesanan.id', "=", 'detail_pesanan.id_pesanan')
            ->where('detail_pesanan.id_produk', $productId)
            ->where('pesanan.status_pesanan', '3') // Selesai
            ->selectRaw('DATE(pesanan.tanggal_pesanan) as tanggal, SUM(detail_pesanan.jumlah_produk) as terjual')
            ->groupBy('tanggal');

        if ($tanggal_mulai) {
            $tanggal_mulai = date('Y-m-d', strtotime($tanggal_mulai));
            $query->whereDate('pesanan.tanggal_pesanan', '>=', $tanggal_mulai);
        }

        if ($tanggal_selesai) {
            $tanggal_selesai = date('Y-m-d', strtotime($tanggal_selesai));
            $query->whereDate('pesanan.tanggal_pesanan', '<=', $tanggal_selesai);
        }

        return $query->get();
    }
}
