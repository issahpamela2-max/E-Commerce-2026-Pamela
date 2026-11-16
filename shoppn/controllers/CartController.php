<?php
require_once __DIR__ . '/../classes/CartClass.php';

class CartController {
    private $model;

    public function __construct() {
        $this->model = new CartClass();
    }

    public function isInCart($product_id, $ip, $customer_id = null) {
        return $this->model->isInCart($product_id, $ip, $customer_id);
    }

    public function addToCart($product_id, $qty, $ip, $customer_id = null) {
        return $this->model->addToCart($product_id, $qty, $ip, $customer_id);
    }

    public function getCartItems($ip, $customer_id = null) {
        return $this->model->getCartItems($ip, $customer_id);
    }

    public function getCartCount($ip, $customer_id = null) {
        return $this->model->getCartCount($ip, $customer_id);
    }

    public function getCartTotal($ip, $customer_id = null) {
        return $this->model->getCartTotal($ip, $customer_id);
    }

    public function removeFromCart($product_id, $ip, $customer_id = null) {
        return $this->model->removeFromCart($product_id, $ip, $customer_id);
    }

    public function updateQty($product_id, $new_qty, $ip, $customer_id = null) {
        return $this->model->updateQty($product_id, $new_qty, $ip, $customer_id);
    }

    public function mergeGuestCart($ip, $customer_id) {
        return $this->model->mergeGuestCart($ip, $customer_id);
    }

    // ---------- PAYMENT ----------

    public function processPayment($customer_id, $amount) {
        return $this->model->processPayment($customer_id, $amount);
    }

    public function getOrderById($order_id) {
        return $this->model->getOrderById($order_id);
    }

    public function getOrderItems($order_id) {
        return $this->model->getOrderItems($order_id);
    }

    public function getOrdersByCustomer($customer_id) {
        return $this->model->getOrdersByCustomer($customer_id);
    }

    public function getAllOrders() {
        return $this->model->getAllOrders();
    }

    public function updateOrderStatus($order_id, $status) {
        return $this->model->updateOrderStatus($order_id, $status);
    }

    public function getAvailableOrderStatuses($current_status) {
        return $this->model->getAvailableOrderStatuses($current_status);
    }
}