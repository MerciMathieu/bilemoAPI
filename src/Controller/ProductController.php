<?php

namespace App\Controller;

use App\Entity\Product;
use App\Pagination\PaginatedCollection;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializationContext;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @OA\Tag(name="Products")
 */
class ProductController extends ExtendedAbstractController
{
    /**
     * @Route("/api/products/{page<\d+>?1}", name="products", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @OA\Response(
     *   response=200,
     *   description="Returns the products' list",
     *   @Model(type=Product::class, groups={"products_list"})
     * )
     * @Cache(maxage="1 hour", public=true)
     */
    public function getProducts(ProductRepository $productRepository, SerializerInterface $serializer, int $page): Response
    {
        $route = 'products';
        $routeParams = array();
        $createLinkUrl = function($targetPage) use ($route, $routeParams) {
            return $this->generateUrl($route, array_merge(
                $routeParams,
                array('page' => $targetPage)
            ));
        };

        $query = $productRepository->findAllQueryBuilder();
        $adapter = new QueryAdapter($query);
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

        $paginatedCollection->addLink('self', $createLinkUrl($page));
        $paginatedCollection->addLink('first', $createLinkUrl(1));
        $paginatedCollection->addLink('last', $createLinkUrl($pagerfanta->getNbPages()));

        if ($pagerfanta->hasNextPage()) {
            $paginatedCollection->addLink('next', $createLinkUrl($pagerfanta->getNextPage()));
        }
        if ($pagerfanta->hasPreviousPage()) {
            $paginatedCollection->addLink('prev', $createLinkUrl($pagerfanta->getPreviousPage()));
        }

        $productsJson = $serializer->serialize(
            $paginatedCollection,
            'json',
        );

        $response = new Response($productsJson, Response::HTTP_OK, ['Content-Type' => 'application/json']);

        return $response;
    }

    /**
     * @Route("/api/products/{id<\d+>}", name="product_details", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @OA\Response(
     *   response=200,
     *   description="Returns the products' details",
     *   @Model(type=Product::class, groups={"product_details"})
     * )
     * @Cache(maxage="1 hour", public=true)
     */
    public function getProductDetails(Product $product, SerializerInterface $serializer): Response
    {
        $productJson = $serializer->serialize(
            $product,
            'json',
            SerializationContext::create()->setGroups(
                ['product_details']
            )
        );

        $response = new Response($productJson, Response::HTTP_OK, ['Content-Type' => 'application/json']);

        return $response;
    }
}
