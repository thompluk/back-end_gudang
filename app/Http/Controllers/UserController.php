<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Http\Response;

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

    public function userSelect()
    {
        $currentUserId = auth()->id();

        // Mengambil semua pengguna kecuali pengguna yang sedang login
        $users = User::where('id', '!=', $currentUserId)->get();

        return response()->json([
            'success' => true,
            'message' => 'User data successfully retrieved!',
            'data' => $users
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
                'role' => 'required|string',
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
                'role' => 'required|string',
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

    public function upload(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv', // max file size 2MB
        ]);

        // Menggunakan PHPSpreadsheet untuk membaca file Excel
        $filePath = $request->file('file')->getRealPath();
        $spreadsheet = IOFactory::load($filePath);

        // Ambil data dari sheet pertama
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Ambil header dari baris pertama
        $headers = $sheetData[1]; // Mengambil header dari baris pertama

        if($headers['A'] !== 'Name' || $headers['B'] !== 'Email' || $headers['C'] !== 'Phone Number' || $headers['D'] !== 'Password' ||$headers['E'] !== 'Role' ){
            return response()->json([
                'success' => false,
                'message' => 'File format is not valid! Please use the template form.',
                'data'=> $headers,
            ], 404);
        }
        // Loop melalui data dan simpan ke database
        foreach ($sheetData as $rowIndex => $row) {
            if ($rowIndex == 1) continue; // Lewati header
        
            $name = $row['A'] ?? null;
            $email = $row['B'] ?? null;
            $phone_number = $row['C'] ?? null;
            $password = $row['D'] ?? null;
            $role = $row['E'] ?? null;
        
            if (empty($name) || empty($email) || empty($phone_number) || empty($password) || empty($role)) {
                // return response()->json(['message' => 'File uploaded successfully!','data' => $name]);
                Log::warning("Missing name or email or phone number or password or role at row $rowIndex");
                continue; // Lewati baris ini jika nama atau email kosong
            }

            User::create([
                'name' => $name,
                'email' => $email,
                'phone_number' => $phone_number,
                'password' => bcrypt($password),    
                'role' => $role,
            ]);

        }

        return response()->json(['message' => 'File uploaded successfully!']);
    }

    public function downloadTemplate()
    {
        // Buat objek spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set header untuk kolom
        $sheet->setCellValue('A1', 'Name'); // Kolom nama
        $sheet->setCellValue('B1', 'Email'); // Kolom email
        $sheet->setCellValue('C1', 'Phone Number'); // Kolom nomor telepon
        $sheet->setCellValue('D1', 'Password'); // Kolom password
        $sheet->setCellValue('E1', 'Role'); // Kolom role

        $sheet->setCellValue('A2', 'TestName'); // Kolom nama
        $sheet->setCellValue('B2', 'TestEmail'); // Kolom email
        $sheet->setCellValue('C2', '123456789'); // Kolom nomor telepon
        $sheet->setCellValue('D2', 'TestPassword'); // Kolom password
        $sheet->setCellValue('E2', 'ADMIN'); // Kolom role

        // Set format header untuk pengunduhan file
        $filename = 'templateUser.xlsx';
        
        // Buat penulis untuk file Excel
        $writer = new Xlsx($spreadsheet);

        // Bersihkan output buffer
        ob_end_clean();
        
        // Buat response untuk mengunduh file
        return response()->stream(function() use ($writer) {
            $writer->save('php://output');
        }, Response::HTTP_OK, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
