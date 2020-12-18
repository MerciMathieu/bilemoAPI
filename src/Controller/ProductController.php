<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/bilemo/products", name="products", methods={"GET"})
     */
    public function getProducts(ProductRepository $productRepository, SerializerInterface $serializer): Response
    {
        $products = $productRepository->findAll();
        $productsJson = $serializer->serialize(
            $products,
            'json',
            ['groups' => 'list_products']

        );

        return new Response($productsJson, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/bilemo/products/{productId<\d+>}", name="product_details", methods={"GET"})
     */
    public function getProductDetails(ProductRepository $productRepository, SerializerInterface $serializer, int $productId): Response
    {
        $product = $productRepository->find($productId);
        $productJson = $serializer->serialize(
            $product,
            'json',
            ['groups' => 'list_products_details']
        );

        return new Response($productJson, 200, ['Content-Type' => 'application/json']);
    }
}
