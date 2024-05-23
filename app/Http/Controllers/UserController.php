<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title' => 'Pengguna',
            'users' => User::orderByDesc('id')->get()
        ];

        // return view('user-index', $data);
        return redirect('home.setting');

    }

    public function create()
    {
        $data = array(
            'title' => 'Pengguna',
        );

        // return view('user-add-edit', $data);

        return redirect('home.setting');
    }

    public function edit($id)
    {
        $user = User::find(base64_decode($id));

        if (empty($user->id)) {
            return redirect()->route('home.setting')->with('error', 'Data kasir tidak ditemukan.');
        }

        $data = [
            'title'     => 'Pengaturan',
            'users'     => User::orderByDesc('id')->get(),
            'data'      => $user
        ];

        return view('user-index', $data);
    }

    public function save(Request $request, $id = null): JsonResponse
    {
        $user = User::find(base64_decode($id));
        if (!$user) {
            $user = new User();
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', isset($user->id) ? Rule::unique('users', 'email')->ignore($user->id) : 'unique:users,email'],
            'username' => isset($user->id) ? ['required', Rule::unique('users', 'username')->ignore($user->id)] : ['nullable'],
            'telephone' => ['required', 'string', 'min:10', isset($user->id) ? Rule::unique('users', 'telephone')->ignore($user->id) : 'unique:users,telephone'],
            'password' => [empty($user->id) ? 'required' : 'nullable', 'string', empty($user->id) ? 'min:8' : 'sometimes|min:8'],
            'profile' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        if ($user->exists) {
            $user->username = $request->input('username');
        } else {
            $user->username = strstr($validatedData['email'], '@', true);
        }

        if (isset($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }
        $user->telephone = $validatedData['telephone'];

        if ($request->hasFile('profile')) {
            $profileImage = $request->file('profile');
            $imageName = time() . '.' . $profileImage->getClientOriginalExtension();
            $profileImage->move(public_path('profile_images'), $imageName);
            $user->profile = $imageName;
        }

        $user->save();

        $data = array(
            'success' => true,
            'message' => 'Data kasir berhasil disimpan.'
        );

        if ($user->wasRecentlyCreated) {
            $data['previous'] = true;
        }

        return response()->json($data, 200);
    }

    public function destroy($id): RedirectResponse
    {
        $user = User::find(base64_decode($id));

        if ($user) {
            if ($user->profile) {
                $profilePath = public_path('profile_images/' . $user->profile);
                if (File::exists($profilePath)) {
                    File::delete($profilePath);
                }
            }

            $user->delete();

            return redirect()->route('home.setting')->with('success', 'Data kasir berhasil dihapus.');
        } else {
            return redirect()->route('home.setting')->with('error', 'Data kasir tidak ditemukan.');
        }
    }
}
