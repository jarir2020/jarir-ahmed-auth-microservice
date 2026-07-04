<?php

namespace JarirAhmed\AuthMicroservice\Controllers;

use JarirAhmed\AuthMicroservice\Http\Controller;
use JarirAhmed\AuthMicroservice\Http\Request;
use JarirAhmed\AuthMicroservice\Services\ExportService;

class ExportController extends Controller
{
    public function __construct(private ExportService $exportService) {}

    public function request(Request $request)
    {
        $data = $request->validate(['format' => 'in:json,csv']);
        $exportRequest = $this->exportService->requestExport($request->user(), $data['format'] ?? 'json');
        return response()->json(['message' => 'Export requested.', 'request' => $exportRequest], 202);
    }

    public function status(Request $request, int $id)
    {
        $exportRequest = $request->user()->dataExportRequests()->findOrFail($id);
        return response()->json($exportRequest);
    }
}
