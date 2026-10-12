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

    public function getCustomerById($id) {
        return $this->model->getCustomerById($id);
    }

    public function updateProfile($id, $name, $country, $city, $contact) {
        return $this->model->updateProfile($id, $name, $country, $city, $contact);
    }

    // Verify the current password, then change it
    public function changePassword($id, $current_pass, $new_pass) {
        $customer = $this->model->getCustomerById($id);

        if (!$customer) {
            return ['success' => false, 'error' => 'Account not found.'];
        }

        if (!password_verify($current_pass, $customer['customer_pass'])) {
            return ['success' => false, 'error' => 'Your current password is incorrect.'];
        }

        if ($this->model->updatePassword($id, $new_pass)) {
            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Could not change password. Please try again.'];
    }
}