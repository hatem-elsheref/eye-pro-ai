# AWS S3 IAM Policy Configuration

## Required IAM Policy for S3 Operations

Your IAM user `s3-web-app` needs the following permissions to work with the bucket `web-app-var`.

### IAM Policy JSON

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "AllowS3BucketOperations",
            "Effect": "Allow",
            "Action": [
                "s3:ListBucket",
                "s3:GetBucketLocation",
                "s3:ListBucketMultipartUploads",
                "s3:ListBucketVersions"
            ],
            "Resource": "arn:aws:s3:::web-app-var"
        },
        {
            "Sid": "AllowS3ObjectOperations",
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:GetObjectVersion",
                "s3:PutObjectAcl",
                "s3:GetObjectAcl",
                "s3:DeleteObjectVersion",
                "s3:AbortMultipartUpload",
                "s3:ListMultipartUploadParts"
            ],
            "Resource": "arn:aws:s3:::web-app-var/*"
        }
    ]
}
```

## How to Apply This Policy

### Option 1: Attach Policy to IAM User (Recommended)

1. Go to AWS Console → IAM → Users → `s3-web-app`
2. Click on "Add permissions" → "Attach policies directly"
3. Click "Create policy"
4. Select "JSON" tab
5. Paste the policy above
6. Review and name it (e.g., `WebAppS3FullAccess`)
7. Save and attach it to your user

### Option 2: Add Inline Policy

1. Go to AWS Console → IAM → Users → `s3-web-app`
2. Click on "Add permissions" → "Create inline policy"
3. Select "JSON" tab
4. Paste the policy above
5. Name it and save

## Minimum Required Permissions (If you want to restrict more)

If you want to restrict permissions further (least privilege), you can use:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:ListBucket"
            ],
            "Resource": "arn:aws:s3:::web-app-var",
            "Condition": {
                "StringLike": {
                    "s3:prefix": [
                        "Full_Matches/*",
                        "chunks/*"
                    ]
                }
            }
        },
        {
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:PutObjectAcl"
            ],
            "Resource": [
                "arn:aws:s3:::web-app-var/Full_Matches/*",
                "arn:aws:s3:::web-app-var/chunks/*"
            ]
        }
    ]
}
```

## Testing the Policy

After applying the policy, test with:

```bash
aws s3 ls s3://web-app-var/
aws s3 ls s3://web-app-var/Full_Matches/
```

## Notes

- `s3:ListBucket` is required for:
  - Checking if files/directories exist (`Storage::exists()`)
  - Listing directory contents
  - Multipart upload operations

- The policy separates bucket-level permissions (`s3:ListBucket`) from object-level permissions (`s3:GetObject`, etc.)
- The bucket name in Resource should match your actual bucket: `web-app-var`
- Make sure your bucket region matches your `AWS_DEFAULT_REGION` environment variable

## Troubleshooting

If you still get permission errors:
1. Wait 1-2 minutes for IAM changes to propagate
2. Check that the bucket name matches exactly
3. Verify the IAM user ARN is correct
4. Check CloudTrail logs for detailed permission denial reasons

