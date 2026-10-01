<?php
require_once __DIR__ . '/../classes/CustomerClass.php';

class CustomerController {
    private $model;

    public function __construct() {
        $this->model = new CustomerClass();
    }

    public function register($data) {
        if ($this->model->emailExists($data['email'])) {
            return ['success' => false, 'error' => 'Email already registered.'];
        }

        $id = $this->model->addCustomer(
            $data['name'],
            $data['email'],
            $data['pass'],
            $data['country'],
            $data['city'],
            $data['contact']
        );

        if ($id === false) {
            return ['success' => false, 'error' => 'Registration failed. Please try again.'];
        }

        return ['success' => true, 'customer_id' => $id];
    }

    public function login($email, $pass) {
        $row = $this->model->login($email, $pass);

        if ($row === false) {
            return ['success' => false, 'error' => 'Invalid email or password.'];
        }

        return ['success' => true, 'customer' => $row];
    }
}