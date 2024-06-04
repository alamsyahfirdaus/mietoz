<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $data = array(
            'title'     => 'Kategori',
            'category'  => Category::orderBy('nama')->get(),
        );
        return view('category-index', $data);
    }

    public function edit($id)
    {
        $category = Category::find(base64_decode($id));

        if (empty($category->id)) {
            return redirect()->route('category')->with('error', 'Data kategori tidak ditemukan.');
        }

        $data = [
            'title'         => 'Kategori',
            'category'       => Category::orderBy('nama')->get(),
            'data'          => $category,
        ];

        return view('category-index', $data);
    }

    public function save(Request $request, $id = null): JsonResponse
    {
        $category = Category::find(base64_decode($id));
        if (!$category) {
            $category = new Category();
        }

        $validatedData = $request->validate([
            'nama' => [
                'required', 'string', 'max:255',
                isset($category->id) ? Rule::unique('kategori', 'nama')->ignore($category->id) : 'unique:kategori,nama'
            ],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $category->nama = ucwords(strtolower($validatedData['nama']));
        $category->deskripsi = $validatedData['deskripsi'];

        $category->save();

        $data = array(
            'success' => true,
            'message' => 'Data kategori berhasil disimpan.'
        );

        if ($category->wasRecentlyCreated) {
            $data['previous'] = true;
        }

        return response()->json($data, 200);
    }

    public function destroy($id): RedirectResponse
    {
        $category = Category::find(base64_decode($id));

        if ($category) {

            $category->delete();

            return redirect()->route('category')->with('success', 'Data kategori berhasil dihapus.');
        } else {
            return redirect()->route('category')->with('error', 'Data kategori tidak ditemukan.');
        }
    }
}
