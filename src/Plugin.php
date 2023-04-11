<?php
/**
 * Class to hook into and augment WordPress functionality.
 */

namespace MOJDigital\RewriteMediaToS3;

class Plugin
{
    /**
     * Plugin constructor.
     */
    public function __construct(public Rewrite $rewrite){}

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
     */
    public function wpGetAttachmentUrl(string $url): string
    {
        return $this->rewrite->url($url);
    }

    /**
     * Filter the attachment Image src URL.
     */
    public function wpGetAttachmentImageSrc(array $image): array
    {
        $url = $this->rewrite->url($image[0]);
        $image[0] = $url;
        return $image;
    }

    /**
     * Filter an image's 'srcset' sources.
     */
    public function wpCalculateImageSrcset(array $sources): array
    {
        foreach ($sources as $size => $source) {
            $sources[$size]['url'] = $this->rewrite->url($source['url']);
        }

        return $sources;
    }
}
