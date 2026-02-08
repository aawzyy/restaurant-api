<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    // API untuk mengambil semua daftar meja
    public function index()
    {
        $tables = Table::all();
        
        return response()->json([
            'status' => 'success',
            'data' => $tables
        ]);
    }
}