<?php
/**
 * The file responsible for starting the Rewrite Media to CDN plugin
 *
 * The Rewrite Media to CDN is a plugin that rewrites the domain path
 * of images, documents and other assets to a defined domain.
 * It also signs URLs with a secure hash so it can be verified
 * and accessible via the AWS s3 bucket.
 * This particular file is responsible for
 * including the necessary dependencies and starting the plugin.
 *
 * @package Rewrite Media to CDN
 *
 * @wordpress-plugin
 * Plugin Name:       Rewrite Media to CDN
 * Plugin URI:        https://github.com/ministryofjustice/wp-rewrite-media-to-s3
 * Description:       This plugin will rewrite media asset and sign URLs to their equivalent CDN URL.
 * Version:           0.3.1
 * Text Domain:       rewrite-media-cdn
 * Author:            Ministry of Justice
 * Author URI:        https://github.com/ministryofjustice
 * License:           The MIT License (MIT)
 * License URI:       https://opensource.org/licenses/MIT
 * Copyright:         Crown Copyright (c) 2020 Ministry of Justice
 */

namespace MOJDigital\RewriteMediaToS3;


//Do not allow access outside of WP to plugin
defined('ABSPATH') or die();

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
