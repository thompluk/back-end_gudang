<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Prinsipal;

class PrinsipalController extends Controller
{

    public function index()
    {
        $prinsipal = Prinsipal::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'All prinsipal successfully retrieved!',
            'data' => $prinsipal
        ], 200);
    }

    public function show($id)
    {
        $prinsipal = Prinsipal::find($id);

        if ($prinsipal == null) {
            return response()->json([
                'success' => false,
                'message' => 'prinsipal not found!',
                'data' => $prinsipal
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'prinsipal retrieved successfully!',
                'data' => $prinsipal
            ], 200);
        }
    } 

    public function create(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'telephone' => 'required',
                'fax' => 'required',
            ],
            [
                'name.required' => 'Nama Prinsipal wajib diisi!',
                'telephone.required' => 'Telepon wajib diisi!',
                'fax.required' => 'Fax wajib diisi!',
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()
            ], 400);
        }

        $prinsipal = Prinsipal::create([
            'name' => $request->name,
            'telephone' => $request->telephone,
            'fax' => $request->fax,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Prinsipal successfully created!',
            'data' => $prinsipal
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'telephone' => 'required',
                'fax' => 'required',
            ],
            [
                'name.required' => 'Nama Prinsipal wajib diisi!',
                'telephone.unique' => 'Telepon wajib diisi!',
                'fax.required' => 'Fax wajib diisi!',
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()
            ], 400);
        }

        $prinsipal = Prinsipal::find($id);

        if ($prinsipal == null) {
            return response()->json([
                'success' => false,
                'message' => 'Prinsipal not found!',
                'data' => $prinsipal
            ], 404);
        } else {
            $prinsipal->update([
                'name' => $request->name,
                'telephone' => $request->telephone,
                'fax' => $request->fax,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prinsipal updated successfully!',
                'data' => $prinsipal
            ], 200);
        }
    }

    public function destroy($id)
    {
        $prinsipal = Prinsipal::find($id);

        if ($prinsipal == null) {
            return response()->json([
                'success' => false,
                'message' => 'Prinsipal not found!',
                'data' => $prinsipal
            ], 404);
        } else {
            $prinsipal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Prinsipal deleted successfully!',
            ], 200);
        }
    }
}
