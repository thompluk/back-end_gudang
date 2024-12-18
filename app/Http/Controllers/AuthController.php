<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validationData = Validator::make(
                [
                    'email' => $request->email,
                    'password' => $request->password
                ],
                [
                    'email' => 'required',
                    'password' => 'required|string'
                ],
            );

            if ($validationData->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => "Email dan Password harus diisi!"
                ], 400);
            }

            $user = User::where('email', $request->email)->first();

            // if ((($page == 'admin') && ($user->role != 'KARYAWAN')) || (($page == 'user') && ($user->role != 'KONSUMEN'))){
            //     return response()->json([
            //         'success' => false,
            //         'message' => [
            //             'role' => ['Role tidak sesuai!']
            //         ],
            //         'data' => $user
            //     ], 400);
            // }

            if ($user != null) {
                $user_password = $user->password;

                if (Hash::check($request->password, $user_password)) {
                    $token = $user->createToken('auth-token')->plainTextToken;

                    // return response()->json([
                    //     'success' => true,
                    //     'message' => 'Berhasil login!',
                    //     'token_type' => 'Bearer',
                    //     'token' => $token,
                    //     'data' => $user
                    // ], 200);

                    return response(compact('user','token'));
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "Password tidak sesuai!"
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Email tidak terdaftar!"
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout()
    {
        $user = Auth::user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logout!'
        ], 200);
    }

    public function notAuthenticated()
    {
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 403);
    }

    public function ubahPassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'password_lama' => 'required',
            'password_baru' => 'required',
            'password_konfirmasi' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 400);
        }

        if ($request->password_baru != $request->password_konfirmasi) {
            return response()->json([
                'success' => false,
                'message' => 'Password konfirmasi tidak sesuai!'
            ], 400);
        }

        if (!Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai!'
            ], 400);
        }

        $user_update = User::find($user->id);

        $user_update->update([
            'password' => Hash::make($request->password_baru)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password successfully updated!'
        ], 200);
    }

    public function resetPassword()
    {
        $user = Auth::user();
        $user_update = User::find($user->id);
        $user_update->update([
            'password' => Hash::make('12345')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Password successfully updated!',
            'data' => $user_update,
        ], 200);
    }
}
