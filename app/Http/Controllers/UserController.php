<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $judul = 'Pengguna';
        $data = User::all();
        return view('dashboard.users.index', compact('judul', 'data'));
    }

    public function simpan(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => ['required', Rule::in(['admin', 'user'])],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('user')->with('berhasil', 'User berhasil ditambahkan.');
    }

    public function ubah(Request $request)
    {
        $user = User::findOrFail($request->id);
        return response()->json($user);
    }

    public function perbarui(Request $request)
    {
        $user = User::findOrFail($request->id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role'     => ['required', Rule::in(['admin', 'user'])],
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('user')->with('berhasil', 'User berhasil diperbarui.');
    }

    public function hapus(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->delete();

        return response()->json(['success' => true]);
    }
}