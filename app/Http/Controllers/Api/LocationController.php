<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GpsData; // Sesuaikan dengan nama model tabel gps_data kamu
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function getLatestLocation()
    {
        // Mengambil data terbaru dari tabel gps_data
        $location = GpsData::latest()->first();

        if ($location) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'lat' => (float) $location->latitude, // Kolom latitude
                    'lng' => (float) $location->longitude, // Kolom longitude
                    'updated_at' => $location->created_at->format('H:i:s d M Y')
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data GPS tidak ditemukan'
        ], 404);
    }
}