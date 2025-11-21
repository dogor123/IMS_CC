<?php
/**
 * Clase abstracta Product (parte del patrón Factory Method)
 * Define la estructura base para todos los tipos de productos
 * 
 * PATRÓN PROTOTYPE: Implementa clonación de productos
 */
abstract class Product {
    protected $id;
    protected $name;
    protected $description;
    protected $productType;
    protected $costPrice;
    protected $salePrice;
    protected $stock;
    protected $sku;
    protected $imageUrl;
    protected $createdAt;
    protected $updatedAt;
    
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->productType = $data['product_type'] ?? '';
        $this->costPrice = $data['cost_price'] ?? 0;
        $this->salePrice = $data['sale_price'] ?? 0;
        $this->stock = $data['stock'] ?? 0;
        $this->sku = $data['sku'] ?? '';
        $this->imageUrl = $data['image_url'] ?? '';
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }
    
    // Método abstracto que debe ser implementado por las clases hijas
    abstract public function getSpecificInfo();
    
    /**
     * Patrón PROTOTYPE: Clona el producto actual
     * Útil para crear productos similares sin empezar desde cero
     */
    public function clone() {
        $cloned = clone $this;
        $cloned->id = null; // Nuevo producto no tiene ID
        $cloned->sku = $this->generateNewSku(); // Generar nuevo SKU
        $cloned->name = $this->name . ' (Copia)';
        return $cloned;
    }
    
    /**
     * Genera un nuevo SKU único para el producto clonado
     */
    private function generateNewSku() {
        return $this->sku . '-COPY-' . strtoupper(substr(uniqid(), -4));
    }
    
    /**
     * Convierte el producto a array para guardar en BD
     */
    public function toArray() {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'product_type' => $this->productType,
            'cost_price' => $this->costPrice,
            'sale_price' => $this->salePrice,
            'stock' => $this->stock,
            'sku' => $this->sku,
            'image_url' => $this->imageUrl
        ];
    }
    
    // Método para calcular el margen de ganancia
    public function getProfitMargin() {
        if ($this->costPrice == 0) return 0;
        return (($this->salePrice - $this->costPrice) / $this->costPrice) * 100;
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getDescription() { return $this->description; }
    public function getProductType() { return $this->productType; }
    public function getCostPrice() { return $this->costPrice; }
    public function getSalePrice() { return $this->salePrice; }
    public function getStock() { return $this->stock; }
    public function getSku() { return $this->sku; }
    public function getImageUrl() { return $this->imageUrl; }
    
    // Setters
    public function setId($id) { $this->id = $id; }
    public function setName($name) { $this->name = $name; }
    public function setDescription($description) { $this->description = $description; }
    public function setCostPrice($price) { $this->costPrice = $price; }
    public function setSalePrice($price) { $this->salePrice = $price; }
    public function setStock($stock) { $this->stock = $stock; }
    public function setSku($sku) { $this->sku = $sku; }
    public function setImageUrl($url) { $this->imageUrl = $url; }
}