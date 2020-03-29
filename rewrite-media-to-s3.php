<?php

/**
 * Plugin name: Rewrite Media to CDN
 * Description: This plugin will rewrite media asset URLs to their equivalent CDN URL.
 */

namespace MOJDigital\RewriteMediaToS3;

require 'autoload.php';
require_once('src/settings.php');

$uploadDir = wp_upload_dir();
$localBase = $uploadDir['baseurl'];

// define S3_SIGN_URLS
$s3SignUrlOptions = get_option('rewrite_media_to_s3_settings', []);
define(
    'S3_SIGN_URLS',
    (
    isset($s3SignUrlOptions['create_secure_urls_select'])
        ? $s3SignUrlOptions['create_secure_urls_select']
        : false
    )
);

// Instantiate the class and register hooks
if (defined('S3_UPLOADS_BASE_URL') && !empty(S3_UPLOADS_BASE_URL)) {
    $Signed = null;
    if (S3_SIGN_URLS === 'yes') {
        $Signed = new Signature();
    }
    $UrlRewrite = new UrlRewriter($localBase, S3_UPLOADS_BASE_URL, $Signed);
    $RewriteMediaToS3 = new Plugin($UrlRewrite);
    $RewriteMediaToS3->registerHooks();
}
