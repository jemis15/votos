<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    function index()
    {
        $users = User::all();

        return view('users.index', compact('users'));
    }

    function create()
    {
        return view('users.create');
    }

    function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'identification' => 'required|string|digits:8|unique:users,identification',
            'email' => 'nullable|string|max:100|unique:users,email'
        ]);

        $user = new User();
        $user->fill($request->only('name', 'identification', 'email'));
        $user->password = Hash::make($request->identification);
        $user->save();

        return redirect()->route('users')->with('success', 'Usuario creado.');
    }

    function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'identification' => 'required|string|digits:8|unique:users,identification,' . $user->id,
            'email' => 'nullable|string|max:100|unique:users,email,' . $user->id
        ]);

        $user->fill($request->only('name', 'identification', 'email'));
        $user->save();

        return redirect()->route('users')->with('success', 'Usuario actualizado.');
    }
    
    function delete(User $user) {
        return view('users.delete', compact('user'));
    }
    
    function destroy(User $user) {
        $user->delete();
        
        return redirect()->route('users')->with('success', 'Usuario eliminado.');
    }
    
    function resetPassword(User $user) {
        $user->password = Hash::make($user->identification);
        $user->save();
        
        return redirect()->route('users')->with('success', 'Contraseña restablecido.');
    }

    function import(Request $request) {
        $request->validate([
            'file_users' => 'required|file|mimetypes:text/plain,text/csv,application/csv,application/vnd.ms-excel'
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new UsersImport, $request->file_users);

        return redirect()->back()->with('success', 'Importado con exito !!');
    }
}
