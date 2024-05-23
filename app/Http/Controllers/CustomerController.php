<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        $data = array(
            'title'      => 'Pelanggan',
            'customer'   => Customer::whereNotNull('kode')->orderByDesc('id')->get()
        );
        return view('customer-index', $data);
    }

    public function edit($id)
    {
        $customer = Customer::find(base64_decode($id));

        if (empty($customer->id)) {
            return redirect()->route('customer')->with('error', 'Data pelanggan tidak ditemukan.');
        }

        $data = array(
            'title'      => 'Pelanggan',
            'customer'   => Customer::orderByDesc('id')->get(),
            'data'       => $customer,
        );

        return view('customer-index', $data);
    }

    public function save(Request $request, $id = null): JsonResponse
    {
        $customer = Customer::find(base64_decode($id));

        $validatedData = $request->validate([
            'nama' => [
                'required', 'string', 'max:255',
            ],
            'telepon' => [
                'required', 'string', 'numeric', 'min:10',
                isset($customer->id) ? Rule::unique('pelanggan', 'telepon')->ignore($customer->id) : 'unique:pelanggan,telepon'
            ],
            'alamat' => ['nullable', 'string'],
        ]);

        if (!$customer) {
            $customer = new Customer();
            $customer->kode = $this->generateCustomerCode();
        }

        $customer->nama = ucwords(strtolower($validatedData['nama']));
        $customer->telepon = $validatedData['telepon'];
        $customer->alamat = $validatedData['alamat'];
        $customer->save();

        $data = array(
            'success' => true,
            'message' => 'Data pelanggan berhasil disimpan.'
        );

        if ($customer->wasRecentlyCreated) {
            $data['previous'] = true;
        }

        return response()->json($data, 200);
    }

    public function destroy($id): RedirectResponse
    {
        $customer = Customer::find(base64_decode($id));

        if (!$customer) {
            return redirect()->route('customer')->with('error', 'Data pelanggan tidak ditemukan.');
        }

        $customer->delete();

        return redirect()->route('customer')->with('success', 'Data pelanggan berhasil dihapus.');
    }

    private function generateCustomerCode()
    {
        $lastCustomer = Customer::orderBy('id', 'desc')->first();
        $lastCode = $lastCustomer ? (int) substr($lastCustomer->kode, 1) : 0;
        $newCode = str_pad($lastCode + 1, 8, '0', STR_PAD_LEFT);
        return $newCode;
    }
}
