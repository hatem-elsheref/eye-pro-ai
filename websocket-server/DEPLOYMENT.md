# WebSocket Server Deployment Guide

This guide covers deploying the Node.js WebSocket server on AWS EC2 with Nginx, SSL, PM2, and subdomain configuration.

## Prerequisites

- AWS EC2 instance (Ubuntu 20.04+ recommended)
- Domain name with DNS access
- SSH access to your server
- Basic knowledge of Linux commands

## Step 1: Server Setup

### 1.1 Update System
```bash
sudo apt update
sudo apt upgrade -y
```

### 1.2 Install Node.js
```bash
# Install Node.js 18.x (LTS)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verify installation
node --version
npm --version
```

### 1.3 Install PM2 Globally
```bash
sudo npm install -g pm2
```

### 1.4 Install Nginx
```bash
sudo apt install nginx -y
sudo systemctl start nginx
sudo systemctl enable nginx
```

## Step 2: Deploy WebSocket Server

### 2.1 Create Application Directory
```bash
sudo mkdir -p /var/www/websocket-server
sudo chown $USER:$USER /var/www/websocket-server
cd /var/www/websocket-server
```

### 2.2 Upload Server Files
Upload your server files to `/var/www/websocket-server/`:
- `server.js`
- `package.json`

You can use `scp` from your local machine:
```bash
scp -r websocket-server/* user@your-server-ip:/var/www/websocket-server/
```

Or use `git`:
```bash
cd /var/www/websocket-server
git clone <your-repo-url> .
```

### 2.3 Install Dependencies
```bash
cd /var/www/websocket-server
npm install --production
```

### 2.4 Create Environment File
```bash
nano .env
```

Add:
```
PORT=3001
NODE_ENV=production
```

### 2.5 Test Server Locally
```bash
node server.js
```

Press `Ctrl+C` to stop. If it runs, continue.

## Step 3: Configure PM2

### 3.1 Create PM2 Ecosystem File
```bash
nano ecosystem.config.js
```

Add:
```javascript
module.exports = {
  apps: [{
    name: 'websocket-server',
    script: './server.js',
    instances: 1,
    exec_mode: 'fork',
    env: {
      NODE_ENV: 'production',
      PORT: 3001
    },
    error_file: './logs/err.log',
    out_file: './logs/out.log',
    log_date_format: 'YYYY-MM-DD HH:mm:ss Z',
    merge_logs: true,
    autorestart: true,
    watch: false,
    max_memory_restart: '500M',
    instance_var: 'INSTANCE_ID'
  }]
};
```

### 3.2 Create Logs Directory
```bash
mkdir -p logs
```

### 3.3 Start with PM2
```bash
pm2 start ecosystem.config.js
```

### 3.4 Save PM2 Configuration
```bash
pm2 save
```

### 3.5 Setup PM2 Startup Script
```bash
pm2 startup
```

Follow the instructions shown. It will create a systemd service that starts PM2 on boot.

### 3.6 PM2 Useful Commands
```bash
# Check status
pm2 status

# View logs
pm2 logs websocket-server

# Restart app
pm2 restart websocket-server

# Stop app
pm2 stop websocket-server

# Monitor
pm2 monit
```

## Step 4: Configure Nginx

### 4.1 Create Nginx Configuration
```bash
sudo nano /etc/nginx/sites-available/websocket
```

Add:
```nginx
# Upstream WebSocket server
upstream websocket_backend {
    server 127.0.0.1:3001;
    keepalive 64;
}

# HTTP server (redirect to HTTPS)
server {
    listen 80;
    listen [::]:80;
    server_name ws.yourdomain.com;

    # Redirect all HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

# HTTPS server
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name ws.yourdomain.com;

    # SSL certificates (will be configured by Certbot)
    # ssl_certificate /etc/letsencrypt/live/ws.yourdomain.com/fullchain.pem;
    # ssl_certificate_key /etc/letsencrypt/live/ws.yourdomain.com/privkey.pem;

    # SSL configuration (add after Certbot)
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # WebSocket proxy settings
    location /ws {
        proxy_pass http://websocket_backend;
        proxy_http_version 1.1;
        
        # WebSocket headers
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        # Timeouts for long-lived connections
        proxy_read_timeout 3600s;
        proxy_send_timeout 3600s;
    }

    # API endpoint
    location /api/ {
        proxy_pass http://websocket_backend;
        proxy_http_version 1.1;
        
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        # CORS headers (if needed)
        add_header 'Access-Control-Allow-Origin' '*' always;
        add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS' always;
        add_header 'Access-Control-Allow-Headers' 'Content-Type, Authorization' always;
        
        if ($request_method = 'OPTIONS') {
            return 204;
        }
    }

    # Health check
    location /health {
        proxy_pass http://websocket_backend;
        access_log off;
    }
}
```

