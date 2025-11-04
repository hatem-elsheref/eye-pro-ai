const express = require('express');
const http = require('http');
const WebSocket = require('ws');
const cors = require('cors');
const bodyParser = require('body-parser');

const app = express();
const server = http.createServer(app);

// WebSocket server
const wss = new WebSocket.Server({
    server,
    path: '/ws'
});

// Store active connections by channel
const connections = new Map();

// Middleware
app.use(cors());
app.use(bodyParser.json());

// Helper function to create channel name
function createChannelName(userId, matchId) {
    return `private-user-${userId}-match-${matchId}`;
}

// Helper function to create notification channel name
function createNotificationChannelName(userId) {
    return `notifications-user-${userId}`;
}

// WebSocket connection handling
wss.on('connection', (ws, req) => {
   // console.log('New WebSocket connection');

    let channel = null;

    ws.on('message', (message) => {
        try {
            const data = JSON.parse(message);

            if (data.type === 'subscribe') {
                if (data.userId && data.subscribeType === 'notifications') {
                    // Subscribe to notifications channel
                    channel = createNotificationChannelName(data.userId);

                    if (!connections.has(channel)) {
                        connections.set(channel, new Set());
                    }

                    connections.get(channel).add(ws);
                    ws.channel = channel;
                    ws.subscriptionType = 'notifications';

                    console.log(`Client subscribed to notifications channel: ${channel}`);

                    ws.send(JSON.stringify({
                        type: 'subscribed',
                        channel: channel,
                        subscriptionType: 'notifications'
                    }));
                } else if (data.userId && data.matchId) {
                    // Subscribe to match channel
                    channel = createChannelName(data.userId, data.matchId);

                    if (!connections.has(channel)) {
                        connections.set(channel, new Set());
                    }

                    connections.get(channel).add(ws);
                    ws.channel = channel;
                    ws.subscriptionType = 'match';

                    console.log(`Client subscribed to match channel: ${channel}`);

                    ws.send(JSON.stringify({
                        type: 'subscribed',
                        channel: channel
                    }));
                }
            }
        } catch (error) {
            console.error('Error parsing WebSocket message:', error);
        }
    });

    ws.on('close', () => {
        if (channel && connections.has(channel)) {
            connections.get(channel).delete(ws);

            // Clean up empty channels
            if (connections.get(channel).size === 0) {
                connections.delete(channel);
            }
        }
       // console.log(`Client disconnected from channel: ${channel || 'unknown'}`);
    });

    // Store user ID if provided for notifications
    if (req.url && req.url.includes('userId=')) {
        const urlParams = new URLSearchParams(req.url.split('?')[1]);
        const userId = urlParams.get('userId');
        if (userId) {
            ws.userId = parseInt(userId);
        }
    }

    ws.on('error', (error) => {
        //console.error('WebSocket error:', error);
    });
});

// POST endpoint to send notification to user
app.post('/api/notification', (req, res) => {
    try {
        const { userId, notification } = req.body;

        // Validate required fields
        if (!userId || !notification) {
            return res.status(400).json({
                success: false,
                message: 'Missing required fields: userId and notification'
            });
        }

        // Create notification channel name
        const channel = createNotificationChannelName(userId);

        // Get connections for this channel
        const channelConnections = connections.get(channel);

        if (!channelConnections || channelConnections.size === 0) {
           // console.log(`No active connections for notification channel: ${channel}`);
            return res.status(200).json({
                success: true,
                message: 'Notification received but no active listeners',
                channel: channel
            });
        }

        // Prepare message
        const message = JSON.stringify({
            type: 'notification',
            userId: userId,
            notification: notification,
            timestamp: new Date().toISOString()
        });

        // Send to all connected clients in this channel
        let sentCount = 0;
        const clientsToRemove = [];

        channelConnections.forEach((ws) => {
            if (ws.readyState === WebSocket.OPEN) {
                try {
                    ws.send(message);
                    sentCount++;
                } catch (error) {
                    console.error('Error sending notification:', error);
                    clientsToRemove.push(ws);
                }
            } else {
                clientsToRemove.push(ws);
            }
        });

        // Clean up closed connections
        clientsToRemove.forEach(ws => {
            channelConnections.delete(ws);
        });

        //console.log(`Sent notification to ${sentCount} client(s) in channel: ${channel}`);

        res.json({
            success: true,
            message: `Notification sent to ${sentCount} client(s)`,
            channel: channel,
            sentCount: sentCount
        });

    } catch (error) {
        console.error('Error processing notification:', error);
        res.status(500).json({
            success: false,
            message: 'Internal server error',
            error: error.message
        });
    }
});

// POST endpoint to receive predictions
app.post('/api/prediction', (req, res) => {
    try {
        const { userId, matchId, prediction } = req.body;

        // Validate required fields
        if (!userId || !matchId || !prediction) {
            return res.status(400).json({
                success: false,
                message: 'Missing required fields: userId, matchId, and prediction'
            });
        }

        // Create channel name
        const channel = createChannelName(userId, matchId);

        // Get connections for this channel
        const channelConnections = connections.get(channel);

        if (!channelConnections || channelConnections.size === 0) {
           // console.log(`No active connections for channel: ${channel}`);
            return res.status(200).json({
                success: true,
                message: 'Prediction received but no active listeners',
                channel: channel
            });
        }

        // Prepare message
        const message = JSON.stringify({
            type: 'prediction',
            userId: userId,
            matchId: matchId,
            prediction: prediction,
            timestamp: new Date().toISOString()
        });

        // Send to all connected clients in this channel
        let sentCount = 0;
        const clientsToRemove = [];

        channelConnections.forEach((ws) => {
            if (ws.readyState === WebSocket.OPEN) {
                try {
                    ws.send(message);
                    sentCount++;
                } catch (error) {
                    console.error('Error sending message:', error);
                    clientsToRemove.push(ws);
                }
            } else {
                clientsToRemove.push(ws);
            }
        });

        // Clean up closed connections
        clientsToRemove.forEach(ws => {
            channelConnections.delete(ws);
        });

        //console.log(`Sent prediction to ${sentCount} client(s) in channel: ${channel}`);

        res.json({
            success: true,
            message: `Prediction sent to ${sentCount} client(s)`,
            channel: channel,
            sentCount: sentCount
        });

    } catch (error) {
        console.error('Error processing prediction:', error);
        res.status(500).json({
            success: false,
            message: 'Internal server error',
            error: error.message
        });
    }
});

