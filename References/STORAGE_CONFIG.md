# Storage Configuration Guide

This application supports dynamic storage configuration for video uploads. You can configure where chunks are stored temporarily and where final files are stored permanently.

## Environment Variables

Add these variables to your `.env` file:

### Chunk Storage (Temporary)
```
CHUNK_STORAGE_DISK=public
```
- **Purpose**: Where upload chunks are stored temporarily during upload
- **Options**: `public`, `local`, `s3` (or any configured disk)
- **Default**: `public` (local storage)
- **Recommendation**: Use `public` (local) for faster chunk writes, or `s3` if you want chunks in cloud

### Final Storage (Permanent)
```
FINAL_STORAGE_DISK=s3
```
- **Purpose**: Where final assembled video files are stored permanently
- **Options**: `public`, `local`, `s3` (or any configured disk in `config/filesystems.php`)
- **Default**: Uses `FILESYSTEM_DISK` env variable (falls back to `public`)
- **Recommendation**: Use `s3` for production, `public` for development

## Configuration Examples

### Example 1: Local Storage (Development)
```env
CHUNK_STORAGE_DISK=public
FINAL_STORAGE_DISK=public
```
- Chunks: `storage/app/public/chunks/`
- Final files: `storage/app/public/matches/`

### Example 2: S3 for Final, Local for Chunks (Recommended for Production)
```env
CHUNK_STORAGE_DISK=public
FINAL_STORAGE_DISK=s3
```
- Chunks: `storage/app/public/chunks/` (fast local writes)
- Final files: `s3://your-bucket/matches/` (cloud storage)

### Example 3: S3 for Everything
```env
CHUNK_STORAGE_DISK=s3
FINAL_STORAGE_DISK=s3
```
- Chunks: `s3://your-bucket/chunks/`
- Final files: `s3://your-bucket/matches/`

## S3 Configuration

To use S3, ensure your `.env` has:

```env
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_URL=https://your-bucket.s3.amazonaws.com
```

## How It Works

### Upload Process:
1. **Chunk Upload**: Chunks are stored in the `CHUNK_STORAGE_DISK` (temporary storage)
2. **Assembly**: 
   - If `FINAL_STORAGE_DISK` is local/public: Assembles directly on disk
   - If `FINAL_STORAGE_DISK` is S3/remote: Assembles in temp local file, then uploads to final storage
3. **Cleanup**: Chunks are deleted from temporary storage after successful assembly

### Benefits:
- **Flexible**: Switch between local and cloud storage easily
- **Efficient**: Can use fast local storage for chunks, cloud for final files
- **Scalable**: Supports any Laravel storage driver (S3, FTP, SFTP, etc.)

## Notes

- Chunks are automatically cleaned up after successful upload
- If assembly fails, chunks remain for potential resume
- Storage disks must be configured in `config/filesystems.php`
- For S3, ensure proper IAM permissions for read/write operations




