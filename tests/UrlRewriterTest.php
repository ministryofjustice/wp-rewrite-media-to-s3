<?php

namespace MOJDigital\RewriteMediaToS3;

use PHPUnit\Framework\TestCase;

class UrlRewriterTest extends TestCase
{
    /**
     * @covers \MOJDigital\RewriteMediaToS3\UrlRewriter::__construct
     * @uses   \MOJDigital\RewriteMediaToS3\UrlRewriter
     */
    public function testCanBeConstructed()
    {
        $r = new UrlRewriter(
            'http://www.example.com/wp-content/uploads',
            'http://cdn.example.com/uploads',
            null
        );

        $this->assertInstanceOf(UrlRewriter::class, $r);
        return $r;
    }

    /**
     * @covers  \MOJDigital\RewriteMediaToS3::rewriteUrl
     * @uses    \MOJDigital\RewriteMediaToS3
     * @depends testCanBeConstructed
     */
    public function testUrlCanBeRewritten(UrlRewriter $r)
    {
        $local = 'http://www.example.com/wp-content/uploads/2016/02/photo.jpg';
        $rewritten = 'http://cdn.example.com/uploads/2016/02/photo.jpg';
        $this->assertEquals($rewritten, $r->rewriteUrl($local));
    }

    /**
     * @covers  \MOJDigital\RewriteMediaToS3::rewriteUrl
     * @uses    \MOJDigital\RewriteMediaToS3
     * @depends testCanBeConstructed
     */
    public function testAlreadyRewrittenUrlIsNotChanged(UrlRewriter $r)
    {
        $alreadyRewritten = 'http://cdn.example.com/uploads/2016/02/photo.jpg';
        $this->assertEquals($alreadyRewritten, $r->rewriteUrl($alreadyRewritten));
    }

    /**
     * @covers  \MOJDigital\RewriteMediaToS3::rewriteUrl
     * @uses    \MOJDigital\RewriteMediaToS3
     * @depends testCanBeConstructed
     */
    public function testMSUrlCanBeRewritten(UrlRewriter $r)
    {
        $local = 'http://www.example.com/wp-content/uploads/sites/2/2016/02/photo.jpg';
        $rewritten = 'http://cdn.example.com/uploads/sites/2/2016/02/photo.jpg';
        $this->assertEquals($rewritten, $r->rewriteUrl($local));
    }

    /**
     * @covers  \MOJDigital\RewriteMediaToS3::rewriteUrl
     * @uses    \MOJDigital\RewriteMediaToS3
     * @depends testCanBeConstructed
     */
    public function testMSAlreadyRewrittenUrlIsNotChanged(UrlRewriter $r)
    {
        $alreadyRewritten = 'http://cdn.example.com/uploads/sites/2/2016/02/photo.jpg';
        $this->assertEquals($alreadyRewritten, $r->rewriteUrl($alreadyRewritten));
    }
}
