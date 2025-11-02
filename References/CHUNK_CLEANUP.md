# Chunk Cleanup Command

This command automatically cleans up orphaned chunks from failed/expired uploads and expired cache keys.

## What It Does

1. **Cleans expired cache entries**: Removes upload status and lock cache entries that have expired
2. **Finds orphaned chunks**: Identifies chunk directories that have no corresponding cache entry (upload failed or expired)
3. **Deletes orphaned chunks**: Removes chunk directories that are older than the specified threshold
4. **Cleans expired locks**: Removes upload locks that have expired

## Usage

### Manual Cleanup

```bash
# Interactive mode (asks for confirmation)
php artisan chunks:cleanup

# Force cleanup without confirmation
php artisan chunks:cleanup --force

# Clean chunks older than 4 hours (default is 2 hours)
php artisan chunks:cleanup --older-than=4

# Force cleanup for chunks older than 6 hours
php artisan chunks:cleanup --force --older-than=6
```

### Scheduled Cleanup

The command is automatically scheduled to run **daily at 2:00 AM**. The schedule is configured in `routes/console.php`.

To test the scheduled task:
```bash
php artisan schedule:run
```

## Options

- `--force`: Skip confirmation prompt and delete immediately
- `--older-than=N`: Only clean chunks older than N hours (default: 2)

## Safety Features

- **Recent upload protection**: Chunks modified in the last 30 minutes are skipped (assumed to be in progress)
- **Cache validation**: Only deletes chunks that have no active cache entry
- **Age threshold**: Only deletes chunks older than the specified hours
- **Error handling**: Logs errors but continues cleanup process

## How It Works

1. **Scans chunk directories**: Finds all directories matching `chunks/user_*/upload_*`
2. **Checks cache**: Verifies if each directory has a corresponding cache entry
3. **Age check**: Verifies directories are older than the threshold
4. **Deletes**: Removes orphaned chunk directories and expired cache entries

## Storage Support

Works with:
- **Local storage** (`public`, `local` disks)
- **S3 storage** (chunks stored in S3)
- **Any Laravel storage driver**

## Monitoring

Check logs for cleanup activity:
```bash
tail -f storage/logs/laravel.log | grep "chunks:cleanup"
```

## Manual Testing

```bash
# Test with verbose output
php artisan chunks:cleanup --verbose

# See what would be deleted (without --force)
php artisan chunks:cleanup

# Force cleanup older than 1 hour
php artisan chunks:cleanup --force --older-than=1
```

## Cron Setup

For production, add to your crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

This runs the Laravel scheduler every minute, which will execute scheduled tasks at their designated times.




