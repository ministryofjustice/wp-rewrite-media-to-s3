<?php

namespace MOJDigital\RewriteMediaToS3;

use \Aws\S3\S3Client;
use \Aws\Exception\AwsException;

class Signature
{
    /**
     * @var S3Client|null
     */
    public $client = null;

    /**
     * @var string|null
     */
    public $bucket = null;

    public function __construct()
    {
        try {
            $this->client = new S3Client([
                'version' => 'latest',
                'region' => env('AWS_DEFAULT_REGION'),
                'credentials' => [
                    'key' => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ]
            ]);
        } catch (AwsException $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }

        $this->bucket = env('AWS_S3_BUCKET') ?: false;
    }

    /**
     * Return a signed URL
     * @param $uri
     * @return string
     */
    public function uri($uri)
    {
        $url_parts = parse_url($uri);
        if (!empty($url_parts['query'])) {
            parse_str($url_parts['query'], $query_parts);
            if (isset($query_parts['X-Amz-Content-Sha256'])) {
                return $uri;
            }
        }

        $uri = ltrim($url_parts['path'], '/');

        if (strpos($uri, 'uploads') === false) {
            $uri = 'uploads/' . ltrim($uri, '/');
        }

        if (strpos($uri, 'app') >= 0) {
            $uri = str_replace('app/', '', $uri);
        }

        $cmd = $this->client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $uri
        ]);

        $request = $this->client->createPresignedRequest($cmd, '+5 minutes');

        return (string)$request->getUri();
    }
}
