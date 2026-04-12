<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IngestRequest;
use App\Services\SensorService;

class IngestController extends Controller
{
    protected $sensorService;

    public function __construct(SensorService $sensorService)
    {
        $this->sensorService = $sensorService;
    }

    public function store(IngestRequest $request)
    {
        try {
            $result = $this->sensorService->storeData($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Data tabungan berhasil dicatat',
                'data' => $result
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}