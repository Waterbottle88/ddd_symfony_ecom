<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Service\ProductService;
use App\Application\Service\OrderService;
use App\Domain\Product\ProductType;
use App\Infrastructure\Repository\InMemoryProductRepository;
use App\Infrastructure\Repository\InMemoryOrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DemoController extends AbstractController
{
    public function __construct(
        private ProductService $productService,
        private OrderService $orderService,
        private InMemoryProductRepository $productRepository,
        private InMemoryOrderRepository $orderRepository
    ) {
    }

    #[Route('/demo/setup', name: 'demo_setup')]
    public function setupDemoData(): Response
    {
        try {
            // Start the session explicitly
            $session = $this->container->get('request_stack')->getSession();
            $session->start();

            // Create demo products
            $this->productService->createProduct('iPhone 15', 999.99, ProductType::PIECE->value);
            $this->productService->createProduct('Samsung Galaxy S24', 899.99, ProductType::PIECE->value);
            $this->productService->createProduct('MacBook Pro', 2499.99, ProductType::PIECE->value);
            $this->productService->createProduct('Organic Rice', 2.50, ProductType::WEIGHT->value);
            $this->productService->createProduct('Premium Coffee', 15.75, ProductType::WEIGHT->value);
            $this->productService->createProduct('Olive Oil', 8.99, ProductType::WEIGHT->value);

            // Debug: Check how many products we have after creation
            $productCount = count($this->productService->getAllProducts());

            $this->addFlash('success', "🎉 Demo data created successfully! {$productCount} products added to the catalog.");
        } catch (\Exception $e) {
            $this->addFlash('error', 'Demo data setup failed: ' . $e->getMessage());
        }

        return $this->redirectToRoute('home');
    }

    #[Route('/demo/reset', name: 'demo_reset')]
    public function resetDemoData(): Response
    {
        try {
            // Clear all demo data
            $this->productRepository->clear();
            $this->orderRepository->clear();

            $this->addFlash('success', '🗑️ All demo data has been cleared successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Demo data reset failed: ' . $e->getMessage());
        }

        return $this->redirectToRoute('home');
    }
}