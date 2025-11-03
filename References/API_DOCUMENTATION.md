# API Endpoints Documentation

This document provides detailed information about the API endpoints for the Eye Pro Match Analysis Platform. These endpoints are designed for the AI model to interact with the system.

**Base URL:** `https://your-domain.com/api`

---

## Endpoint 1: Get Match Information

### Overview
Retrieves match video information including file path, storage location, and video URL. Used by the AI model to access match files for processing.

### Details

| Property | Value |
|----------|-------|
| **URL** | `/api/match/{id}` |
| **Method** | `GET` |
| **Authentication** | Required (API Key via Header) |

### Path Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | integer | Yes | The unique identifier of the match |

### Query Parameters
None

### Headers
```
X-API-Key: {your-api-key}
Accept: application/json
```

### Request Body
None

### Request Example
```bash
curl -X GET "https://your-domain.com/api/match/123" \
  -H "X-API-Key: your-api-key-here" \
  -H "Accept: application/json"
```

### Response

#### Success Response (200 OK)
```json
{
  "success": true,
  "data": {
    "id": 123,
    "name": "Match Video Name",
    "path": "Full_Matches/upload_user_1_1234567890_video.mp4",
    "disk": "s3",
    "size": "500 MB",
    "url": "https://s3.amazonaws.com/bucket/Full_Matches/...",
    "size_bytes": 524288000
  }
}
```

#### Error Responses

**401 Unauthorized**
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**404 Not Found**
```json
{
  "success": false,
  "message": "Match not found"
}
```

#### Response Fields
| Field | Type | Description |
|-------|------|-------------|
| `success` | boolean | Indicates if the request was successful |
| `data` | object | Match information object |
| `data.id` | integer | Match unique identifier |
| `data.name` | string | Name of the match |
| `data.path` | string | Storage path of the video file |
| `data.disk` | string | Storage disk (`public`, `local`, `s3`) |
| `data.size` | string | Human-readable file size (e.g., "500 MB") |
| `data.url` | string | Accessible URL for the video file (may be pre-signed for S3) |
| `data.size_bytes` | integer | File size in bytes (if available) |

---

## Endpoint 2: Store Prediction

### Overview
Stores a prediction result from the AI model. This endpoint is called by the AI model each time it generates a prediction for an event in the match video.

### Details

| Property | Value |
|----------|-------|
| **URL** | `/api/match/{id}/prediction` |
| **Method** | `POST` |
| **Authentication** | Required (API Key via Header) |
| **Content-Type** | `application/json` |

### Path Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | integer | Yes | The unique identifier of the match |

### Query Parameters
None

### Headers
```
X-API-Key: {your-api-key}
Content-Type: application/json
Accept: application/json
```

### Request Body
| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `clip_path` | string | No | Storage path of the clip video file (if generated) |
| `relative_time` | string | No | Relative time in the match when this prediction occurred |
| `first_model_prop` | number | No | Confidence score from the first model (0-1) |
| `prediction_0` | object | No | First prediction data (model output structure) |
| `prediction_1` | object | No | Second prediction data (model output structure) |

### Request Example
```json
{
  "clip_path": "clips/match_123_prediction_1.mp4",
  "relative_time": "00:15:30",
  "first_model_prop": 0.85,
  "prediction_0": {
    "class": "offence",
    "severity": "high",
    "confidence": 0.92
  },
  "prediction_1": {
    "class": "action",
    "type": "foul",
    "confidence": 0.88
  }
}
```

### Request Example (cURL)
```bash
curl -X POST "https://your-domain.com/api/match/123/prediction" \
  -H "X-API-Key: your-api-key-here" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "clip_path": "clips/match_123_prediction_1.mp4",
    "relative_time": "00:15:30",
    "first_model_prop": 0.85,
    "prediction_0": {
      "class": "offence",
      "severity": "high",
      "confidence": 0.92
    },
    "prediction_1": {
      "class": "action",
      "type": "foul",
      "confidence": 0.88
    }
  }'
```

### Response

#### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Prediction stored successfully",
  "data": {
    "prediction_id": 456,
    "match_id": 123
  }
}
```

#### Error Responses

**400 Bad Request - Validation Error**
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "prediction_0": ["The prediction_0 must be an array."]
  }
}
```

