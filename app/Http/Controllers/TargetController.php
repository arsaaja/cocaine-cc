<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TargetController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'target_amount' => 'required|integer|min:1',
            'target_title' => 'nullable|string|max:255'
        ]);

        // Menggunakan DB::transaction untuk menjamin konsistensi data
        DB::transaction(function () use ($request) {
            $user = Auth::user();
            $user->target_title = $request->target_title ?? 'Rencana Saya';
            $user->target_amount = $request->target_amount;
            $user->save();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Target tabungan berhasil disimpan permanen.'
        ]);
    }

    public function clear()
    {
        DB::transaction(function () {
            $user = Auth::user();
            $user->target_title = null;
            $user->target_amount = null;
            $user->save();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Target tabungan berhasil dihapus.'
        ]);
    }
}
