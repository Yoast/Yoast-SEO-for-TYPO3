<?php

/**
 * This file is part of the "yoast_seo" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace YoastSeoForTypo3\Sitepackage\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use YoastSeoForTypo3\Sitepackage\Domain\Model\Minimal;
use YoastSeoForTypo3\Sitepackage\Domain\Repository\MinimalRepository;

final class RecordController extends ActionController
{
    public function __construct(
        private readonly MinimalRepository $minimalRepository,
    ) {}

    public function minimalListAction(): ResponseInterface
    {
        $this->view->assign('records', $this->minimalRepository->findAll());
        return $this->htmlResponse();
    }

    public function minimalDetailAction(Minimal $minimal): ResponseInterface
    {
        $this->view->assign('record', $minimal);
        return $this->htmlResponse();
    }
}
