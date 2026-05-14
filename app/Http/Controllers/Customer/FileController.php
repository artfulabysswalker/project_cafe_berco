<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function downloadFile(Request $request)
    {
        $filename = basename($request->input('file'));

        $allowed = ['pdf'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            abort(403, 'Tipe file tidak diizinkan.');
        }

        $path = storage_path('app/laporan/' . $filename);
        $realPath = realpath($path);
        $allowedDir = realpath(storage_path('app/laporan'));

        if (!$realPath || !str_starts_with($realPath, $allowedDir)) {
            abort(403, 'Akses ditolak.');
        }

        if (!file_exists($realPath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->download($realPath);
    }
}