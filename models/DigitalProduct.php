<?php
require_once __DIR__ . '/Product.php';

/**
 * Producto Digital - Implementación concreta del patrón Factory Method
 */
class DigitalProduct extends Product {
    private $downloadLink;
    
    public function __construct($data = []) {
        parent::__construct($data);
        $this->downloadLink = $data['download_link'] ?? '';
        $this->productType = 'digital';
        // Los productos digitales tienen stock ilimitado
        $this->stock = 999;
    }
    
    public function getSpecificInfo() {
        return [
            'type' => 'Producto Digital',
            'download_link' => $this->downloadLink,
            'requires_shipping' => false,
            'instant_delivery' => true
        ];
    }
    
    public function getDownloadLink() { return $this->downloadLink; }
    public function setDownloadLink($link) { $this->downloadLink = $link; }
}