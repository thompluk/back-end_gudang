<?php

namespace App\Http\Controllers;

use App\Models\Companies;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    public function index()
    {
        $companies = Companies::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'All companies successfully retrieved!',
            'data' => $companies
        ], 200);
    }

    public function show($id)
    {
        $companies = Companies::find($id);

        if ($companies == null) {
            return response()->json([
                'success' => false,
                'message' => 'companies not found!',
                'data' => $companies
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'companies retrieved successfully!',
                'data' => $companies
            ], 200);
        }
    } 

    public function create(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'address' => 'required',
                'telephone' => 'required',
                'fax' => 'required',
                'email' => 'required',
            ],
            [
                'name.required' => 'Nama Companies wajib diisi!',
                'address.required' => 'Alamat wajib diisi!',
                'telephone.required' => 'Telephone wajib diisi!',
                'fax.required' => 'Fax wajib diisi!',
                'email.required' => 'Email wajib diisi!',
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()
            ], 400);
        }

        $companies = Companies::create([
            'name' => $request->name,
            'address' => $request->address,
            'telephone' => $request->telephone,
            'fax' => $request->fax,
            'email' => $request->email
        ]);

        return response()->json([
            'success' => true,
            'message' => 'companies successfully created!',
            'data' => $companies
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'address' => 'required',
                'telephone' => 'required',
                'fax' => 'required',
                'email' => 'required',
            ],
            [
                'name.required' => 'Nama Companies wajib diisi!',
                'address.required' => 'Alamat wajib diisi!',
                'telephone.required' => 'Telephone wajib diisi!',
                'fax.required' => 'Fax wajib diisi!',
                'email.required' => 'Email wajib diisi!',
            ]
        );

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validation->errors()
            ], 400);
        }

        $companies = Companies::find($id);

        if ($companies == null) {
            return response()->json([
                'success' => false,
                'message' => 'companies not found!',
                'data' => $companies
            ], 404);
        } else {
            $companies->update([
                'name' => $request->name,
                'address' => $request->address,
                'telephone' => $request->telephone,
                'fax' => $request->fax,
                'email' => $request->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Companies updated successfully!',
                'data' => $companies
            ], 200);
        }
    }

    public function destroy($id)
    {
        $companies = Companies::find($id);

        if ($companies == null) {
            return response()->json([
                'success' => false,
                'message' => 'companies not found!',
                'data' => $companies
            ], 404);
        } else {
            $companies->delete();

            return response()->json([
                'success' => true,
                'message' => 'companies deleted successfully!',
            ], 200);
        }
    }
}
