<?php

namespace App\Twig;

use App\Service\BreadcrumbService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BreadcrumbExtension extends AbstractExtension
{
    private $breadcrumbService;

    public function __construct(BreadcrumbService $breadcrumbService)
    {
        $this->breadcrumbService = $breadcrumbService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('breadcrumbs', [$this, 'getBreadcrumbs']),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbService->getBreadcrumbs();
    }
}