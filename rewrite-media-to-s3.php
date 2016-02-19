<?php

/**
 * Plugin name: Rewrite Media to CDN
 * Description: This plugin will rewrite media asset URLs to their equivalent CDN URL.
 */

namespace MOJDigital\RewriteMediaToS3;

require 'autoload.php';

// Instantiate the class and register hooks
$uploadDir = wp_upload_dir();
$localBase = $uploadDir['baseurl'];

if (defined('S3_UPLOADS_BASE_URL') && !empty(S3_UPLOADS_BASE_URL)) {
    $UrlRewriter = new UrlRewriter($localBase, S3_UPLOADS_BASE_URL);
    $RewriteMediaToS3 = new Plugin($UrlRewriter);
    $RewriteMediaToS3->registerHooks();
}
