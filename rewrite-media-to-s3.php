<?php

/**
 * Plugin name: Rewrite Media to CDN
 * Description: This plugin will rewrite media asset URLs to their equivalent CDN URL.
 */

namespace MOJDigital\RewriteMediaToS3;

require 'autoload.php';

$uploadDir = wp_upload_dir();
$localBase = $uploadDir['baseurl'];

// define S3_SIGN_URLS
define('S3_SIGN_URLS', env('S3_SIGN_URLS') ?: false);

// Instantiate the class and register hooks
if (defined('S3_UPLOADS_BASE_URL') && !empty(S3_UPLOADS_BASE_URL)) {
    $signed = null;
    var_dump($signed);
    if (S3_SIGN_URLS !== false) {
        var_dump(S3_SIGN_URLS);
        $signed = new Signature();
    }
    $UrlRewriter = new UrlRewriter($localBase, S3_UPLOADS_BASE_URL, $signed);
    $RewriteMediaToS3 = new Plugin($UrlRewriter);
    $RewriteMediaToS3->registerHooks();
}
