<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class BreadcrumbService
{
    private $requestStack;
    private $router;

    public function __construct(RequestStack $requestStack, RouterInterface $router)
    {
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    public function getBreadcrumbs(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $pathInfo = $request->getPathInfo();

        // Divise le chemin en segments
        $pathSegments = array_filter(explode('/', $pathInfo));

        // Initialise le tableau de breadcrumbs avec la page d'accueil
        $breadcrumbs = [
            ['title' => 'Accueil', 'path' => $this->router->generate('app_accueil_prof')]
        ];

        $currentPath = '';

        // Construit le fil d'ariane
        foreach ($pathSegments as $segment) {
            $currentPath .= '/' . $segment;

            // Convertit le segment en titre lisible
            $title = ucwords(str_replace(['-', '_'], ' ', $segment));

            // Essaie de générer une route (si elle existe)
            try {
                $path = $this->router->generate('app_' . str_replace('-', '_', $segment));
            } catch (\Exception $e) {
                $path = $currentPath;
            }

            $breadcrumbs[] = [
                'title' => $title,
                'path' => $path
            ];
        }

        return $breadcrumbs;
    }
}