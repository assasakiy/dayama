<?php

declare(strict_types=1);

namespace App\Services;

use Jaybizzle\CrawlerDetect\CrawlerDetect;

class CrawlerService
{
    /**
     * Determine if the current request is coming from a crawler/bot.
     */
    public static function isCrawler(?string $userAgent = null): bool
    {
        $crawlerDetect = new CrawlerDetect();
        
        $userAgent = $userAgent ?? request()->userAgent();

        if (empty($userAgent)) {
            return false; // Or true if you consider empty UA as bot
        }

        return $crawlerDetect->isCrawler($userAgent);
    }
}
