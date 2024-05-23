<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index()
    {
        $data = array(
            'title'     => 'Produk',
            'product'   => Product::orderByDesc('id')->get(),
            'kode'      => $this->_nextKode(),
        );
        return view('product-index', $data);
    }

    public function edit($id)
    {
        $product = Product::find(base64_decode($id));

        if (empty($product->id)) {
            return redirect()->route('product')->with('error', 'Data produk tidak ditemukan.');
        }

        $data = [
            'title'         => 'Produk',
            'product'       => Product::orderByDesc('id')->get(),
            'kode'          => isset($product->kode) ? $product->kode : $this->_nextKode(),
            'data'          => $product,
            'terjual'       => OrderDetail::countProductsSold($product->id)
        ];

        return view('product-index', $data);
    }

    private function _nextKode()
    {
        $latestProduct = Product::count();
        $nextKode = sprintf('%08d', $latestProduct + 1);

        while (Product::where('kode', $nextKode)->exists()) {
            $latestProduct++;
            $nextKode = sprintf('%08d', $latestProduct + 1);
        }

        return $nextKode;
    }

    public function save(Request $request, $id = null): JsonResponse
    {
        $product = Product::find(base64_decode($id));
        if (!$product) {
            $product = new Product();
        }

        $validatedData = $request->validate([
            'kode' => [
                'required', 'string', 'max:255',
                isset($product->id) ? Rule::unique('produk', 'kode')->ignore($product->id) : 'unique:produk,kode'
            ],
            'nama' => [
                'required', 'string', 'max:255',
                isset($product->id) ? Rule::unique('produk', 'nama')->ignore($product->id) : 'unique:produk,nama'
            ],
            'stok' => ['required', 'integer', 'min:0'],
            'harga' => ['required', 'numeric', 'min:1'],
            'diskon' => ['nullable', 'integer', 'min:1', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $product->kode = $validatedData['kode'];
        $product->nama = ucwords(strtolower($validatedData['nama']));
        $product->stok = isset($product->id) ? $product->stok + $validatedData['stok'] : $validatedData['stok'];
        $product->harga = $validatedData['harga'];
        $product->diskon = $validatedData['diskon'];
        $product->deskripsi = $validatedData['deskripsi'];

        if ($request->hasFile('gambar')) {
            $gambarProduk = $request->file('gambar');
            $namaGambar = time() . '.' . $gambarProduk->getClientOriginalExtension();
            $gambarProduk->move(public_path('upload_images'), $namaGambar);
            $product->gambar = $namaGambar;
        } else if (!$request->hasFile('gambar') && $request->input('delete_image') == '1') {
            if ($product->gambar) {
                $gambarPath = public_path('upload_images/' . $product->gambar);
                if (File::exists($gambarPath)) {
                    File::delete($gambarPath);
                }
                $product->gambar = null;
            }
        }

        $product->save();

        $data = array(
            'success' => true,
            'message' => 'Data produk berhasil disimpan.'
        );

        if ($product->wasRecentlyCreated) {
            $data['previous'] = true;
        }

        return response()->json($data, 200);
    }

    public function destroy($id): RedirectResponse
    {
        // Temukan produk berdasarkan ID
        $product = Product::find(base64_decode($id));

        // Periksa apakah produk ditemukan
        if ($product) {
            // Hapus gambar terkait jika ada
            if ($product->gambar) {
                $gambarPath = public_path('upload_images/' . $product->gambar);
                if (File::exists($gambarPath)) {
                    File::delete($gambarPath);
                }
            }

            // Hapus produk
            $product->delete();

            // Redirect ke halaman yang sesuai dengan pesan sukses
            return redirect()->route('product')->with('success', 'Data produk berhasil dihapus.');
        } else {
            // Jika produk tidak ditemukan, redirect dengan pesan kesalahan
            return redirect()->route('product')->with('error', 'Data produk tidak ditemukan.');
        }
    }
}
