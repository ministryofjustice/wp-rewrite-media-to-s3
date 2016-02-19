<?php

namespace MOJDigital\RewriteMediaToS3;

class UrlRewriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \MOJDigital\RewriteMediaToS3\UrlRewriter::__construct
     * @uses   \MOJDigital\RewriteMediaToS3\UrlRewriter
     */
    public function testCanBeConstructed()
    {
        $r = new UrlRewriter('http://www.example.com/wp-content/uploads', 'http://cdn.example.com/uploads');
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
}
