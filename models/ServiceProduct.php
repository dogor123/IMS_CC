<?php
require_once __DIR__ . '/Product.php';

/**
 * Servicio - Implementación concreta del patrón Factory Method
 */
class ServiceProduct extends Product {
    private $durationHours;
    
    public function __construct($data = []) {
        parent::__construct($data);
        $this->durationHours = $data['duration_hours'] ?? 1;
        $this->productType = 'service';
        // Los servicios tienen stock ilimitado
        $this->stock = 999;
    }
    
    public function getSpecificInfo() {
        return [
            'type' => 'Servicio',
            'duration_hours' => $this->durationHours,
            'requires_shipping' => false,
            'hourly_rate' => $this->calculateHourlyRate()
        ];
    }
    
    private function calculateHourlyRate() {
        return $this->salePrice / $this->durationHours;
    }
    
    public function getDurationHours() { return $this->durationHours; }
    public function setDurationHours($hours) { $this->durationHours = $hours; }
}