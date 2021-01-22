<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends ExtendedAbstractController
{
    /**
     * @Route("/api/products", name="products", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function getProducts(ProductRepository $productRepository, SerializerInterface $serializer): Response
    {
        $products = $productRepository->findAll();
        $productsJson = $serializer->serialize($products, 'json', [
            'groups' => ['products_list']
        ]);

        return new Response($productsJson, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/api/products/{productId<\d+>}", name="product_details", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function getProductDetails(
        ProductRepository $productRepository,
        SerializerInterface $serializer,
        int $productId
    ): Response {
        $product = $productRepository->find($productId);

        if (!$product || $product === null) {
            throw $this->createNotFoundException();
        }

        $productJson = $serializer->serialize($product, 'json', [
            'groups' => ['product_details']
        ]);

        return new Response($productJson, 200, ['Content-Type' => 'application/json']);
    }
}
