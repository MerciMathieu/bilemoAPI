<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;

class ProductController extends ExtendedAbstractController
{
    /**
     * @Route("/api/products", name="products", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function getProducts(ProductRepository $productRepository, SerializerInterface $serializer): Response
    {
        $products = $productRepository->findAll();

        $productsJson = $serializer->serialize(
            $products,
            'json',
            SerializationContext::create()->setGroups(
                ['products_list']
            )
        );

        return new Response($productsJson, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/api/products/{id<\d+>}", name="product_details", methods={"GET"})
     * @IsGranted("ROLE_USER")
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

        return new Response($productJson, 200, ['Content-Type' => 'application/json']);
    }
}
