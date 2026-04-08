<?php

namespace Tests\Unit;

use App\Services\Posts\YouTubeUrlParser;
use PHPUnit\Framework\TestCase;

class YouTubeUrlParserTest extends TestCase
{
    public function testItExtractsVideoIdFromSupportedUrls(): void
    {
        $parser = new YouTubeUrlParser();

        $this->assertSame('abc123', $parser->extractVideoId('https://www.youtube.com/watch?v=abc123'));
        $this->assertSame('abc123', $parser->extractVideoId('https://youtu.be/abc123'));
        $this->assertSame('abc123', $parser->extractVideoId('https://www.youtube.com/shorts/abc123'));
    }

    public function testItReturnsNullForUnsupportedUrls(): void
    {
        $parser = new YouTubeUrlParser();

        $this->assertNull($parser->extractVideoId('https://vimeo.com/123'));
        $this->assertNull($parser->embedUrl('not-a-url'));
    }
}
