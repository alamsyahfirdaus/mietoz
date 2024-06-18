<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chats';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function order()
    {
        return $this->belongsTo(Order::class, 'id_pesanan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_kasir');
    }

    public static function latestMessages1($orderId = null, $limit = 2)
    {
        $query = static::whereNull('id_kasir')
            ->where('is_read', false)
            ->orderByDesc('id')
            ->limit($limit)
            ->with(['order', 'order.customer']);

        if ($orderId !== null) {
            $query->where('id_pesanan', $orderId);
        }

        return $query->get();
    }

    public static function latestMessages($limit = 2, $orderId = null)
    {
        $subquery = DB::table('chats')
            ->selectRaw('id_pesanan, MAX(id) AS max_id')
            ->whereNull('id_kasir')
            // ->where('is_read', false)
            ->groupBy('id_pesanan');

        $query = Chat::query()
            ->joinSub($subquery, 'latest_chats', function ($join) {
                $join->on('chats.id_pesanan', "=", 'latest_chats.id_pesanan');
                $join->on('chats.id', "=", 'latest_chats.max_id');
            })
            ->orderByDesc('chats.id');

        if ($orderId !== null) {
            $query->where('chats.id_pesanan', $orderId);
        }

        return $query->with(['order', 'order.customer'])->limit($limit)->get();
    }



    public static function countUnreadMessages($orderId = null)
    {
        $query = static::whereNull('id_kasir')
            ->where('is_read', false);

        if ($orderId !== null) {
            $query->where('id_pesanan', $orderId);
        }

        return $query->count();
    }
}
