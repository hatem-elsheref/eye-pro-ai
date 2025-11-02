<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebSocketHelper
{
    /**
     * Send analysis result to WebSocket server
     *
     * @param int $userId
     * @param int $matchId
     * @param mixed $analysis
     * @return array
     */
    public static function sendAnalysisResult($userId, $matchId, $analysis)
    {
        $websocketUrl = env('WEBSOCKET_URL', 'http://localhost:3001');

        try {
            $response = Http::timeout(10)->post("{$websocketUrl}/api/analysis/result", [
                'userId' => $userId,
                'matchId' => $matchId,
                'analysis' => $analysis
            ]);

            if ($response->successful()) {
                Log::info('Analysis result sent to WebSocket server', [
                    'userId' => $userId,
                    'matchId' => $matchId,
                    'response' => $response->json()
                ]);

                return [
                    'success' => true,
                    'message' => $response->json()['message'] ?? 'Analysis sent successfully',
                    'data' => $response->json()
                ];
            } else {
                Log::error('Failed to send analysis result to WebSocket server', [
                    'userId' => $userId,
                    'matchId' => $matchId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send analysis result',
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception sending analysis result to WebSocket server', [
                'userId' => $userId,
                'matchId' => $matchId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send notification to user via WebSocket
     *
     * @param int $userId
     * @param array $notification
     * @return array
     */
    public static function sendNotification($userId, array $notification)
    {
        $websocketUrl = env('WEBSOCKET_URL', 'http://localhost:3001');

        try {
            $response = Http::timeout(10)->post("{$websocketUrl}/api/notification", [
                'userId' => $userId,
                'notification' => $notification
            ]);

            if ($response->successful()) {
                Log::info('Notification sent to WebSocket server', [
                    'userId' => $userId,
                    'response' => $response->json()
                ]);

                return [
                    'success' => true,
                    'message' => $response->json()['message'] ?? 'Notification sent successfully',
                    'data' => $response->json()
                ];
            } else {
                Log::error('Failed to send notification to WebSocket server', [
                    'userId' => $userId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send notification',
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception sending notification to WebSocket server', [
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred',
                'error' => $e->getMessage()
            ];
        }
    }
}


