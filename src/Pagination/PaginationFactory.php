<?php


namespace App\Pagination;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class PaginationFactory
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function createCollection(QueryBuilder $query, Request $request, $route, array $routeParams = [])
    {
        $page = (int) $request->query->get('page', 1);

        $adapter = new QueryAdapter($query, false);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(5);
        $pagerfanta->setCurrentPage($page);
        $products = [];
        foreach ($pagerfanta->getCurrentPageResults() as $product) {
            $products[] = $product;
        }

        $paginatedCollection = new PaginatedCollection(
            $products,
            $pagerfanta->getNbResults(),
            $page
        );

        $createLinkUrl = function ($targetPage) use ($route, $routeParams) {
            return $this->router->generate($route, array_merge(
                $routeParams,
                array('page' => $targetPage)
            ));
        };

        $paginatedCollection->addLink('self', $createLinkUrl($page));
        $paginatedCollection->addLink('first', $createLinkUrl(1));
        $paginatedCollection->addLink('last', $createLinkUrl($pagerfanta->getNbPages()));

        if ($pagerfanta->hasNextPage()) {
            $paginatedCollection->addLink('next', $createLinkUrl($pagerfanta->getNextPage()));
        }
        if ($pagerfanta->hasPreviousPage()) {
            $paginatedCollection->addLink('prev', $createLinkUrl($pagerfanta->getPreviousPage()));
        }

        return $paginatedCollection;
    }
}
