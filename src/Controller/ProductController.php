<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/bilemo/products", name="products", methods={"GET"})
     */
    public function getProducts(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->json($products);
    }

    /**
     * @Route("/bilemo/products/{id}", name="product_details", methods={"GET"})
     */
    public function getProductDetails(ProductRepository $productRepository, int $id): Response
    {
        $product = $productRepository->findOneBy($id);

        return $this->json($product);
    }
}
