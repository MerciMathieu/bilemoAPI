<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use OpenApi\Annotations as OA;

class ProductController extends ExtendedAbstractController
{
    /**
     * @Route("/api/products", name="products", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @OA\Response(
     *   response=200,
     *   description="Returns the products' list"
     * )
     */
    public function getProducts(ProductRepository $productRepository, SerializerInterface $serializer, Request $request): Response
    {
        $products = $productRepository->findAll();

        $productsJson = $serializer->serialize(
            $products,
            'json',
            SerializationContext::create()->setGroups(
                ['products_list']
            )
        );

        $response = new Response($productsJson, 200, ['Content-Type' => 'application/json']);

        $this->cacheInit($response, $request);

        return $response;
    }

    /**
     * @Route("/api/products/{id<\d+>}", name="product_details", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @OA\Response(
     *   response=200,
     *   description="Returns the products' details"
     * )
     */
    public function getProductDetails(Product $product, SerializerInterface $serializer, Request $request): Response
    {
        $productJson = $serializer->serialize(
            $product,
            'json',
            SerializationContext::create()->setGroups(
                ['product_details']
            )
        );

        $response = new Response($productJson, 200, ['Content-Type' => 'application/json']);

        $this->cacheInit($response, $request);

        return $response;
    }
}
