<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IngestController extends Controller
{
    // Bantuan buat ngecek API Key alat
    private function getDeviceFromToken(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) return null;
        return DB::table('devices')->where('api_key', $token)->first();
    }

    // Fungsi 1: Menerima Uang Masuk (/ingest)
    public function store(Request $request)
    {
        $device = $this->getDeviceFromToken($request);
        if (!$device) return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);

        $request->validate(['jenis_input' => 'required|string', 'nominal' => 'required|integer']);

        DB::table('sensor_data')->insert([
            'device_id'   => $device->id,
            'jenis_input' => $request->jenis_input,
            'nominal'     => $request->nominal,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Uang berhasil masuk!']);
    }

    // Fungsi 2: Menerima Sinyal Pembobolan (/alert)
    public function alert(Request $request)
    {
        $device = $this->getDeviceFromToken($request);
        if (!$device) return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);

        $request->validate(['action' => 'required|string', 'description' => 'required|string']);

        DB::table('activity_log')->insert([
            'device_id'   => $device->id,
            'action'      => $request->action,
            'description' => $request->description,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Alarm bahaya dicatat!']);
    }
}