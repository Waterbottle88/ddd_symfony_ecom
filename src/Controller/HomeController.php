<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Service\OrderService;
use App\Application\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    public function __construct(
        private ProductService $productService,
        private OrderService $orderService
    ) {
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $products = $this->productService->getAllProducts();
        $ordersWithIds = $this->orderService->getAllOrdersWithIds();

        return $this->render('home/index.html.twig', [
            'products' => $products,
            'orders' => $ordersWithIds,
        ]);
    }
}