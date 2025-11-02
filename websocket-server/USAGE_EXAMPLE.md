# Usage Examples

## Laravel Integration

### 1. Update `.env` File

Add to your Laravel `.env`:
```env
WEBSOCKET_URL=http://localhost:3001
# For production:
WEBSOCKET_URL=https://ws.yourdomain.com
WEBSOCKET_HOST=ws.yourdomain.com
```

### 2. Send Analysis Result from Laravel

When your analysis is ready, use the helper:

```php
use App\Helpers\WebSocketHelper;

// Example: After analysis is complete
$match = MatchVideo::find($id);
$analysis = json_encode([
    'score' => 85,
    'events' => [...],
    'stats' => [...]
]);

// Store in database
$match->analysis = $analysis;
$match->save();

// Send to WebSocket server
$result = WebSocketHelper::sendAnalysisResult(
    $match->user_id,
    $match->id,
    json_decode($analysis, true) // Send as array, or keep as JSON string
);

if ($result['success']) {
    // Analysis sent successfully
    logger()->info('Analysis broadcasted', $result);
} else {
    // Handle error (maybe queue for retry)
    logger()->error('Failed to broadcast analysis', $result);
}
```

### 3. Using HTTP Client Directly

```php
use Illuminate\Support\Facades\Http;

Http::post(env('WEBSOCKET_URL') . '/api/analysis/result', [
    'userId' => $match->user_id,
    'matchId' => $match->id,
    'analysis' => $analysisData
]);
```

## Frontend Integration

The frontend code is already integrated in `show.blade.php`. It will:

1. Automatically connect to WebSocket on page load
2. Subscribe to the private channel
3. Receive and render analysis results in real-time

### Custom Frontend Example

```javascript
// Connect to WebSocket
const wsProtocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
const wsHost = 'ws.yourdomain.com'; // or 'localhost:3001' for dev
const ws = new WebSocket(`${wsProtocol}//${wsHost}/ws`);

// Subscribe
ws.onopen = () => {
    ws.send(JSON.stringify({
        type: 'subscribe',
        userId: 1,
        matchId: 5
    }));
};

// Receive results
ws.onmessage = (event) => {
    const data = JSON.parse(event.data);
    
    if (data.type === 'analysis_result') {
        console.log('Analysis received:', data.analysis);
        // Render your analysis data
        renderAnalysis(data.analysis);
    }
};
```

## Testing

### Test WebSocket Server

```bash
# Start server
npm start

# Test health endpoint
curl http://localhost:3001/health

# Test sending analysis
curl -X POST http://localhost:3001/api/analysis/result \
  -H "Content-Type: application/json" \
  -d '{
    "userId": 1,
    "matchId": 1,
    "analysis": {"test": "data"}
  }'
```

### Test from Laravel Tinker

```php
php artisan tinker

>>> use App\Helpers\WebSocketHelper;
>>> WebSocketHelper::sendAnalysisResult(1, 1, ['test' => 'data']);
```

## Queue Integration (Optional)

For better reliability, you can queue the WebSocket notification:

```php
// In your analysis job or controller
dispatch(new BroadcastAnalysisJob($match->id, $analysis));
```

Create the job:

```php
<?php

namespace App\Jobs;

use App\Helpers\WebSocketHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastAnalysisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $matchId,
        public mixed $analysis
    ) {}

    public function handle()
    {
        $match = \App\Models\MatchVideo::find($this->matchId);
        
        if ($match) {
            WebSocketHelper::sendAnalysisResult(
                $match->user_id,
                $match->id,
                $this->analysis
            );
        }
    }
}
```









