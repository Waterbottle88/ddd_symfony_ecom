# Domain Architecture Analysis

## Aggregate Design & Boundaries

### Product Aggregate
**Root**: `Product`
**Boundary**: Single product entity with its properties
**Invariants**:
- Product name uniqueness (requires repository coordination)
- Quantity validation rules by product type
- Non-negative pricing

**Rationale**: Product is a standalone entity with clear boundaries. Name uniqueness is a cross-aggregate invariant that would be enforced at the application service level with repository checks.

### Order Aggregate
**Root**: `Order`
**Entities**: `OrderItem` (child entity)
**Boundary**: Order with all its items and invoice references
**Invariants**:
- Order editing only in NEW status
- Total calculation consistency
- Invoice lifecycle management

**Rationale**: Order contains OrderItems as they have no identity outside the order context. Invoice references are maintained for lifecycle management, but Invoice remains a separate aggregate.

### Invoice Aggregate
**Root**: `Invoice`
**Boundary**: Single invoice with payment processing
**Invariants**:
- Payment amount exactness
- Single payment per invoice
- Status transition rules

**Rationale**: Invoice is a separate aggregate because it has its own lifecycle and can exist independently. The relationship with Order is maintained through reference, not composition.

## Cross-Aggregate Invariants

### Product Name Uniqueness
**Challenge**: Names must be unique across all products
**Solution**: Application service coordination with repository
```php
// In Application Service
if ($this->productRepository->existsByName($productName)) {
    throw new DuplicateProductNameException($productName->toString());
}
```

### Order-Invoice Consistency
**Challenge**: Multiple invoices per order with cancellation rules
**Solution**: Order aggregate manages invoice lifecycle
```php
// Order maintains invoice references and handles cancellation
private function cancelAllActiveInvoices(): void
{
    foreach ($this->invoices as $invoice) {
        if ($invoice->isNew()) {
            $invoice->cancel();
        }
    }
}
```

## Transactional Boundaries

### Creating New Invoice
**Scope**: Order aggregate operation
**Atomicity**: All previous invoices cancelled + new invoice created + status updated
```php
public function issueInvoice(): Invoice
{
    // Single transaction scope:
    // 1. Cancel existing invoices
    // 2. Create new invoice
    // 3. Update order status
}
```

### Payment Processing
**Scope**: Invoice aggregate operation
**Atomicity**: Payment validation + invoice status + order status update
```php
public function pay(PaymentMethod $paymentMethod): void
{
    // Single transaction scope:
    // 1. Validate payment amount
    // 2. Process payment
    // 3. Update invoice status
    // 4. Update order status
}
```

## Domain Events (Future Enhancement)

While not implemented in this solution, production systems would benefit from domain events:

```php
// Example domain events
class InvoicePaidEvent
{
    public function __construct(
        public readonly Invoice $invoice,
        public readonly \DateTimeImmutable $paidAt
    ) {}
}

class OrderStatusChangedEvent
{
    public function __construct(
        public readonly Order $order,
        public readonly OrderStatus $previousStatus,
        public readonly OrderStatus $newStatus
    ) {}
}
```

## Persistence Considerations

### Aggregate Storage
- **Product**: Single table with value object columns
- **Order**: Order table + OrderItem table (one-to-many)
- **Invoice**: Single table with order reference

### Repository Patterns
```php
interface ProductRepositoryInterface
{
    public function save(Product $product): void;
    public function findByName(ProductName $name): ?Product;
    public function existsByName(ProductName $name): bool;
}

interface OrderRepositoryInterface
{
    public function save(Order $order): void;
    public function findById(OrderId $id): ?Order;
}
```

### Value Object Mapping
```php
// Doctrine embeddable for Money
#[Embeddable]
class Money
{
    #[Column(type: 'integer')]
    private int $amount;

    #[Column(type: 'string', length: 3)]
    private string $currency;
}
```

## Scalability Considerations

### Performance Optimizations
1. **Eager Loading**: Order with OrderItems in single query
2. **Lazy Loading**: Invoice collection loaded on demand
3. **Caching**: Product catalog with name uniqueness cache
4. **Event Sourcing**: For audit trail and performance (future)

### Consistency Models
1. **Strong Consistency**: Within aggregate boundaries
2. **Eventual Consistency**: Cross-aggregate operations via events
3. **Optimistic Locking**: For concurrent modifications

This architecture provides a solid foundation that can scale from simple CRUD operations to complex event-driven systems while maintaining domain integrity and business rule enforcement.