**Replace `ws.yourdomain.com` with your subdomain!**

### 4.2 Enable Site
```bash
sudo ln -s /etc/nginx/sites-available/websocket /etc/nginx/sites-enabled/
```

### 4.3 Test Nginx Configuration
```bash
sudo nginx -t
```

### 4.4 Reload Nginx
```bash
sudo systemctl reload nginx
```

## Step 5: SSL Certificate with Let's Encrypt

### 5.1 Install Certbot
```bash
sudo apt install certbot python3-certbot-nginx -y
```

### 5.2 Configure DNS
Before running Certbot, ensure your DNS A record points to your server:
```
Type: A
Name: ws
Value: YOUR_SERVER_IP
TTL: 3600
```

### 5.3 Obtain Certificate
```bash
sudo certbot --nginx -d ws.yourdomain.com
```

Follow the prompts:
- Enter your email
- Agree to terms
- Choose redirect HTTP to HTTPS (option 2)

### 5.4 Verify Auto-Renewal
```bash
sudo certbot renew --dry-run
```

Certbot will auto-renew certificates. Check renewal:
```bash
sudo systemctl status certbot.timer
```

## Step 6: Configure Firewall

### 6.1 Allow Required Ports
```bash
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable
```

### 6.2 Check Status
```bash
sudo ufw status
```

## Step 7: Update Laravel Configuration

### 7.1 Update `.env` File
In your Laravel `.env` file:
```env
WEBSOCKET_HOST=ws.yourdomain.com
```

### 7.2 Update WebSocket URL in Frontend
The frontend code will use:
- `wss://ws.yourdomain.com/ws` for HTTPS
- `ws://ws.yourdomain.com/ws` for HTTP (development)

## Step 8: Testing

### 8.1 Test WebSocket Server
```bash
# Check if server is running
pm2 status

# Check logs
pm2 logs websocket-server

# Test health endpoint
curl http://localhost:3001/health
```

### 8.2 Test via Browser Console
Open browser console on your match page and check for WebSocket connection logs.

### 8.3 Test API Endpoint
```bash
curl -X POST https://ws.yourdomain.com/api/analysis/result \
  -H "Content-Type: application/json" \
  -d '{
    "userId": 1,
    "matchId": 1,
    "analysis": {"test": "data"}
  }'
```

## Step 9: Monitoring & Maintenance

### 9.1 Monitor PM2
```bash
pm2 monit
```

### 9.2 Check Logs
```bash
# PM2 logs
pm2 logs websocket-server

# Nginx logs
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log
```

### 9.3 Restart Services
```bash
# Restart WebSocket server
pm2 restart websocket-server

# Restart Nginx
sudo systemctl restart nginx
```

## Troubleshooting

### WebSocket Connection Fails
1. Check PM2 status: `pm2 status`
2. Check Nginx logs: `sudo tail -f /var/log/nginx/error.log`
3. Verify firewall: `sudo ufw status`
4. Test locally: `curl http://localhost:3001/health`

### SSL Certificate Issues
1. Check certificate: `sudo certbot certificates`
2. Renew manually: `sudo certbot renew`
3. Check DNS: `dig ws.yourdomain.com`

### PM2 Not Auto-Starting
1. Check startup script: `pm2 startup`
2. Save PM2 config: `pm2 save`
3. Check systemd: `sudo systemctl status pm2-$(whoami)`

## Security Recommendations

1. **Keep system updated**: `sudo apt update && sudo apt upgrade`
2. **Use strong passwords** or SSH keys only
3. **Enable fail2ban** for SSH protection
4. **Regular backups** of server and database
5. **Monitor logs** regularly
6. **Keep Node.js and dependencies updated**

## Auto-Restart on Server Crash

PM2 automatically restarts the application if it crashes. To ensure PM2 itself starts on boot:

1. Run `pm2 startup` (already done in Step 3.5)
2. Run `pm2 save` to save current process list

If the entire server reboots, PM2 will automatically start and restore your application.

## Update Application

When updating the application:

```bash
cd /var/www/websocket-server
git pull  # or upload new files
npm install --production
pm2 restart websocket-server
pm2 save
```

## Useful Commands Summary

```bash
# Server
pm2 status                    # Check status
pm2 logs websocket-server     # View logs
pm2 restart websocket-server  # Restart
pm2 stop websocket-server     # Stop
pm2 monit                     # Monitor

# Nginx
sudo nginx -t                 # Test config
sudo systemctl reload nginx   # Reload
sudo systemctl restart nginx  # Restart

# SSL
sudo certbot renew            # Renew certificate
sudo certbot certificates     # List certificates
```

---

**Done!** Your WebSocket server is now running with:
- ✅ Nginx reverse proxy
- ✅ SSL/HTTPS encryption
- ✅ PM2 process management
- ✅ Auto-restart on crash
- ✅ Subdomain setup














