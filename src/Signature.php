<?php
namespace MOJDigital\RewriteMediaToS3;

use \Aws\S3\S3Client as S3Client;
use \Aws\Exception\AwsException;

class Signature
{
    private $client = null;

    private $bucket = null;

    public $uri = '';

    public function __construct(string $uri)
    {
        $region = (defined('AWS_DEFAULT_REGION') ? AWS_DEFAULT_REGION : 'eu-west-1');
        $this->client = new S3Client([
            'profile' => 'default',
            'region' => $region,
            'version' => '2012-10-17'
        ]);

        $this->uri = $uri;
        $this->bucket = $this->get_bucket() ?? 'intranet2-staging-storage-h1d4c9820k0u';

        return $this->uri();
    }

    public function uri()
    {
        $cmd = $this->client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $this->uri
        ]);

        $request = $this->client->createPresignedRequest($cmd, '+2 minutes');

        return (string)$request->getUri();
    }

    private function get_bucket()
    {
        $env = (WP_ENV === 'production' ? 'prod' : (WP_ENV === 'development' ? 'dev' : 'staging'));
        $app = (defined('APP_NAME') ? APP_NAME : 'intranet2');

        $buckets = $this->client->listBuckets([]);
        var_dump($buckets);
        die();

        $buckets = $buckets['Buckets'];
        $pattern = $app . '-' . $env . '-';

        $bucket = array_filter($buckets, function($entry) use ($pattern) {
            return fnmatch($pattern, $entry);
        });

        return $bucket ?? null;
    }
}
