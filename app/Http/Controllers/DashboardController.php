<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sensor_data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Ambil semua data sensor untuk tabel & grafik
     * GET /api/dashboard/data
     */
    public function getData(Request $request)
    {
        $limit  = (int) $request->query('limit', 100);
        $sensor = $request->query('sensor');

        $query = Sensor_data::query()->orderBy('created_at', 'desc');

        if ($sensor) {
            $query->where('sensor_name', $sensor);
        }

        $data = $query->limit($limit)->get(['id', 'device_id', 'sensor_name', 'value', 'created_at']);

        return response()->json([
            'status' => 'success',
            'total'  => $data->count(),
            'data'   => $data,
        ]);
    }

    /**
     * Ambil data terbaru tiap jenis sensor (untuk card summary)
     * GET /api/dashboard/latest
     */
    public function getLatest()
    {
        $sensors = ['Suhu', 'Kelembapan', 'Jarak'];
        $latest  = [];

        foreach ($sensors as $s) {
            $row = Sensor_data::where('sensor_name', $s)
                ->orderBy('created_at', 'desc')
                ->first(['value', 'device_id', 'created_at']);

            $latest[$s] = $row ? $row->value : null;
        }

        return response()->json([
            'status' => 'success',
            'data'   => $latest,
        ]);
    }

    /**
     * Gabungkan data sensor terbaru + status relay terbaru
     * GET /get-data  (dipanggil dari dashboard.blade.php)
     */
    public function getCombined()
    {
        $sensors    = ['Suhu', 'Kelembapan', 'Jarak'];
        $sensorData = [];

        foreach ($sensors as $s) {
            $row = Sensor_data::where('sensor_name', $s)
                ->orderBy('created_at', 'desc')
                ->first(['device_id', 'sensor_name', 'value', 'created_at']);

            if ($row) {
                $sensorData[] = [
                    'sensor_name' => $row->sensor_name,
                    'value'       => $row->value,
                ];
            }
        }

        $relay = DB::table('relay_controls')
            ->orderBy('updated_at', 'desc')
            ->first();

        return response()->json([
            'sensor' => ['data' => $sensorData],
            'relay'  => $relay ? (int) $relay->status : 0,
        ]);
    }

    /**
     * Hapus semua data sensor (opsional)
     * DELETE /api/dashboard/clear
     */
    public function clearData()
    {
        Sensor_data::truncate();

        return response()->json([
            'status'  => 'success',
            'message' => 'Semua data sensor telah dihapus.',
        ]);
    }
}