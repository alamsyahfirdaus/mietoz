<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\Customer;
use App\Models\User;
use App\Models\Bank;
use App\Models\Carousel;
use App\Models\Chat;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $data = array(
            'title'     => 'Beranda',
            'order'     => Order::getOrdersOnline(),
            'transaksi' => Order::getSalesByMonth(),
            'chats'     => Chat::latestMessages(100, null),
        );

        if (session('role') == 1) {
            return view('home', $data);
        } else {
            echo '404 Page Not Found';
        }
    }

    public function setting()
    {
        $data = [
            'title'     => 'Pengaturan',
            'users'     => User::orderByDesc('id')->whereNotIn('id', [1])->get(),
            'bank'      => Bank::orderByDesc('id')->get(),
            'carousel'  => Carousel::orderByDesc('id')->take(5)->get()

        ];
        return view('user-index', $data);
    }

    public function saveBank(Request $request): JsonResponse
    {
        $bank = Bank::find(base64_decode($request->id));

        $validatedData = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nomor_rekening' => [
                'required', 'string', 'numeric', 'min:0',
                isset($bank->id) ? Rule::unique('bank', 'nomor_rekening')->ignore($bank->id) : 'unique:bank,nomor_rekening'
            ]
        ]);

        if (!$bank) {
            $bank = new Bank();
        }

        $bank->nama = $validatedData['nama'];
        $bank->nomor_rekening = $validatedData['nomor_rekening'];
        $bank->save();

        $data = [
            'success' => true,
            'message' => 'Nomor rekening berhasil disimpan.',
            'previous' => true
        ];

        return response()->json($data, 200);
    }

    public function deleteBank($id): RedirectResponse
    {
        $bank = Bank::find(base64_decode($id));

        if (!$bank) {
            return redirect()->route('home.setting')->with('error', 'Nomor rekening tidak ditemukan.');
        }

        $bank->delete();

        return redirect()->route('home.setting')->with('success', 'Nomor rekening berhasil dihapus.');
    }

    public function saveCarousel(Request $request): JsonResponse
    {
        $carousel = Carousel::find(base64_decode($request->id));
        if (!$carousel) {
            $carousel = new Carousel();
        }

        $validatedData = $request->validate([
            'judul'     => ['nullable', 'string', 'max:255'],
            'gambar'    => [empty($carousel->id) ? 'required' : 'nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $carousel->judul = $validatedData['judul'];
        if ($request->hasFile('gambar')) {
            $fileGambar = $request->file('gambar');
            $namaGambar = time() . '.' . $fileGambar->getClientOriginalExtension();
            $fileGambar->move(config('constants.UPLOAD_PATH'), $namaGambar);
            $carousel->gambar = $namaGambar;
        }
        $carousel->save();

        $data = [
            'success' => true,
            'message' => 'Gambar banner berhasil disimpan.',
            'previous' => true
        ];

        return response()->json($data, 200);
    }

    public function deleteCarousel($id): RedirectResponse
    {
        $carousel = Carousel::find(base64_decode($id));

        if ($carousel) {
            if ($carousel->gambar) {
                $pathGambar = config('constants.UPLOAD_PATH') . '/' . $carousel->gambar;
                if (File::exists($pathGambar)) {
                    File::delete($pathGambar);
                }
            }

            $carousel->delete();

            return redirect()->route('home.setting')->with('success', 'Gambar banner berhasil dihapus.');
        } else {
            return redirect()->route('home.setting')->with('error', 'Gambar banner tidak ditemukan.');
        }
    }

    public function shop()
    {
        $data = array(
            'title'     => '',
            'carousel'  => Carousel::all(),
            'product'   => Product::with(['category'])->orderByDesc('id')->get(),
        );

        return view('shop', $data);
    }

    public function payOrder($id)
    {
        $order = Order::find(base64_decode($id));

        if (empty($order->id)) {
            return redirect()->route('home.shop')->with('error', 'Data pesanan tidak ditemukan.');
        }

        $payment = OrderPayment::where('id_pesanan', $order->id)->first();

        if (empty($payment->id)) {
            return redirect()->route('order')->with('error', 'Pesanan ini belum dibayar.');
        }

        $data = [
            'title'         => 'Pesanan',
            'data'          => array(
                'order'         => $order,
                'order_detail'  => OrderDetail::with('product')->where('id_pesanan', $order->id)->get(),
                'payment'       => $payment,
                'bank'          => Bank::orderByDesc('id')->get(),
            ),
        ];

        return view('order-detail', $data);
    }

    public function confirmPayment(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'no_transaksi' => ['required', 'string', 'max:255', 'exists:pesanan,no_transaksi'],
            'bukti_pembayaran' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $order = Order::where('no_transaksi', $request->no_transaksi)->first();
        $payment = OrderPayment::where('id_pesanan', $order->id)->first();

        if (!$payment->bukti_pembayaran && $request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $fileName = date('YmdHis') . '-' . $order->id . '.' . $file->getClientOriginalExtension();
            $file->move(config('constants.UPLOAD_PATH'), $fileName);

            OrderPayment::updateOrCreate(
                ['id_pesanan' => $order->id],
                ['bukti_pembayaran' => $fileName]
            );

            $message = 'Bukti pembayaran berhasil terkirim dan sedang dalam proses verifikasi.';
        } elseif ($order->status_pesanan == 3) {
            $message = 'Pesanan telah selesai dan pembayarannya sudah diverifikasi.';
        } else {
            $message = 'Pesanan sedang diproses dan tunggu konfirmasi selanjutnya.';
        }

        $data = [
            'success' => true,
            'message' => $message,
            'previous' => true
        ];

        return response()->json($data, 200);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'no_transaksi' => ['required', 'string', 'max:255', 'exists:pesanan,no_transaksi'],
        ]);

        $order = Order::where('no_transaksi', $validatedData['no_transaksi'])->firstOrFail();

        $data = [
            'success' => true,
            'message' => 'Pesan berhasil dikirim.',
            'url'     => route('home.chat', base64_encode($order->id)),
        ];

        return response()->json($data, 200);
    }

    public function chatList($id)
    {
        $orderId = base64_decode($id);
        $order = Order::find($orderId);

        if (empty($order)) {
            return redirect()->route('home.shop')->with('error', 'Data pesanan tidak ditemukan.');
        }

        if (request()->isMethod('post')) {

            date_default_timezone_set('Asia/Jakarta');

            $validatedData = request()->validate([
                'message' => 'required|string|max:255',
            ]);

            $chat = new Chat();
            $chat->message = $validatedData['message'];
            $chat->id_pesanan = $orderId;
            $chat->id_kasir = Auth::check() ? auth()->user()->id : null;
            $chat->tanggal = now();
            $chat->save();

            return redirect()->route('home.chat', ['id' => $id])->with('success', 'Pesan berhasil dikirim.');
        } else {
            $chatList = [];
            foreach (Chat::with(['order.customer', 'user'])->where('id_pesanan', $orderId)->get() as $key => $item) {
                if ($item->id_kasir && !$item->is_read) {
                    $item->is_read = true;
                    $item->save();
                } else {
                    if ((!$item->is_read && $item->id_kasir) || (Auth::check() && !$item->id_kasir && !$item->is_read)) {
                        $item->is_read = true;
                        $item->save();
                    }
                }

                $chatList[] = array(
                    'id'                => $item->id,
                    'message'           => $item->message,
                    'id_pesanan'        => $item->id_pesanan,
                    'nama_pelanggan'    => $item->order->id_pelanggan ? $item->order->customer->nama : $item->order->nama_pelanggan,
                    'id_kasir'          => $item->id_kasir,
                    'nama_kasir'        => $item->user ? $item->user->name : null,
                    'tanggal'           => $item->tanggal,
                );
            }

            $data = [
                'title'         => Auth::check() ? 'Chat Pelanggan' : 'Chat',
                'id_pesanan'    => $orderId,
                'chat'          => $chatList
            ];

            return view('order-chat', $data);
        }
    }
}
