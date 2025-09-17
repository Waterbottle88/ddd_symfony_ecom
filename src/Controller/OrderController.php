<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\DTO\AddProductToOrderRequest;
use App\Application\Service\InvoiceService;
use App\Application\Service\OrderService;
use App\Application\Service\ProductService;
use App\Domain\Exception\DomainException;
use App\Domain\Exception\ProductNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    public function __construct(
        private OrderService $orderService,
        private ProductService $productService,
        private InvoiceService $invoiceService
    ) {
    }

    #[Route('/orders', name: 'orders_list')]
    public function list(): Response
    {
        $ordersWithIds = $this->orderService->getAllOrdersWithIds();

        return $this->render('order/list.html.twig', [
            'orders' => $ordersWithIds,
        ]);
    }

    #[Route('/orders/create', name: 'orders_create')]
    public function create(): Response
    {
        $orderId = $this->orderService->createOrder();

        return $this->redirectToRoute('orders_view', ['id' => $orderId]);
    }

    #[Route('/orders/{id}', name: 'orders_view', requirements: ['id' => '\d+'])]
    public function view(int $id): Response
    {
        $order = $this->orderService->getOrder($id);
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        $products = $this->productService->getAllProducts();

        return $this->render('order/view.html.twig', [
            'order' => $order,
            'orderId' => $id,
            'products' => $products,
        ]);
    }

    #[Route('/orders/{id}/add-product', name: 'orders_add_product', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function addProduct(int $id, Request $request): Response
    {
        try {
            $addProductRequest = new AddProductToOrderRequest(
                orderId: $id,
                productName: $request->request->get('product_name', ''),
                quantity: (float) $request->request->get('quantity', 0)
            );

            $product = $this->productService->findProductByName($addProductRequest->productName);
            if (!$product) {
                throw new ProductNotFoundException($addProductRequest->productName);
            }

            $this->orderService->addProductToOrderFromRequest($addProductRequest, $product);
            $this->addFlash('success', 'Product added to order successfully!');

        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', 'An unexpected error occurred: ' . $e->getMessage());
        }

        return $this->redirectToRoute('orders_view', ['id' => $id]);
    }

    #[Route('/orders/{id}/invoice', name: 'orders_create_invoice', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function createInvoice(int $id): Response
    {
        try {
            $order = $this->orderService->getOrder($id);
            if (!$order) {
                throw $this->createNotFoundException('Order not found');
            }

            $invoice = $this->invoiceService->createInvoiceForOrder($order);
            $this->addFlash('success', 'Invoice created successfully!');

        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
        }

        return $this->redirectToRoute('orders_view', ['id' => $id]);
    }

    #[Route('/orders/{id}/pay', name: 'orders_pay_invoice', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function payInvoice(int $id): Response
    {
        try {
            $order = $this->orderService->getOrder($id);
            if (!$order) {
                throw $this->createNotFoundException('Order not found');
            }

            $invoices = $order->getInvoices();
            $activeInvoice = null;

            foreach ($invoices as $invoice) {
                if ($invoice->isNew()) {
                    $activeInvoice = $invoice;
                    break;
                }
            }

            if (!$activeInvoice) {
                $this->addFlash('error', 'No active invoice found to pay');
                return $this->redirectToRoute('orders_view', ['id' => $id]);
            }

            $this->invoiceService->payInvoice($activeInvoice);
            $this->addFlash('success', 'Invoice paid successfully!');

        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
        }

        return $this->redirectToRoute('orders_view', ['id' => $id]);
    }
}