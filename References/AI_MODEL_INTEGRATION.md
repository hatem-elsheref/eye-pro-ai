# AI Model Integration Guide

This document describes how the AI model integration works with the match processing system.

## Overview

When a match is created in the database, the system automatically:
1. Sends a notification to the user that the upload is complete and AI processing has started
2. Calls the AI model `/start` endpoint to begin processing
3. The AI model can query match information and update analysis results via API

## Environment Variables

Add these to your `.env` file:

```env
# AI Model Endpoint
AI_MODEL_ENDPOINT=http://localhost:8000
# For production: https://ai-model.yourdomain.com

# API Key for AI Model to authenticate when calling Laravel API
AI_API_KEY=your-secure-api-key-here
# Generate a secure random key for production
```

## AI Model Endpoints

### 1. Start Processing (Called by Laravel)

**Endpoint:** `POST {AI_MODEL_ENDPOINT}/start`

**Request Body:**
```json
{
  "match_id": 123
}
```

**Purpose:** Laravel calls this endpoint when a new match is created to start AI processing.

### 2. Get Processing Status (Called by Laravel)

**Endpoint:** `POST {AI_MODEL_ENDPOINT}/status`

**Request Body:**
```json
{
  "match_id": 123
}
```

**Expected Response:**
```json
{
  "status": "processing",
  "message": "Processing in progress"
}
```

**Possible Status Values:**
- `processing` or `in_progress` - Still processing
- `completed` or `finished` - Processing complete
- `failed` - Processing failed

**Purpose:** Laravel calls this endpoint daily to check the actual status of matches that have been processing for >24 hours. Based on the response, Laravel will:
- Mark as failed if status is `failed` or still `processing` after 24h
- Skip if status is `completed` (analysis may be queued)

### 3. Get Match Information (Called by AI Model)

**Endpoint:** `GET /api/match/{id}`

**Headers:**
```
X-API-Key: {AI_API_KEY}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 123,
    "name": "Match Name",
    "path": "matches/upload_123_video.mp4",
    "disk": "public",
    "size": "150.5 MB",
    "url": "http://example.com/storage/matches/upload_123_video.mp4",
    "size_bytes": 157286400
  }
}
```

**Purpose:** AI model queries match information including file path, disk, size, and URL.

### 4. Update Match Analysis (Called by AI Model)

**Endpoint:** `PUT /api/match/{id}/analysis`

**Headers:**
```
X-API-Key: {AI_API_KEY}
Content-Type: application/json
```

**Request Body:**
```json
{
  "analysis": {
    "score": 85,
    "events": [...],
    "stats": {...}
  },
  "status": "completed"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Match analysis updated successfully",
  "data": {
    "match_id": 123,
    "status": "completed"
  }
}
```

**Purpose:** AI model updates the match with analysis results. This automatically:
- Saves analysis to the `analysis` column in the matches table
- Updates match status (completed/failed/processing)
- Sends a notification to the user that analysis is complete

## Notifications

The system sends three types of notifications:

### 1. Match Upload Processing
**Triggered:** When a match is successfully uploaded
**Message:** "Your match '{name}' has been uploaded successfully. AI model is now processing your video."
**Type:** `match_upload_processing`

### 2. Match Analysis Complete
**Triggered:** When AI model updates match with analysis results
**Message:** "AI model has finished processing your match '{name}'. Results are now available."
**Type:** `match_analysis_complete`

### 3. Match Processing Failed
**Triggered:** When processing fails (timeout after 24 hours or system failure)
**Message:** "Processing failed for your match '{name}'. Please try uploading again or contact support."
**Type:** `match_processing_failed`

## Daily Command

A scheduled command runs daily at 3 AM to check for stale matches:

```bash
php artisan matches:check-stale
```

**What it does:**
- Finds matches older than 24 hours with status `processing`
- Calls AI model `/status` endpoint for each match to get actual status
- Handles different status responses:
  - If status is `failed` → Marks as failed in database
  - If status is `completed`/`finished` → Skips (analysis may be queued)
  - If status is still `processing` → Marks as failed (timeout after 24h)
  - If status check fails → Marks as failed (assumes failure)
- Sets analysis to show appropriate error message
- Sends failure notification to user (only for failed matches)

**Schedule:** Configured in `routes/console.php` to run daily at 3:00 AM

**Status Check Logic:**
The command queries the AI model's actual status rather than just relying on time. This ensures accuracy - if the AI model says it's completed but analysis hasn't arrived yet, we wait. Only actual failures or timeouts are marked as failed.

## Flow Diagram

```
User Uploads Match
    ↓
Match Created in Database (status: processing)
    ↓
Notification Sent: "Upload complete, AI processing started"
    ↓
Laravel calls: POST {AI_MODEL_ENDPOINT}/start
    ↓
AI Model Receives Start Request
    ↓
AI Model Queries: GET /api/match/{id} (with API key)
    ↓
AI Model Gets Match Info (path, disk, size, URL)
    ↓
AI Model Processes Video
    ↓
AI Model Updates: PUT /api/match/{id}/analysis (with API key)
    ↓
Laravel Updates Match (analysis + status)
    ↓
Notification Sent: "Analysis complete"
```

## Error Handling

### If AI Model Start Fails
- Error is logged but match creation continues
- User still receives upload notification
- Match remains in `processing` status
- Daily command will mark as failed after 24 hours

### If Analysis Update Fails
- AI model should retry the update
- Check API key is correct
- Check match ID exists
- Verify network connectivity

### Timeout Handling
- Matches processing for >24 hours are automatically marked as failed
- Users receive failure notification
- Analysis column shows timeout error

## Security

1. **API Key Protection:**
   - Store `AI_API_KEY` securely in `.env`
   - Never commit to version control
   - Use strong, random keys in production

2. **Endpoint Protection:**
   - API endpoints require `X-API-Key` header
   - Invalid keys return 401 Unauthorized

3. **Match Access:**
   - AI model can only query matches by ID
   - No user authentication bypass
   - File paths returned are safe (already stored in database)

## Testing

### Test AI Model Start
```bash
curl -X POST http://localhost:8000/start \
  -H "Content-Type: application/json" \
  -d '{"match_id": 1}'
```

### Test AI Model Status
```bash
curl -X POST http://localhost:8000/status \
  -H "Content-Type: application/json" \
  -d '{"match_id": 1}'
```

**Expected Response:**
```json
{
  "status": "processing",
  "message": "Processing in progress"
}
```

### Test Match Query
```bash
curl -X GET http://localhost:8000/api/match/1 \
  -H "X-API-Key: your-api-key"
```

### Test Analysis Update
```bash
curl -X PUT http://localhost:8000/api/match/1/analysis \
  -H "X-API-Key: your-api-key" \
  -H "Content-Type: application/json" \
  -d '{
    "analysis": {"test": "data"},
    "status": "completed"
  }'
```

## Troubleshooting

### AI model not receiving start request
- Check `AI_MODEL_ENDPOINT` in `.env`
- Verify endpoint is accessible
- Check Laravel logs for errors

### API key authentication failing
- Verify `AI_API_KEY` matches between Laravel and AI model
- Check request headers include `X-API-Key`
- Ensure no extra spaces or newlines in API key

### Notifications not showing
- Verify user has `Notifiable` trait
- Check notifications table exists: `php artisan migrate`
- View notifications at `/notifications`

### Matches stuck in processing
- Run manual check: `php artisan matches:check-stale`
- Verify scheduled command is running
- Check cron/scheduler configuration

