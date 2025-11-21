<?php
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/OrderController.php';
require_once __DIR__ . '/ObserverPattern.php';
require_once __DIR__ . '/StrategyPattern.php';
require_once __DIR__ . '/DecoratorPattern.php';

/**
 * Patrón FACADE
 * Proporciona una interfaz simplificada para operaciones complejas del sistema
 */
class InventorySystemFacade {
    private $productController;
    private $orderController;
    private $inventorySubject;
    private $priceCalculator;
    
    public function __construct() {
        $this->productController = new ProductController();
        $this->orderController = new OrderController();
        
        // Configurar observadores
        $this->inventorySubject = new InventorySubject();
        $this->inventorySubject->attach(new LowStockObserver());
        $this->inventorySubject->attach(new AuditLogObserver());
        $this->inventorySubject->attach(new OrderNotificationObserver());
        
        // Configurar calculadora de precios con estrategias por defecto
        $this->priceCalculator = new PriceCalculator();
    }
    
    /**
     * Crea un producto completo con todas las validaciones
     */
    public function createProductComplete($data) {
        try {
            // Validar SKU único
            if ($this->productController->skuExists($data['sku'])) {
                return ['success' => false, 'message' => 'El SKU ya existe'];
            }
            
            // Crear el producto
            $result = $this->productController->createProduct($data);
            
            if ($result) {
                // Notificar creación
                $this->inventorySubject->notify('product_created', $data);
                return ['success' => true, 'message' => 'Producto creado exitosamente'];
            }
            
            return ['success' => false, 'message' => 'Error al crear el producto'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Actualiza el stock y verifica si está bajo
     */
    public function updateStockAndNotify($productId, $newStock) {
        $product = $this->productController->getProductById($productId);
        
        if (!$product) {
            return ['success' => false, 'message' => 'Producto no encontrado'];
        }
        
        // Actualizar stock
        $data = [
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'product_type' => $product->getProductType(),
            'cost_price' => $product->getCostPrice(),
            'sale_price' => $product->getSalePrice(),
            'stock' => $newStock,
            'sku' => $product->getSku(),
            'image_url' => $product->getImageUrl(),
            'weight' => method_exists($product, 'getWeight') ? $product->getWeight() : null,
            'download_link' => method_exists($product, 'getDownloadLink') ? $product->getDownloadLink() : null,
            'duration_hours' => method_exists($product, 'getDurationHours') ? $product->getDurationHours() : null
        ];
        
        $result = $this->productController->updateProduct($productId, $data);
        
        // Verificar stock bajo (menos de 10 unidades)
        if ($newStock < 10 && $product->getProductType() === 'physical') {
            $this->inventorySubject->notify('low_stock', [
                'product_id' => $productId,
                'product_name' => $product->getName(),
                'stock' => $newStock
            ]);
        }
        
        return ['success' => $result, 'message' => $result ? 'Stock actualizado' : 'Error al actualizar'];
    }
    
    /**
     * Crea un pedido completo con cálculos avanzados
     */
    public function createOrderWithCalculations(
        $customerName,
        $customerEmail,
        $products,
        $notes = '',
        $shippingType = 'standard',
        $discountType = 'none',
        $discountValue = 0,
        $addTax = true,
        $addInsurance = false
    ) {
        try {
            // Configurar estrategia de envío
            switch($shippingType) {
                case 'express':
                    $this->priceCalculator->setShippingStrategy(new ExpressShipping());
                    break;
                case 'economy':
                    $this->priceCalculator->setShippingStrategy(new EconomyShipping());
                    break;
                default:
                    $this->priceCalculator->setShippingStrategy(new StandardShipping());
            }
            
            // Configurar estrategia de descuento
            switch($discountType) {
                case 'percentage':
                    $this->priceCalculator->setDiscountStrategy(new PercentageDiscount($discountValue));
                    break;
                case 'fixed':
                    $this->priceCalculator->setDiscountStrategy(new FixedAmountDiscount($discountValue));
                    break;
                default:
                    $this->priceCalculator->setDiscountStrategy(new NoDiscount());
            }
            
            // Crear el pedido
            $result = $this->orderController->createOrder($customerName, $customerEmail, $products, $notes);
            
            if ($result['success']) {
                // Notificar creación de pedido
                $this->inventorySubject->notify('order_created', [
                    'order_number' => $result['order_number'],
                    'customer_name' => $customerName,
                    'total' => 0 // Se calculará después
                ]);
                
                return [
                    'success' => true,
                    'message' => 'Pedido creado exitosamente',
                    'order_id' => $result['order_id'],
                    'order_number' => $result['order_number']
                ];
            }
            
            return $result;
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Obtiene un reporte completo del inventario
     */
    public function getInventoryReport() {
        $products = $this->productController->getAllProducts();
        $orders = $this->orderController->getAllOrders();
        
        $report = [
            'total_products' => count($products),
            'total_orders' => count($orders),
            'low_stock_products' => [],
            'top_revenue_products' => [],
            'total_inventory_value' => 0,
            'products_by_type' => [
                'physical' => 0,
                'digital' => 0,
                'service' => 0
            ]
        ];
        
        foreach ($products as $product) {
            // Contabilizar por tipo
            $type = $product->getProductType();
            $report['products_by_type'][$type]++;
            
            // Productos con stock bajo
            if ($product->getProductType() === 'physical' && $product->getStock() < 10) {
                $report['low_stock_products'][] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'stock' => $product->getStock()
                ];
            }
            
            // Valor del inventario
            $report['total_inventory_value'] += $product->getCostPrice() * $product->getStock();
        }
        
        return $report;
    }
    
    /**
     * Calcula el precio total de un pedido con todas las opciones
     */
    public function calculateOrderPrice($subtotal, $weight = 0, $options = []) {
        // Crear pedido base
        $order = new BasicOrder($subtotal, "Pedido");
        
        // Aplicar decoradores según opciones
        if ($options['add_tax'] ?? true) {
            $order = new TaxDecorator($order, $options['tax_rate'] ?? 0.16);
        }
        
        if ($options['add_insurance'] ?? false) {
            $order = new InsuranceDecorator($order, $options['insurance_cost'] ?? 10);
        }
        
        if ($options['gift_wrap'] ?? false) {
            $order = new GiftWrapDecorator($order, $options['wrap_cost'] ?? 5);
        }
        
        if ($options['urgent_shipping'] ?? false) {
            $order = new UrgentShippingDecorator($order, $options['urgent_fee'] ?? 25);
        }
        
        if (($options['tip_percentage'] ?? 0) > 0) {
            $order = new TipDecorator($order, $options['tip_percentage']);
        }
        
        return [
            'total' => $order->getTotal(),
            'description' => $order->getDescription()
        ];
    }
}