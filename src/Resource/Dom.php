<?php

namespace Myerscode\Utilities\Web\Resource;

use Symfony\Component\DomCrawler\Crawler;

class Dom extends Crawler
{
    /**
     * Get all image sources from the page
     *
     * @return array<string>
     */
    public function imageSrcs(): array
    {
        $imgs = $this->filterXPath('//img[@src]');

        if ($imgs->count() === 0) {
            return [];
        }

        return $imgs->each(fn (Crawler $node): string => $node->attr('src') ?? '');
    }

    /**
     * Get all links (href attributes) from the page
     *
     * @return array<string>
     */
    public function linkHrefs(): array
    {
        $anchors = $this->filterXPath('//a[@href]');

        if ($anchors->count() === 0) {
            return [];
        }

        return $anchors->each(fn (Crawler $node): string => $node->attr('href') ?? '');
    }

    /**
     * Get a meta tag content by name
     */
    public function meta(string $name): ?string
    {
        $meta = $this->filterXPath(sprintf('//meta[@name="%s"]', $name));

        return $meta->count() > 0 ? $meta->attr('content') : null;
    }

    /**
     * Get the meta description content
     */
    public function metaDescription(): ?string
    {
        $meta = $this->filterXPath('//meta[@name="description"]');

        return $meta->count() > 0 ? $meta->attr('content') : null;
    }
    /**
     * Get the page title text
     */
    public function title(): ?string
    {
        $title = $this->filterXPath('//title');

        return $title->count() > 0 ? $title->text() : null;
    }
}
