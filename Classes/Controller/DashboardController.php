<?php

declare(strict_types=1);

namespace YoastSeoForTypo3\YoastSeo\Controller;

use Psr\Http\Message\ResponseInterface;

class DashboardController extends AbstractBackendController
{
    public function indexAction(): ResponseInterface
    {
        return $this->returnResponse();
    }
}
