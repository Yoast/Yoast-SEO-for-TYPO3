<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\Translations\Utility;

class FileSaverUtility
{
    public static function saveToFile(string $filePath, string $content): void
    {
        file_put_contents($filePath, $content);
    }
}
