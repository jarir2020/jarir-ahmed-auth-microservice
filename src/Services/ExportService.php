<?php

namespace JarirAhmed\AuthMicroservice\Services;

use JarirAhmed\AuthMicroservice\Models\DataExportRequest;
use League\Csv\Writer;

class ExportService
{
    public function requestExport(mixed $user, string $format = 'json'): DataExportRequest
    {
        return DataExportRequest::create([
            'user_id' => $user->getKey(),
            'format'  => $format,
            'status'  => 'pending',
        ]);
    }

    public function process(DataExportRequest $request): void
    {
        $request->update(['status' => 'processing']);

        try {
            $user = $request->user;
            $data = [
                'profile'       => $user->only(['id', 'name', 'email', 'created_at']),
                'login_history' => $user->loginHistories()->get(['ip_address', 'country', 'city', 'device', 'os', 'browser', 'created_at'])->toArray(),
                'audit_logs'    => $user->auditLogs()->get(['event', 'ip_address', 'created_at'])->toArray(),
                'tokens'        => $user->personalAccessTokens()->get(['name', 'scopes', 'created_at', 'expires_at'])->toArray(),
            ];

            $dir  = storage_path('app/exports');
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $filename = "export_{$user->getKey()}_{$request->id}";

            if ($request->format === 'csv') {
                $path = "{$dir}/{$filename}.csv";
                $this->writeCsv($path, $data);
            } else {
                $path = "{$dir}/{$filename}.json";
                file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
            }

            $request->update(['status' => 'completed', 'file_path' => $path, 'completed_at' => now()]);
        } catch (\Throwable $e) {
            $request->update(['status' => 'failed']);
            throw $e;
        }
    }

    private function writeCsv(string $path, array $data): void
    {
        $rows = [];
        foreach ($data as $section => $records) {
            foreach ((array) $records as $record) {
                $rows[] = array_merge(['section' => $section], (array) $record);
            }
        }

        $fp = fopen($path, 'w');
        if (!empty($rows)) {
            fputcsv($fp, array_keys($rows[0]));
            foreach ($rows as $row) fputcsv($fp, $row);
        }
        fclose($fp);
    }
}
