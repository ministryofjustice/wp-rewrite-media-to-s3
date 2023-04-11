<?php
/**
 * Class to rewrite URLs from local to CDN.
 */

namespace MOJDigital\RewriteMediaToS3;

class Rewrite
{
    /**
     * UrlRewriter constructor.
     *
     * @param $localBase
     * @param $cdnBase
     * @param mixed $signed Class object used to sign urls, or null
     */
    public function __construct(private $localBase, private $cdnBase, private readonly mixed $signed){}

    /**
     * Rewrite a local URL to the CDN.
     *
     * @param $url
     */
    public function url($url): string
    {
        if ($this->signed) {
            return $this->signed->uri($url);
        }

        if (stripos((string) $url, (string) $this->localBase) === 0) {
            $url = substr((string) $url, strlen((string) $this->localBase));

            if (is_multisite()) {
                $blog_id = get_current_blog_id();
                if ($blog_id > 1) {
                    $prefix = "/sites/" . $blog_id;
                    $url = $prefix . $url;
                }
            }

            $url = $this->cdnBase . $url;
        }

        return $url;
    }
}
