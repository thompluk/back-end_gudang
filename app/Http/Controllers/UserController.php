<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index()
    {
        $user = User::all();

        return response()->json([
            'success' => true,
            'message' => 'User data successfully retrieved!',
            'data' => $user
        ], 200);
    }

    public function getLoggedUser()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'message' => 'User logged in data successfully retrieved!',
            'data' => $user
        ], 200);
    }
   

    public function show($id)
    {
        $user = User::find($id);

        if ($user == null) {
            return response()->json([
                'success' => false,
                'message' => 'User not found!'
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'User data successfully retrieved!',
                'data' => $user
            ]);
        }
    }

    public function createuser(Request $request){
        $validateData = Validator::make(
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'phone_number' => $request->phone_number,
                'role' => $request->role
            ],
            [
                'name' => 'required|string',
                'email' => 'required|string|email',
                'password' => 'required|string',
                'phone_number' => 'required|numeric|starts_with:0|digits_between:11,14',
                'role' => 'required|string|in:ADMIN,SALES',
            ],
            [
                'name.required' => 'Nama wajib diisi!',
                'email.required' => 'Email wajib diisi!',
                'email.email' => 'Format email tidak valid!',
                'password.required' => 'Password wajib diisi!',
                'phone_number.required' => 'Nomor Telepon wajib diisi!',
                'phone_number.starts_with' => 'Nomor Telepon wajib dimulai dengan angka 0!',
                'phone_number.digits_between' => 'Nomor Telepon memiliki min. 11 dan maks. 14 digit!',
                'role.required' => 'Role harus diisi!',
            ]
        );

        if ($validateData->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validateData->errors()
            ], 400);
        }

        $new_user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,   
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User successfully registered',
            'data' => $new_user
        ], 200);
   
    }

    public function update(Request $request, $id){

        $user = User::find($id);

        if ($user == null) {
            return response()->json([
                'success' => false,
                'message' => 'User not found!',
            ], 404);
        }

        $validateData = Validator::make(
            [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'role' => $request->role
            ],
            [
                'name' => 'required|string',
                'email' => 'required|string|email',
                'phone_number' => 'required|numeric|starts_with:0|digits_between:11,14',
                'role' => 'required|string|in:ADMIN,SALES',
            ],
            [
                'name.required' => 'Nama wajib diisi!',
                'email.required' => 'Email wajib diisi!',
                'email.email' => 'Format email tidak valid!',
                'phone_num.required' => 'Nomor Telepon wajib diisi!',
                'phone_num.starts_with' => 'Nomor Telepon wajib dimulai dengan angka 0!',
                'phone_num.digits_between' => 'Nomor Telepon memiliki min. 11 dan maks. 14 digit!',
                'role.required' => 'Role harus diisi!',
            ]
        );

        if ($validateData->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validateData->errors()
            ], 400);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User successfully edited',
            'data' => $user
        ], 200);
   
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if ($user == null) {
            return response()->json([
                'success' => false,
                'message' => 'User not found!',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User with ID ' . $id . ' successfully deleted!',
        ]);
    }
}
