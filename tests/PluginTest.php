<?php

namespace MOJDigital\RewriteMediaToS3;

use PHPUnit\Framework\TestCase;

class PluginTest extends TestCase
{
    /**
     * @covers \MOJDigital\RewriteMediaToS3\Plugin::__construct
     * @uses   \MOJDigital\RewriteMediaToS3\Plugin
     */
    public function testCanBeConstructed()
    {
        $rewriter = $this->getMockBuilder('\MOJDigital\RewriteMediaToS3\UrlRewriter')
                         ->disableOriginalConstructor()
                         ->getMock();

        $p = new Plugin($rewriter);
        $this->assertInstanceOf(Plugin::class, $p);
        return $p;
    }

    /**
     * @covers \MOJDigital\RewriteMediaToS3\Plugin::wpGetAttachmentUrl
     * @uses   \MOJDigital\RewriteMediaToS3\Plugin
     */
    public function testWpGetAttachmentUrlRewritesUrl()
    {
        $local = 'http://www.example.com/wp-content/uploads/2016/02/photo.jpg';
        $rewritten = 'http://cdn.example.com/uploads/2016/02/photo.jpg';

        $rewriter = $this->getMockBuilder('\MOJDigital\RewriteMediaToS3\UrlRewriter')
                         ->disableOriginalConstructor()
                         ->getMock();

        $rewriter->expects($this->once())
                 ->method('rewriteUrl')
                 ->with($this->equalTo($local))
                 ->willReturn($rewritten);

        $p = new Plugin($rewriter);
        $this->assertEquals($rewritten, $p->wpGetAttachmentUrl($local, 10));
    }

    /**
     * @covers \MOJDigital\RewriteMediaToS3\Plugin::wpCalculateImageSrcset
     * @uses   \MOJDigital\RewriteMediaToS3\Plugin
     */
    public function testWpCalculateImageSrcsetRewritesUrls()
    {
        // Input array – contains local URLs
        $local = array (
            300 => array (
                'url' => 'http://www.example.com/wp-content/uploads/2016/02/photo-300x225.jpg',
                'descriptor' => 'w',
                'value' => 300,
            ),
            600 => array (
                'url' => 'http://www.example.com/wp-content/uploads/2016/02/photo.jpg',
                'descriptor' => 'w',
                'value' => 600,
            ),
        );

        // Expected output array – containing CDN URLs
        $expectRewritten = array (
            300 => array (
                'url' => 'http://cdn.example.com/uploads/2016/02/photo-300x225.jpg',
                'descriptor' => 'w',
                'value' => 300,
            ),
            600 => array (
                'url' => 'http://cdn.example.com/uploads/2016/02/photo.jpg',
                'descriptor' => 'w',
                'value' => 600,
            ),
        );

        // Setup a mock UrlRewriter object
        $rewriter = $this->getMockBuilder('\MOJDigital\RewriteMediaToS3\UrlRewriter')
                         ->disableOriginalConstructor()
                         ->getMock();

        $rewriter->expects($this->exactly(2))
                 ->method('rewriteUrl')
                 ->will(
                     $this->returnValueMap(
                         array(
                             array(
                                 'http://www.example.com/wp-content/uploads/2016/02/photo-300x225.jpg',
                                 'http://cdn.example.com/uploads/2016/02/photo-300x225.jpg',
                             ),
                             array(
                                 'http://www.example.com/wp-content/uploads/2016/02/photo.jpg',
                                 'http://cdn.example.com/uploads/2016/02/photo.jpg',
                             )
                         )
                     )
                 );

        // Mock values to pass to method being tested
        $size_array = array(
            0 => 300,
            1 => 225,
        );

        $image_src = 'http://cdn.example.com/uploads/2016/02/photo.jpg';

        $image_meta = array(
            'width' => 600,
            'height' => 500,
            'file' => '2016/02/photo.jpg',
            'sizes' => array(
                'medium' => array(
                    'file' => 'photo-300x225.jpg',
                    'width' => 300,
                    'height' => 225,
                    'mime-type' => 'image/jpeg',
                ),
            ),
            'image_meta' => array(
                'aperture' => 0,
                'credit' => '',
                'camera' => '',
                'caption' => '',
                'created_timestamp' => 0,
                'copyright' => '',
                'focal_length' => 0,
                'iso' => 0,
                'shutter_speed' => 0,
                'title' => '',
                'orientation' => 1,
                'keywords' => array(),
            ),
        );

        $attachment_id = 10;

        $p = new Plugin($rewriter);
        $this->assertEquals(
            $expectRewritten,
            $p->wpCalculateImageSrcset($local, $size_array, $image_src, $image_meta, $attachment_id)
        );
    }
}
