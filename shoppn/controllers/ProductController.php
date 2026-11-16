<?php
require_once __DIR__ . '/../classes/ProductClass.php';

class ProductController {
    private $model;

    public function __construct() {
        $this->model = new ProductClass();
    }

    // ---------- BRANDS ----------

    public function addBrand($name) {
        return $this->model->addBrand($name);
    }

    public function getAllBrands() {
        return $this->model->getAllBrands();
    }

    public function getBrandById($id) {
        return $this->model->getBrandById($id);
    }

    public function updateBrand($id, $name) {
        return $this->model->updateBrand($id, $name);
    }

    // ---------- CATEGORIES ----------

    public function addCategory($name) {
        return $this->model->addCategory($name);
    }

    public function getAllCategories() {
        return $this->model->getAllCategories();
    }

    public function getCategoryById($id) {
        return $this->model->getCategoryById($id);
    }

    public function updateCategory($id, $name) {
        return $this->model->updateCategory($id, $name);
    }

    // ---------- PRODUCTS ----------

    public function addProduct($cat, $brand, $title, $price, $desc, $image, $keywords) {
        return $this->model->addProduct($cat, $brand, $title, $price, $desc, $image, $keywords);
    }

    public function updateProduct($id, $cat, $brand, $title, $price, $desc, $image, $keywords) {
        return $this->model->updateProduct($id, $cat, $brand, $title, $price, $desc, $image, $keywords);
    }

    public function getProductById($id) {
        return $this->model->getProductById($id);
    }

    public function getAllProducts($limit = null, $offset = 0, $sort = 'title_asc') {
        return $this->model->getAllProducts($limit, $offset, $sort);
    }

    public function countAllProducts() {
        return $this->model->countAllProducts();
    }

    // ---------- CUSTOMER-FACING ----------

    public function getFeaturedProducts($limit = 6) {
        return $this->model->getFeaturedProducts($limit);
    }

    public function getProductsByCategory($cat_id, $limit = null, $offset = 0, $sort = 'title_asc') {
        return $this->model->getProductsByCategory($cat_id, $limit, $offset, $sort);
    }

    public function getProductsByBrand($brand_id, $limit = null, $offset = 0, $sort = 'title_asc') {
        return $this->model->getProductsByBrand($brand_id, $limit, $offset, $sort);
    }

    public function countProductsByCategory($cat_id) {
        return $this->model->countProductsByCategory($cat_id);
    }

    public function countProductsByBrand($brand_id) {
        return $this->model->countProductsByBrand($brand_id);
    }

    public function searchProducts($query, $limit = null, $offset = 0, $sort = 'title_asc') {
        return $this->model->searchProducts($query, $limit, $offset, $sort);
    }

    public function countSearchProducts($query) {
        return $this->model->countSearchProducts($query);
    }

    // ---------- DELETES ----------

    public function deleteBrand($id) {
        return $this->model->deleteBrand($id);
    }

    public function deleteCategory($id) {
        return $this->model->deleteCategory($id);
    }

    public function deleteProduct($id) {
        return $this->model->deleteProduct($id);
    }
}