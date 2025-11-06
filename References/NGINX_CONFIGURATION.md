# Nginx Configuration for Chunked File Uploads

This guide explains how to configure Nginx to handle large file chunked uploads and prevent 504 timeout errors.

## Quick Setup

### 1. Edit Your Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/your-site-name
```

### 2. Add These Critical Settings

Add these settings to your `server` block:

```nginx
# Maximum upload size (per chunk + overhead)
client_max_body_size 100M;

# Timeouts (critical for preventing 504 errors)
client_body_timeout 60s;
client_header_timeout 60s;
fastcgi_read_timeout 120s;
fastcgi_send_timeout 120s;
fastcgi_connect_timeout 60s;

# FastCGI buffers
fastcgi_buffer_size 128k;
fastcgi_buffers 4 256k;
fastcgi_busy_buffers_size 256k;
```

### 3. Optional: Specific Configuration for Upload Endpoint

For even better control, add a specific location block for the upload endpoint:

```nginx
location ~ ^/matches/upload/chunk$ {
    client_max_body_size 10M;
    client_body_timeout 120s;
    fastcgi_read_timeout 180s;
    fastcgi_send_timeout 180s;
    
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $realpath_root/index.php;
    include fastcgi_params;
    
    fastcgi_buffer_size 128k;
    fastcgi_buffers 4 256k;
    fastcgi_busy_buffers_size 256k;
}
```

### 4. Test Configuration

```bash
# Test nginx configuration syntax
sudo nginx -t

# If test passes, reload nginx
sudo systemctl reload nginx
```

## Configuration Explained

### Client Body Settings

- **`client_max_body_size`**: Maximum size of client request body (upload size)
  - Set to `100M` to allow chunk uploads with overhead
  - Can be set higher if needed (e.g., `500M` or `1G`)

- **`client_body_buffer_size`**: Buffer size for reading client body
  - Set to `1M` for chunk uploads

### Timeout Settings

- **`client_body_timeout`**: Time to wait for client to send request body
  - Set to `60s` (512KB chunks should upload in 5-10 seconds)
  - Increase to `120s` if you have very slow connections

- **`client_header_timeout`**: Time to wait for client headers
  - Set to `60s`

- **`fastcgi_read_timeout`**: Time to wait for PHP-FPM response
  - Set to `120s` to allow PHP processing time

- **`fastcgi_send_timeout`**: Time to send request to PHP-FPM
  - Set to `120s`

### FastCGI Buffer Settings

These settings help handle chunk uploads efficiently:

- **`fastcgi_buffer_size`**: Buffer size for FastCGI response headers
- **`fastcgi_buffers`**: Number and size of buffers for FastCGI responses
- **`fastcgi_busy_buffers_size`**: Size of buffers marked as "busy"

## Complete Configuration Example

See `nginx-config-example.conf` for a complete nginx configuration example.

## Troubleshooting

### Still Getting 504 Errors?

1. **Check nginx error logs**:
   ```bash
   sudo tail -f /var/log/nginx/error.log
   ```

2. **Increase timeouts further**:
   - Try `client_body_timeout 120s`
   - Try `fastcgi_read_timeout 180s`

3. **Check PHP-FPM settings**:
   ```bash
   sudo nano /etc/php/8.2/fpm/php.ini
   ```
   Ensure:
   ```ini
   upload_max_filesize = 100M
   post_max_size = 100M
   max_execution_time = 300
   memory_limit = 512M
   ```

4. **Check PHP-FPM pool settings**:
   ```bash
   sudo nano /etc/php/8.2/fpm/pool.d/www.conf
   ```
   Ensure:
   ```ini
   request_terminate_timeout = 300
   ```

### Testing Upload

After configuration, test with:
```bash
# Monitor nginx error log in real-time
sudo tail -f /var/log/nginx/error.log

# Monitor PHP-FPM log
sudo tail -f /var/log/php8.2-fpm.log
```

## Production Recommendations

1. **Use HTTPS**: Configure SSL certificates for secure uploads
2. **Monitor Logs**: Set up log rotation for nginx logs
3. **Rate Limiting**: Consider adding rate limiting for upload endpoints
4. **Load Balancer**: If using a load balancer, configure timeouts there too

## Load Balancer Configuration

If you're behind a load balancer (AWS ALB, Cloudflare, etc.), you may also need to configure timeouts there:

### AWS Application Load Balancer
- Idle timeout: 60-120 seconds
- Request timeout: 60-120 seconds

### Cloudflare
- Change timeout in Cloudflare dashboard
- Or use Cloudflare Workers for custom timeout handling

## Verification

After applying changes, verify:

1. **Nginx configuration is valid**:
   ```bash
   sudo nginx -t
   ```

2. **Nginx reloaded successfully**:
   ```bash
   sudo systemctl status nginx
   ```

3. **Test upload in browser**:
   - Try uploading a file
   - Monitor network tab for any 504 errors
   - Check nginx error logs

## Support

If you continue to experience issues:
1. Check nginx error logs
2. Check PHP-FPM error logs
3. Verify PHP configuration
4. Test with a smaller chunk size (256KB instead of 512KB)