// POST endpoint to notify processing is complete
app.post('/api/processing/complete', (req, res) => {
    try {
        const { userId, matchId } = req.body;

        // Validate required fields
        if (!userId || !matchId) {
            return res.status(400).json({
                success: false,
                message: 'Missing required fields: userId and matchId'
            });
        }

        // Create channel name
        const channel = createChannelName(userId, matchId);

        // Get connections for this channel
        const channelConnections = connections.get(channel);

        if (!channelConnections || channelConnections.size === 0) {
           // console.log(`No active connections for channel: ${channel}`);
            return res.status(200).json({
                success: true,
                message: 'Processing complete notification received but no active listeners',
                channel: channel
            });
        }

        // Prepare message
        const message = JSON.stringify({
            type: 'processing_complete',
            userId: userId,
            matchId: matchId,
            timestamp: new Date().toISOString()
        });

        // Send to all connected clients in this channel
        let sentCount = 0;
        const clientsToRemove = [];

        channelConnections.forEach((ws) => {
            if (ws.readyState === WebSocket.OPEN) {
                try {
                    ws.send(message);
                    sentCount++;
                } catch (error) {
                    console.error('Error sending message:', error);
                    clientsToRemove.push(ws);
                }
            } else {
                clientsToRemove.push(ws);
            }
        });

        // Clean up closed connections
        clientsToRemove.forEach(ws => {
            channelConnections.delete(ws);
        });

        //console.log(`Sent processing complete to ${sentCount} client(s) in channel: ${channel}`);

        res.json({
            success: true,
            message: `Processing complete sent to ${sentCount} client(s)`,
            channel: channel,
            sentCount: sentCount
        });

    } catch (error) {
       // console.error('Error processing completion notification:', error);
        res.status(500).json({
            success: false,
            message: 'Internal server error',
            error: error.message
        });
    }
});

// POST endpoint to receive analysis results (legacy - kept for backward compatibility)
app.post('/api/analysis/result', (req, res) => {
    try {
        const { userId, matchId, analysis } = req.body;

        // Validate required fields
        if (!userId || !matchId || analysis === undefined) {
            return res.status(400).json({
                success: false,
                message: 'Missing required fields: userId, matchId, and analysis'
            });
        }

        // Create channel name
        const channel = createChannelName(userId, matchId);

        // Get connections for this channel
        const channelConnections = connections.get(channel);

        if (!channelConnections || channelConnections.size === 0) {
            //console.log(`No active connections for channel: ${channel}`);
            return res.status(200).json({
                success: true,
                message: 'Result received but no active listeners',
                channel: channel
            });
        }

        // Prepare message
        const message = JSON.stringify({
                type: 'analysis_result',
            userId: userId,
            matchId: matchId,
            analysis: analysis,
            timestamp: new Date().toISOString()
        });

        // Send to all connected clients in this channel
        let sentCount = 0;
        const clientsToRemove = [];

        channelConnections.forEach((ws) => {
            if (ws.readyState === WebSocket.OPEN) {
                try {
                    ws.send(message);
                    sentCount++;
                } catch (error) {
                    console.error('Error sending message:', error);
                    clientsToRemove.push(ws);
                }
            } else {
                clientsToRemove.push(ws);
            }
        });

        // Clean up closed connections
        clientsToRemove.forEach(ws => {
            channelConnections.delete(ws);
        });

        //console.log(`Sent analysis result to ${sentCount} client(s) in channel: ${channel}`);

        res.json({
            success: true,
            message: `Analysis result sent to ${sentCount} client(s)`,
            channel: channel,
            sentCount: sentCount
        });

    } catch (error) {
        console.error('Error processing analysis result:', error);
        res.status(500).json({
            success: false,
            message: 'Internal server error',
            error: error.message
        });
    }
});

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({
        status: 'ok',
        timestamp: new Date().toISOString(),
        activeChannels: connections.size,
        totalConnections: Array.from(connections.values()).reduce((sum, set) => sum + set.size, 0)
    });
});

// Get active channels (for debugging)
app.get('/api/channels', (req, res) => {
    const channels = Array.from(connections.keys()).map(channel => ({
        channel: channel,
        connections: connections.get(channel).size
    }));

    res.json({
        success: true,
        channels: channels,
        totalChannels: channels.length,
        totalConnections: channels.reduce((sum, ch) => sum + ch.connections, 0)
    });
});

const PORT = process.env.PORT || 3001;

server.listen(PORT, () => {
    console.log(`WebSocket server running on port ${PORT}`);
    console.log(`WebSocket endpoint: ws://localhost:${PORT}/ws`);
    console.log(`API endpoint: http://localhost:${PORT}/api/analysis/result`);
});

// Graceful shutdown
process.on('SIGTERM', () => {
    console.log('SIGTERM received, closing server...');
    wss.close(() => {
        server.close(() => {
            console.log('Server closed');
            process.exit(0);
        });
    });
});