**401 Unauthorized**
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**404 Not Found**
```json
{
  "success": false,
  "message": "Match not found"
}
```

#### Response Fields
| Field | Type | Description |
|-------|------|-------------|
| `success` | boolean | Indicates if the prediction was stored successfully |
| `message` | string | Human-readable message about the result |
| `data` | object | Prediction information |
| `data.prediction_id` | integer | Unique identifier of the created prediction |
| `data.match_id` | integer | ID of the match this prediction belongs to |

**Note:** When a prediction is stored, it is automatically sent to the user via WebSocket for real-time updates. If the match status is `pending`, it will be updated to `processing`.

---

## Endpoint 3: Complete Processing

### Overview
Marks a match as completed after the AI model finishes analyzing the entire video. This triggers notifications to the user and updates the match status.

### Details

| Property | Value |
|----------|-------|
| **URL** | `/api/match/{id}/complete` |
| **Method** | `POST` |
| **Authentication** | Required (API Key via Header) |
| **Content-Type** | `application/json` |

### Path Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | integer | Yes | The unique identifier of the match |

### Query Parameters
None

### Headers
```
X-API-Key: {your-api-key}
Content-Type: application/json
Accept: application/json
```

### Request Body
```json
{}
```

**Note:** This endpoint does not require a request body. All necessary information is provided via the path parameter.

### Request Example
```bash
curl -X POST "https://your-domain.com/api/match/123/complete" \
  -H "X-API-Key: your-api-key-here" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

### Response

#### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Match processing marked as completed",
  "data": {
    "match_id": 123,
    "status": "completed"
  }
}
```

#### Error Responses

**401 Unauthorized**
```json
{
  "success": false,
  "message": "Unauthorized"
}
```

**404 Not Found**
```json
{
  "success": false,
  "message": "Match not found"
}
```

#### Response Fields
| Field | Type | Description |
|-------|------|-------------|
| `success` | boolean | Indicates if processing was marked as completed |
| `message` | string | Human-readable message about the result |
| `data` | object | Match status information |
| `data.match_id` | integer | ID of the match |
| `data.status` | string | New status of the match (should be `"completed"`) |

**Note:** When processing is completed:
- The match status is updated to `completed`
- A notification is sent to the user
- A WebSocket message is sent to notify the user's browser in real-time

---

## Authentication

All API endpoints require authentication via the `X-API-Key` header. The API key must be configured in your environment variable `AI_API_KEY`.

### How to Use API Key
1. Set the `AI_API_KEY` environment variable in your `.env` file
2. Include the header in all API requests: `X-API-Key: your-api-key-value`

### Example
```bash
# .env file
AI_API_KEY=your-secret-api-key-12345

# In API request
X-API-Key: your-secret-api-key-12345
```

---

## Error Handling

All endpoints follow consistent error response patterns:

- **200 OK**: Request successful
- **400 Bad Request**: Validation errors or invalid input
- **401 Unauthorized**: Missing or invalid API key
- **404 Not Found**: Resource not found
- **500 Internal Server Error**: Server-side error

All error responses include:
```json
{
  "success": false,
  "message": "Error description"
}
```

---

## Notes

1. **Base URL**: Replace `https://your-domain.com` with your actual application domain
2. **API Routes**: All routes are prefixed with `/api`
3. **Real-time Updates**: Predictions are automatically sent to users via WebSocket when stored
4. **File URLs**: For S3 storage, URLs are pre-signed and expire after 1 hour for security
5. **Processing Flow**: 
   - Match status starts as `pending`
   - Changes to `processing` when first prediction is received
   - Changes to `completed` when `/complete` endpoint is called

---

## Complete Workflow Example

### Step 1: Get Match Information
```bash
GET /api/match/123
Response: Match file path and URL
```

### Step 2: Process Video and Store Predictions (Multiple Calls)
```bash
POST /api/match/123/prediction
Body: { "prediction_0": {...}, "prediction_1": {...}, ... }
Response: Prediction stored
```

### Step 3: Mark Processing Complete
```bash
POST /api/match/123/complete
Response: Match marked as completed
```

---

## Support

For issues or questions regarding the API:
- Check the application logs for detailed error messages
- Ensure your API key is correctly configured
- Verify the match ID exists and is in the correct status
