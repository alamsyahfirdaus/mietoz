<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $table = 'pesanan';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'no_transaksi',
        'id_pelanggan',
        'nama_pelanggan',
        'telepon_pelanggan',
        'tanggal_pesanan',
        'total_harga',
        'biaya_pengiriman',
        'status_pesanan',
        'keterangan',
        'id_kasir'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_pelanggan');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'id_pesanan');
    }

    public function payment()
    {
        return $this->hasOne(OrderPayment::class, 'id_pesanan', 'id');
    }

    public static function countOrdersOnline()
    {
        return self::where('status_pesanan', 1)
            ->whereHas('payment', function ($query) {
                $query->where('metode_pembayaran', 2);
            })
            ->count();
    }

    public static function getOrdersOnline()
    {
        return static::with(['customer', 'payment'])
            ->where('status_pesanan', 1)
            ->whereHas('payment', function ($query) {
                $query->where('metode_pembayaran', 2);
            })
            ->orderByDesc('id')
            ->get();
    }

    public static function getSalesByMonth(): array
    {
        $currentYear = date('Y');

        $salesData = static::select(
            DB::raw('MONTH(tanggal_pesanan) as bulan'),
            DB::raw('COUNT(id) as jumlah_transaksi'),
            DB::raw('SUM(total_harga) as total_pendapatan')
        )
            ->whereYear('tanggal_pesanan', $currentYear)
            ->where('status_pesanan', 3)
            ->groupBy(DB::raw('MONTH(tanggal_pesanan)'))
            ->get()
            ->keyBy('bulan');

        $formattedData = array_fill(1, 12, ['jumlah_transaksi' => 0, 'total_pendapatan' => 0]);

        foreach ($salesData as $month => $data) {
            $formattedData[$month] = [
                'jumlah_transaksi' => $data->jumlah_transaksi,
                'total_pendapatan' => $data->total_pendapatan
            ];
        }

        return $formattedData;
    }

    public static function getTransactionsByDate($tanggal_mulai = null, $tanggal_selesai = null)
    {
        $query = static::select(
            DB::raw('DATE(tanggal_pesanan) as tanggal_pesanan'),
            DB::raw('COUNT(id) as jumlah_transaksi'),
            DB::raw('SUM(total_harga) as total_pendapatan')
        )
            ->where('status_pesanan', 3)
            ->groupBy(DB::raw('DATE(tanggal_pesanan)'));

        if ($tanggal_mulai) {
            $tanggal_mulai = date('Y-m-d', strtotime($tanggal_mulai));
            $query->whereDate('pesanan.tanggal_pesanan', '>=', $tanggal_mulai);
        }

        if ($tanggal_selesai) {
            $tanggal_selesai = date('Y-m-d', strtotime($tanggal_selesai));
            $query->whereDate('pesanan.tanggal_pesanan', '<=', $tanggal_selesai);
        }

        $dailyTransactions = $query->get();

        $totals = static::select(
            DB::raw('COUNT(id) as jumlah_transaksi'),
            DB::raw('SUM(total_harga) as total_pendapatan')
        )
            ->where('status_pesanan', 3);

        if ($tanggal_mulai) {
            $totals->whereDate('pesanan.tanggal_pesanan', '>=', $tanggal_mulai);
        }

        if ($tanggal_selesai) {
            $totals->whereDate('pesanan.tanggal_pesanan', '<=', $tanggal_selesai);
        }

        $totals = $totals->first();

        return [
            'daily' => $dailyTransactions,
            'totals' => $totals
        ];
    }

    public static function getOrderDates($orderBy = 'asc')
    {
        $query = static::select(
            DB::raw('DATE(tanggal_pesanan) as tanggal')
        )
            ->where('status_pesanan', 3)
            ->groupBy(DB::raw('DATE(tanggal_pesanan)'));

        if ($orderBy === 'desc') {
            $query->orderByDesc('tanggal_pesanan');
        } else {
            $query->orderBy('tanggal_pesanan');
        }

        return $query->get();
    }
}
