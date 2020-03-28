<?php
/**
 * Class to hook into and augment WordPress functionality.
 */

namespace MOJDigital\RewriteMediaToS3;

class Plugin
{
    /**
     * Holds the UrlRewriter object.
     *
     * @var UrlRewriter
     */
    public $UrlRewriter = null;

    /**
     * Plugin constructor.
     *
     * @param UrlRewriter $UrlRewriter
     */
    public function __construct(UrlRewriter $UrlRewriter)
    {
        $this->UrlRewriter = $UrlRewriter;
    }

    /**
     * Register required WordPress hooks.
     *
     * @return void
     */
    public function registerHooks()
    {
        add_filter('wp_get_attachment_url', array($this, 'wpGetAttachmentUrl'), 9, 2);
        add_filter('wp_get_attachment_image_src', array($this, 'wpGetAttachmentImageSrc'), 99, 4);
        add_filter('wp_calculate_image_srcset', array($this, 'wpCalculateImageSrcset'), 9, 5);
    }

    /**
     * Deregister WordPress hooks.
     * Undoes what $this->registerHooks() does.
     *
     * @return void
     */
    public function deregisterHooks()
    {
        remove_filter('wp_get_attachment_url', array($this, 'wpGetAttachmentUrl'), 9);
        remove_filter('wp_calculate_image_srcset', array($this, 'wpCalculateImageSrcset'), 9);
    }

    /**
     * Filter the attachment URL.
     *
     * @param string $url URL for the given attachment.
     * @param int $post_id Attachment ID.
     *
     * @return string
     */
    public function wpGetAttachmentUrl($url, $post_id)
    {
        return $this->UrlRewriter->rewriteUrl($url);
    }

    /**
     * Filter the attachment Image src URL.
     *
     * @param $image
     * @param int $post_id Attachment ID.
     *
     * @param $size
     * @param $icon
     * @return string
     */
    public function wpGetAttachmentImageSrc($image, $post_id, $size, $icon)
    {
        $url = $this->UrlRewriter->rewriteUrl($image[0]);
        $image[0] = $url;
        return $image;
    }

    /**
     * Filter an image's 'srcset' sources.
     *
     * @param array $sources {
     *     One or more arrays of source data to include in the 'srcset'.
     *
     * @type array $width {
     * @type string $url The URL of an image source.
     * @type string $descriptor The descriptor type used in the image candidate string,
     *                                  either 'w' or 'x'.
     * @type int $value The source width if paired with a 'w' descriptor, or a
     *                                  pixel density value if paired with an 'x' descriptor.
     *     }
     * }
     *
     * @param array $size_array Array of width and height values in pixels (in that order).
     * @param string $image_src The 'src' of the image.
     * @param array $image_meta The image meta data as returned by 'wp_get_attachment_metadata()'.
     * @param int $attachment_id Image attachment ID or 0.
     *
     * @return array
     */
    public function wpCalculateImageSrcset($sources, $size_array, $image_src, $image_meta, $attachment_id)
    {
        foreach ($sources as $size => $source) {
            $sources[$size]['url'] = $this->UrlRewriter->rewriteUrl($source['url']);
        }

        return $sources;
    }
}
