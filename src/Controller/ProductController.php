<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends ExtendedAbstractController
{
    /**
     * @Route("/bilemo/products", name="products", methods={"GET"})
     */
    public function getProducts(ProductRepository $productRepository, SerializerInterface $serializer): Response
    {
        $products = $productRepository->findAll();
        $productsJson = $serializer->serialize($products,'json', [
                'groups' => ['products_list']
            ]);

        return new Response($productsJson, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/bilemo/products/{productId<\d+>}", name="product_details", methods={"GET"})
     */
    public function getProductDetails(
        int $productId,
        ProductRepository $productRepository,
        SerializerInterface $serializer
    ): Response
    {
        $product = $productRepository->find($productId);
        if (!$product || $product === null) {
            return $this->throwJsonNotFoundException("Product $productId was not found");
        }

        $productJson = $serializer->serialize(
            $product,'json', [
                'groups' => ['product_details']
            ]);

        return new Response($productJson, 200, ['Content-Type' => 'application/json']);
    }
}
