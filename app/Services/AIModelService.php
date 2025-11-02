<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIModelService
{
    protected $endpoint;

    public function __construct()
    {
        $this->endpoint = env('AI_MODEL_ENDPOINT');
    }

    /**
     * Start processing for a match
     */
    public function startProcessing(int $matchId): array
    {
        if (!$this->endpoint) {
            return $this->error('AI_MODEL_ENDPOINT not configured');
        }


        try {
            $response = Http::timeout(30)->post("{$this->endpoint}/start", [
                'match_id' => $matchId
            ]);

            if ($response->successful()) {
                Log::info('AI processing started', ['matchId' => $matchId]);
                return $this->success($response->json());
            }

            return $this->error('Failed to start processing', $response->body());
        } catch (\Exception $e) {
            Log::error('Exception starting AI processing', ['matchId' => $matchId, 'error' => $e->getMessage()]);
            return $this->error('Exception occurred', $e->getMessage());
        }
    }

    /**
     * Stop processing for a match
     */
    public function stopProcessing(int $matchId): array
    {
        if (!$this->endpoint) {
            return $this->error('AI_MODEL_ENDPOINT not configured');
        }

        try {
            $response = Http::timeout(30)->post("{$this->endpoint}/stop", [
                'match_id' => $matchId
            ]);

            if ($response->successful()) {
                Log::info('AI processing stopped', ['matchId' => $matchId]);
                return $this->success($response->json());
            }

            return $this->error('Failed to stop processing', $response->body());
        } catch (\Exception $e) {
            Log::error('Exception stopping AI processing', ['matchId' => $matchId, 'error' => $e->getMessage()]);
            return $this->error('Exception occurred', $e->getMessage());
        }
    }

    /**
     * Get processing status
     */
    public function getStatus(int $matchId): array
    {
        if (!$this->endpoint) {
            return $this->error('AI_MODEL_ENDPOINT not configured', null, null);
        }

        try {
            $response = Http::timeout(10)->post("{$this->endpoint}/status", [
                'match_id' => $matchId
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('AI status retrieved', ['matchId' => $matchId, 'status' => $data['status'] ?? null]);
                return $this->success($data, $data['status'] ?? null);
            }

            return $this->error('Failed to get status', $response->body(), null);
        } catch (\Exception $e) {
            Log::error('Exception getting AI status', ['matchId' => $matchId, 'error' => $e->getMessage()]);
            return $this->error('Exception occurred', $e->getMessage(), null);
        }
    }

    /**
     * Success response
     */
    protected function success($data = null, $status = null): array
    {
        return [
            'success' => true,
            'status'  => $status,
            'data'    => $data
        ];
    }

    /**
     * Error response
     */
    protected function error(string $message, $error = null, $status = null): array
    {
        return [
            'success' => false,
            'status'  => $status,
            'message' => $message,
            'error'   => $error
        ];
    }
}


