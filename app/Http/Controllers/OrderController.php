<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class OrderController extends Controller
{
    // public function index()
    // {
    //     $data = [
    //         'title'             => session('level') == 3 ? 'Pendapatan' : 'Penjualan',
    //         'order'             => Order::with(['customer', 'payment'])->orderByDesc('id')->get(),
    //         'product'           => Product::with(['category'])->orderByDesc('id')->get(),
    //         'customer'          => Customer::whereNotNull('kode')->orderBy('nama')->get(),
    //         'transaction'       => Order::getTransactionsByDate(),
    //         'tanggal_mulai'     => '',
    //         'tanggal_selesai'   => '',
    //     ];
    //     return view('order-index', $data);
    // }

    public function index(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', '');
        $tanggal_selesai = $request->input('tanggal_selesai', '');

        if ($request->isMethod('post')) {
            $transactionData = Order::getTransactionsByDate($tanggal_mulai, $tanggal_selesai);
        } else {
            $transactionData = Order::getTransactionsByDate();
        }

        $data = [
            'title'                 => session('level') == 3 ? 'Pendapatan' : 'Penjualan',
            'order'                 => Order::with(['customer', 'payment'])->orderByDesc('id')->get(),
            'product'               => Product::with(['category'])->orderByDesc('id')->get(),
            'customer'              => Customer::whereNotNull('kode')->orderBy('nama')->get(),
            'transaction'           => $transactionData['daily'],
            'jumlah_transaksi'      => $transactionData['totals']->jumlah_transaksi,
            'total_pendapatan'      => $transactionData['totals']->total_pendapatan,
            'tanggal_mulai'         => $tanggal_mulai,
            'tanggal_selesai'       => $tanggal_selesai,
        ];

        return view('order-index', $data);
    }

    public function edit($id)
    {
        $order = Order::find(base64_decode($id));

        if (empty($order->id)) {
            return redirect()->route('order')->with('error', 'Data penjualan tidak ditemukan.');
        }

        if ($order->status_pesanan == 3) {
            return redirect()->route('order')->with('error', 'Pesanan yang sudah selesai tidak dapat diedit.');
        }

        $data = [
            'title'         => 'Penjualan',
            'order'         => Order::with('customer')->orderByDesc('id')->get(),
            'product'       => Product::orderByDesc('id')->get(),
            'customer'      => Customer::orderBy('nama', 'asc')->get(),
            'data'          => array(
                'order'         => $order,
                'order_detail'  => OrderDetail::with('product')->where('id_pesanan', $order->id)->get()
            ),
        ];

        return view('order-index', $data);
    }

    public function show($id)
    {
        $order = Order::with('customer')->find(base64_decode($id));

        if (empty($order->id)) {
            return redirect()->route('order')->with('error', 'Data penjualan tidak ditemukan.');
        }

        $payment = OrderPayment::where('id_pesanan', $order->id)->first();

        if (empty($payment->id)) {
            return redirect()->route('order')->with('error', 'Pesanan ini belum dibayar.');
        }

        $data = [
            'title'         => 'Penjualan',
            'data'          => array(
                'order'         => $order,
                'order_detail'  => OrderDetail::with('product')->where('id_pesanan', $order->id)->get(),
                'payment'       => $payment,
                'bank'          => Bank::orderByDesc('id')->get(),
            ),
        ];

        return view('order-detail', $data);
    }

    public function destroy($id): RedirectResponse
    {
        $orderId = base64_decode($id);

        $order = Order::find($orderId);

        if (!$order) {
            return redirect()->route('order')->with('error', 'Data penjualan tidak ditemukan.');
        }

        $payment = OrderPayment::where('id_pesanan', $order->id)->first();

        if ($payment && $payment->bukti_pembayaran) {
            $filePath = public_path('upload_images') . '/' . $payment->bukti_pembayaran;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // if ($order->id_pelanggan) {
        //     $customer = Customer::find($order->id_pelanggan);
        //     if ($customer && $customer->kode == null) {
        //         $customer->delete();
        //     }
        // }

        $order->delete();

        return redirect()->route('order')->with('success', 'Data penjualan berhasil dihapus.');
    }

    public function update(Request $request): RedirectResponse
    {
        $id = base64_decode($request->id);
        $order = Order::find($id);

        if (empty($order)) {
            return redirect()->route('order')->with('error', 'Data penjualan tidak ditemukan.');
        }

        $payment = OrderPayment::where('id_pesanan', $order->id)->first();

        if (empty($payment)) {
            return redirect()->route('order')->with('error', 'Pesanan ini belum dibayar.');
        }

        $order->status_pesanan = 3;
        $order->save();

        return redirect()->route('order')->with('success', 'Konfirmasi pembayaran berhasil dilakukan.');
    }

    public function save(Request $request): JsonResponse
    {
        date_default_timezone_set('Asia/Jakarta');

        $validatedData = $request->validate([
            'id_produk' => 'required|array',
            'member' => 'required|in:1,2',
            'id_pelanggan' => ($request->input('member') == 1) ? 'required|exists:pelanggan,id' : 'nullable|exists:pelanggan,id',
            'nama_pelanggan' => ($request->input('member') == 2) ? 'required|string' : 'nullable|string',
            'telepon_pelanggan' => ($request->input('member') == 2 && $request->input('metode_pembayaran') == 2) ? 'required|string|numeric|min:10' : 'nullable|string|numeric|min:10',
            'status_pesanan' => 'required|in:1,2,3',
            'opsi_pengiriman' => 'required|in:1,2',
            'biaya_pengiriman' => 'nullable|numeric|min:0',
            'keterangan' => ($request->input('member') == 2 && $request->input('opsi_pengiriman') == 2) ? 'required|string|max:255' : 'nullable|string|max:255',
            'metode_pembayaran' => ($request->input('status_pesanan') != 1) ? 'required|in:1,2' : '',
            'total_pembayaran' => ($request->input('status_pesanan') != 1) ? 'required|numeric' : '',
            'jumlah_pembayaran' => ($request->input('status_pesanan') != 1 && $request->input('opsi_pengiriman') == 1) ? 'required|numeric|min:' . $request->input('total_pembayaran') : '',
        ]);

        $order = Order::find(base64_decode($request->id_pesanan)) ?? new Order([
            'no_transaksi' => date('Ymd') . str_pad(Order::whereDate('tanggal_pesanan', today())->count() + 1, 4, '0', STR_PAD_LEFT),
            'tanggal_pesanan' => now(),
            'id_kasir' => Auth::check() ? Auth::user()->id : null,
        ]);

        $biaya_pengiriman = $request->input('opsi_pengiriman') == 2 ? $request->input('biaya_pengiriman') : 0;

        $order->fill([
            'id_pelanggan' => $request->input('member') == 1 ? $request->input('id_pelanggan') : null,
            'nama_pelanggan' => $request->input('member') == 2 ? $request->input('nama_pelanggan') : null,
            'telepon_pelanggan' => $request->input('telepon_pelanggan'),
            'total_harga' => 0,
            'biaya_pengiriman' => $biaya_pengiriman,
            'status_pesanan' => $request->input('status_pesanan'),
            'keterangan' => $request->input('keterangan'),
        ])->save();

        $orderDetails = [];

        foreach ($request->input('id_produk') as $index => $productId) {
            $product = Product::find(base64_decode($productId));
            if ($product && $product->stok - OrderDetail::countProductsSold($product->id) >= 1) {
                $quantity = max(1, $request->input('qty_' . $productId, 1));
                $level = $request->input('level_' . $productId) ? $request->input('level_' . $productId) : null;

                $orderDetail = OrderDetail::updateOrCreate(
                    ['id_pesanan' => $order->id, 'id_produk' => $product->id],
                    [
                        'jumlah_produk' => $quantity,
                        'harga_satuan' => $product->harga * (100 - $product->diskon) / 100,
                        'level' => $level
                    ]
                );

                $orderDetails[] = $orderDetail;
                $order->total_harga += $orderDetail->harga_satuan * $quantity;
            }
        }

        $order->orderDetails()->whereNotIn('id', collect($orderDetails)->pluck('id')->toArray())->delete();
        $order->save();

        if ($request->input('status_pesanan') != 1 || $request->input('metode_pembayaran') == 2) {
            OrderPayment::updateOrCreate(
                ['id_pesanan' => $order->id],
                [
                    'metode_pembayaran' => $request->input('metode_pembayaran'),
                    'total_pembayaran' => $order->total_harga + $biaya_pengiriman,
                    'tanggal_pembayaran' => now(),
                    'jumlah_pembayaran' => $request->input('jumlah_pembayaran'),
                    'jumlah_kembalian' => $request->input('metode_pembayaran') == 1 ? $request->input('jumlah_pembayaran') - ($order->total_harga + $biaya_pengiriman) : null,
                ]
            );
        }

        if (Auth::check()) {
            $data = [
                'success' => true,
                'message' => ($request->input('status_pesanan') != 1) ? 'Pembayaran berhasil dilakukan dan selesai.' : 'Produk berhasil disimpan ke keranjang.'
            ];

            if ($order->wasRecentlyCreated) {
                $data['previous'] = true;
            }
        } else {
            $data = [
                'success'       => true,
                'message'       => 'Pesanan telah berhasil dibuat. Silakan segera lakukan pembayaran.',
                'url'           => route('home.show', base64_encode($order->id))
            ];

            Session::flash('success_message', $data['message']);
        }

        return response()->json($data, 200);
    }

    public function invoice($id)
    {
        $order = Order::with('customer')->find(base64_decode($id));

        if (empty($order->id)) {
            return redirect()->route('order')->with('error', 'Data penjualan tidak ditemukan.');
        }

        $payment = OrderPayment::where('id_pesanan', $order->id)->first();

        if (empty($payment->id)) {
            return redirect()->route('order')->with('error', 'Pesanan ini belum dibayar.');
        }

        $data = [
            'title'         => 'Penjualan',
            'data'          => array(
                'order'         => $order,
                'order_detail'  => OrderDetail::with('product')->where('id_pesanan', $order->id)->get(),
                'payment'       => $payment
            ),
        ];

        return view('order-invoice', $data);
    }
}
