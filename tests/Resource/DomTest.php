<?php

declare(strict_types=1);

namespace Tests\Resource;

use Myerscode\Utilities\Web\Resource\Dom;
use PHPUnit\Framework\TestCase;

final class DomTest extends TestCase
{
    public function testImagesReturnsAllSrcs(): void
    {
        $html = '<html><body><img src="/img1.png"><img src="/img2.jpg"></body></html>';
        $dom = $this->makeDom($html);

        $this->assertSame(['/img1.png', '/img2.jpg'], $dom->imageSrcs());
    }

    public function testImagesReturnsEmptyWhenNoImages(): void
    {
        $dom = $this->makeDom('<html><body><p>No images</p></body></html>');

        $this->assertSame([], $dom->imageSrcs());
    }

    public function testLinksReturnsAllHrefs(): void
    {
        $html = '<html><body><a href="/page1">One</a><a href="/page2">Two</a></body></html>';
        $dom = $this->makeDom($html);

        $this->assertSame(['/page1', '/page2'], $dom->linkHrefs());
    }

    public function testLinksReturnsEmptyWhenNoAnchors(): void
    {
        $dom = $this->makeDom('<html><body><p>No links</p></body></html>');

        $this->assertSame([], $dom->linkHrefs());
    }

    public function testMetaDescriptionReturnsContent(): void
    {
        $dom = $this->makeDom('<html><head><meta name="description" content="A test page"></head></html>');

        $this->assertSame('A test page', $dom->metaDescription());
    }

    public function testMetaDescriptionReturnsNullWhenMissing(): void
    {
        $dom = $this->makeDom('<html><head></head></html>');

        $this->assertNull($dom->metaDescription());
    }

    public function testMetaReturnsContentByName(): void
    {
        $html = '<html><head><meta name="author" content="John"></head></html>';
        $dom = $this->makeDom($html);

        $this->assertSame('John', $dom->meta('author'));
    }

    public function testMetaReturnsNullWhenNotFound(): void
    {
        $dom = $this->makeDom('<html><head></head></html>');

        $this->assertNull($dom->meta('robots'));
    }

    public function testTitleReturnsNullWhenMissing(): void
    {
        $dom = $this->makeDom('<html><head></head></html>');

        $this->assertNull($dom->title());
    }

    public function testTitleReturnsPageTitle(): void
    {
        $dom = $this->makeDom('<html><head><title>Hello World</title></head></html>');

        $this->assertSame('Hello World', $dom->title());
    }
    private function makeDom(string $html): Dom
    {
        return new Dom($html);
    }
}
