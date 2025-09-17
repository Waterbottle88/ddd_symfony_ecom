<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\DTO\CreateProductRequest;
use App\Application\Service\ProductService;
use App\Domain\Exception\DomainException;
use App\Domain\Exception\DuplicateProductNameException;
use App\Domain\Exception\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    public function __construct(
        private ProductService $productService
    ) {
    }

    #[Route('/products', name: 'products_list')]
    public function list(): Response
    {
        $products = $this->productService->getAllProducts();

        return $this->render('product/list.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/products/create', name: 'products_create')]
    public function create(Request $request): Response
    {
        $error = null;
        $success = null;

        if ($request->isMethod('POST')) {
            try {
                $createRequest = new CreateProductRequest(
                    name: $request->request->get('name', ''),
                    price: (float) $request->request->get('price', 0),
                    type: $request->request->get('type', '')
                );

                $product = $this->productService->createProductFromRequest($createRequest);
                $success = sprintf('Product "%s" created successfully!', $product->getName());

            } catch (DomainException $e) {
                $error = $e->getMessage();
            } catch (\Exception $e) {
                $error = 'An unexpected error occurred: ' . $e->getMessage();
            }
        }

        return $this->render('product/create.html.twig', [
            'error' => $error,
            'success' => $success,
        ]);
    }
}