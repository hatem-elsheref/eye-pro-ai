# WebSocket Server for Match Analysis

Real-time WebSocket server for broadcasting match analysis results to connected clients.

## Features

- ✅ Private channels per user and match (`private-user-{userId}-match-{matchId}`)
- ✅ POST endpoint to receive analysis results
- ✅ Automatic broadcasting to subscribed clients
- ✅ Connection management and cleanup
- ✅ Health check endpoint
- ✅ CORS support

## Quick Start

### Install Dependencies
```bash
npm install
```

### Run Development Server
```bash
npm run dev
# or
node server.js
```

### Run Production Server
```bash
npm start
# or use PM2 (see DEPLOYMENT.md)
pm2 start ecosystem.config.js
```

## API Endpoints

### POST `/api/analysis/result`

Send analysis result to subscribed clients.

**Request:**
```json
{
  "userId": 1,
  "matchId": 5,
  "analysis": {
    "score": 85,
    "events": [...],
    "stats": {...}
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Analysis result sent to 2 client(s)",
  "channel": "private-user-1-match-5",
  "sentCount": 2
}
```

### GET `/health`

Health check endpoint.

**Response:**
```json
{
  "status": "ok",
  "timestamp": "2025-01-01T12:00:00.000Z",
  "activeChannels": 5,
  "totalConnections": 10
}
```

### GET `/api/channels`

Get list of active channels (for debugging).

**Response:**
```json
{
  "success": true,
  "channels": [
    {
      "channel": "private-user-1-match-5",
      "connections": 2
    }
  ],
  "totalChannels": 1,
  "totalConnections": 2
}
```

## WebSocket Protocol

### Connect
```
ws://localhost:3001/ws
```

### Subscribe to Channel
Send message:
```json
{
  "type": "subscribe",
  "userId": 1,
  "matchId": 5
}
```

### Receive Subscription Confirmation
```json
{
  "type": "subscribed",
  "channel": "private-user-1-match-5"
}
```

### Receive Analysis Result
```json
{
  "type": "analysis_result",
  "userId": 1,
  "matchId": 5,
  "analysis": {...},
  "timestamp": "2025-01-01T12:00:00.000Z"
}
```

## Environment Variables

Create `.env` file:

```env
PORT=3001
NODE_ENV=production
```

## Usage Example

### From Laravel (using HTTP client)

```php
use Illuminate\Support\Facades\Http;

// When analysis is ready
Http::post('https://ws.yourdomain.com/api/analysis/result', [
    'userId' => $match->user_id,
    'matchId' => $match->id,
    'analysis' => $analysisData // Your analysis JSON
]);
```

### From Frontend (JavaScript)

```javascript
const ws = new WebSocket('wss://ws.yourdomain.com/ws');

ws.onopen = function() {
    ws.send(JSON.stringify({
        type: 'subscribe',
        userId: 1,
        matchId: 5
    }));
};

ws.onmessage = function(event) {
    const data = JSON.parse(event.data);
    if (data.type === 'analysis_result') {
        console.log('Analysis received:', data.analysis);
    }
};
```

## Channel Naming

Channels are automatically created using the format:
```
private-user-{userId}-match-{matchId}
```

This ensures:
- Privacy: Only the specific user gets results for their matches
- Isolation: Each match has its own channel
- Automatic cleanup: Channels are removed when no clients are connected

## Error Handling

The server handles:
- Missing required fields (returns 400)
- No active connections (returns success but warns)
- WebSocket connection errors
- Invalid JSON payloads

## Security Considerations

1. **Authentication**: Add authentication middleware to the POST endpoint
2. **Rate Limiting**: Implement rate limiting to prevent abuse
3. **CORS**: Configure CORS properly for production
4. **HTTPS/WSS**: Always use SSL/TLS in production

## Development

### Install Nodemon (optional)
```bash
npm install -g nodemon
```

### Run with Auto-Reload
```bash
npm run dev
```

## License

ISC



