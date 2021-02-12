<?php

namespace App\Controller;

use App\Entity\Product;
use App\Pagination\PaginationFactory;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/api/products", name="products", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @OA\Response(
     *   response=200,
     *   description="Returns the products' list",
     *   @Model(type=Product::class, groups={"products_list"})
     * )
     * @Cache(maxage="1 hour", public=true)
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @param Request $request
     * @param PaginationFactory $paginationFactory
     * @return Response
     */
    public function getProducts(ProductRepository $productRepository, SerializerInterface $serializer, Request $request, PaginationFactory $paginationFactory): Response
    {
        $query = $productRepository->findAllQueryBuilder();

        $paginatedCollection = $paginationFactory->createCollection($query, $request, 'products');
        $productsJson = $serializer->serialize(
            $paginatedCollection,
            'json',
        //  SerializationContext::create()->setGroups(['products_list'])
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
