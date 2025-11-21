<?php
require_once __DIR__ . '/PhysicalProduct.php';
require_once __DIR__ . '/DigitalProduct.php';
require_once __DIR__ . '/ServiceProduct.php';

/**
 * Patrón FACTORY METHOD
 * Crea diferentes tipos de productos sin exponer la lógica de creación
 */
class ProductFactory {
    
    /**
     * Método Factory que crea el tipo de producto apropiado
     * @param string $type Tipo de producto (physical, digital, service)
     * @param array $data Datos del producto
     * @return Product Instancia del producto correspondiente
     * @throws Exception Si el tipo no es válido
     */
    public static function createProduct($type, $data = []) {
        $data['product_type'] = $type;
        
        switch($type) {
            case 'physical':
                return new PhysicalProduct($data);
            
            case 'digital':
                return new DigitalProduct($data);
            
            case 'service':
                return new ServiceProduct($data);
            
            default:
                throw new Exception("Tipo de producto no válido: {$type}");
        }
    }
    
    /**
     * Crea un producto desde los datos de la base de datos
     * @param array $dbData Datos de la base de datos
     * @return Product Instancia del producto
     */
    public static function createFromDatabase($dbData) {
        return self::createProduct($dbData['product_type'], $dbData);
    }
}