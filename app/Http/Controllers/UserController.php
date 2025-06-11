<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Logic to retrieve and display users
        return view('users.index');
    }

    public function create()
    {
        // Logic to show user creation form
        return view('users.create');
    }

    public function store(Request $request)
    {
        // Logic to store a new user
        // Validate and save the user data
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        // Logic to show user edit form
        return view('users.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Logic to update the user data
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        // Logic to delete a user
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
