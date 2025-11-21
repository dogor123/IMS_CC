<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/ProductFactory.php';

class ProductController {
    private $db;
    
    public function __construct() {
        // Usa el patrón Singleton para obtener la conexión
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtiene todos los productos
     */
    public function getAllProducts() {
        $stmt = $this->db->query("SELECT * FROM products ORDER BY created_at DESC");
        $productsData = $stmt->fetchAll();
        
        $products = [];
        foreach ($productsData as $data) {
            // Usa Factory Method para crear el producto apropiado
            $products[] = ProductFactory::createFromDatabase($data);
        }
        
        return $products;
    }
    
    /**
     * Obtiene un producto por ID
     */
    public function getProductById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        
        if ($data) {
            return ProductFactory::createFromDatabase($data);
        }
        return null;
    }
    
    /**
     * Crea un nuevo producto
     */
    public function createProduct($data) {
        $sql = "INSERT INTO products (name, description, product_type, cost_price, sale_price, 
                stock, sku, image_url, weight, download_link, duration_hours) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['product_type'],
            $data['cost_price'],
            $data['sale_price'],
            $data['stock'] ?? 0,
            $data['sku'],
            $data['image_url'] ?? null,
            $data['weight'] ?? null,
            $data['download_link'] ?? null,
            $data['duration_hours'] ?? null
        ]);
    }
    
    /**
     * Actualiza un producto existente
     */
    public function updateProduct($id, $data) {
        $sql = "UPDATE products SET name = ?, description = ?, product_type = ?, 
                cost_price = ?, sale_price = ?, stock = ?, sku = ?, image_url = ?,
                weight = ?, download_link = ?, duration_hours = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['product_type'],
            $data['cost_price'],
            $data['sale_price'],
            $data['stock'],
            $data['sku'],
            $data['image_url'] ?? null,
            $data['weight'] ?? null,
            $data['download_link'] ?? null,
            $data['duration_hours'] ?? null,
            $id
        ]);
    }
    
    /**
     * Elimina un producto
     */
    public function deleteProduct($id) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Verifica si el SKU ya existe
     */
    public function skuExists($sku, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE sku = ? AND id != ?");
            $stmt->execute([$sku, $excludeId]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE sku = ?");
            $stmt->execute([$sku]);
        }
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Patrón PROTOTYPE: Clona un producto existente
     */
    public function cloneProduct($id) {
        $product = $this->getProductById($id);
        
        if (!$product) {
            return ['success' => false, 'message' => 'Producto no encontrado'];
        }
        
        // Clonar el producto usando el patrón Prototype
        $clonedProduct = $product->clone();
        
        // Preparar datos para inserción
        $data = $clonedProduct->toArray();
        
        // Agregar campos específicos según el tipo
        if ($product->getProductType() === 'physical') {
            $data['weight'] = method_exists($product, 'getWeight') ? $product->getWeight() : null;
        } elseif ($product->getProductType() === 'digital') {
            $data['download_link'] = method_exists($product, 'getDownloadLink') ? $product->getDownloadLink() : null;
        } elseif ($product->getProductType() === 'service') {
            $data['duration_hours'] = method_exists($product, 'getDurationHours') ? $product->getDurationHours() : null;
        }
        
        // Crear el producto clonado
        if ($this->createProduct($data)) {
            return ['success' => true, 'message' => 'Producto clonado exitosamente', 'sku' => $data['sku']];
        }
        
        return ['success' => false, 'message' => 'Error al clonar el producto'];
    }
}