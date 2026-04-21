<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Record\Builder;

use YoastSeoForTypo3\YoastSeo\Record\Record;

abstract class AbstractBuilder
{
    protected Record $record;

    public function setRecord(Record $record): self
    {
        $this->record = $record;
        return $this;
    }

    abstract public function build(): void;

    /**
     * @return array<string, mixed>|string[]
     */
    abstract public function getResult(): array;
}
