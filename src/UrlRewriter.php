<?php
/**
 * Class to rewrite URLs from local to CDN.
 */

namespace MOJDigital\RewriteMediaToS3;

class UrlRewriter
{
    /**
     * Base URL for local uploads directory.
     * Example: http://example.com/wp-content/uploads
     *
     * @var string
     */
    private $localBase = null;

    /**
     * Base URL for the uploads directory on the CDN.
     * Example: https://s3-eu-west-1.amazonaws.com/example/uploads
     *
     * @var string
     */
    private $cdnBase = null;

    /**
     * UrlRewriter constructor.
     *
     * @param string $localUploadsBaseUrl Base URL for local uploads directory.
     * @param string $cdnUploadsBaseUrl Base URL for the uploads directory on the CDN.
     */
    public function __construct($localUploadsBaseUrl, $cdnUploadsBaseUrl)
    {
        $this->localBase = $localUploadsBaseUrl;
        $this->cdnBase   = $cdnUploadsBaseUrl;
    }

    /**
     * Rewrite a local URL to the CDN.
     *
     * @param $url
     *
     * @return string
     */
    public function rewriteUrl($url)
    {
        if (stripos($url, $this->localBase) === 0) {
            $url = substr($url, strlen($this->localBase));

            var_dump(S3_SIGN_URLS);die();
            if (defined('S3_SIGN_URLS') && (string)S3_SIGN_URLS === 'true') {
                $url = new Signature($url);
            }

            $url = $this->cdnBase . $url;
        }

        return $url;
    }
}
