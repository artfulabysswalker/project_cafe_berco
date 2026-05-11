<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    //  VULNERABLE: Path Traversal
    public function downloadFile(Request $request)
    {
        $filename = $request->input('file');
        $path = storage_path('app/laporan/' . $filename);
        
        return response()->download($path);
    }
}