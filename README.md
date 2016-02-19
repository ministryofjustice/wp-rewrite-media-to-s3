# Rewrite Media to S3

A WordPress plugin which rewrites media attachment URLs to S3.

This plugin requires that the constant `S3_UPLOADS_BASE_URL` is defined. This should be the base URL of the uploads
directory at S3, **without** a trailing slash.

Example to include in `wp-config.php`:

```php
define('S3_UPLOADS_BASE_URL', 'https://s3-eu-west-1.amazonaws.com/bucketname/uploads');
```
