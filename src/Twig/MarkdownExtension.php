<?php

namespace App\Twig;

use League\CommonMark\CommonMarkConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'markdown',
                [$this, 'convertMarkdown'],
                ['is_safe' => ['html']]
            ),

            new TwigFilter(
                'excerpt',
                [$this, 'excerpt']
            ),
        ];
    }

    public function convertMarkdown(string $text): string
    {
        $converter = new CommonMarkConverter();

        return $converter
            ->convert($text)
            ->getContent();
    }

    public function excerpt(string $text, int $length = 180): string
    {
        $converter = new CommonMarkConverter();

        $html = $converter
            ->convert($text)
            ->getContent();

        $text = strip_tags($html);

        $text = trim(
            preg_replace('/\s+/', ' ', $text)
        );

        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . '...';
    }
}