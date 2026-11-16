<?php
require_once __DIR__ . '/../core/db_class.php';

class ProductClass extends Database {

    // ---------- BRANDS ----------

    public function addBrand($name) {
        $stmt = $this->conn->prepare("INSERT INTO brands (brand_name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getAllBrands() {
        $result = $this->conn->query("SELECT * FROM brands ORDER BY brand_name ASC");

        $brands = [];
        while ($row = $result->fetch_assoc()) {
            $brands[] = $row;
        }
        return $brands;
    }

    public function getBrandById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM brands WHERE brand_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ? $row : false;
    }

    public function updateBrand($id, $name) {
        $stmt = $this->conn->prepare("UPDATE brands SET brand_name = ? WHERE brand_id = ?");
        $stmt->bind_param("si", $name, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // Returns false if products still reference this brand; returns 'in_use' string as signal.
    public function deleteBrand($id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM products WHERE product_brand = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($row['cnt'] > 0) {
            return 'in_use';
        }

        $stmt = $this->conn->prepare("DELETE FROM brands WHERE brand_id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // ---------- CATEGORIES ----------

    public function addCategory($name) {
        $stmt = $this->conn->prepare("INSERT INTO categories (cat_name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function getAllCategories() {
        $result = $this->conn->query("SELECT * FROM categories ORDER BY cat_name ASC");

        $cats = [];
        while ($row = $result->fetch_assoc()) {
            $cats[] = $row;
        }
        return $cats;
    }

    public function getCategoryById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE cat_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ? $row : false;
    }

    public function updateCategory($id, $name) {
        $stmt = $this->conn->prepare("UPDATE categories SET cat_name = ? WHERE cat_id = ?");
        $stmt->bind_param("si", $name, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function deleteCategory($id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM products WHERE product_cat = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($row['cnt'] > 0) {
            return 'in_use';
        }

        $stmt = $this->conn->prepare("DELETE FROM categories WHERE cat_id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // ---------- PRODUCTS ----------

    public function addProduct($cat, $brand, $title, $price, $desc, $image, $keywords) {
        $stmt = $this->conn->prepare(
            "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("iisdsss", $cat, $brand, $title, $price, $desc, $image, $keywords);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function updateProduct($id, $cat, $brand, $title, $price, $desc, $image, $keywords) {
        $stmt = $this->conn->prepare(
            "UPDATE products
             SET product_cat = ?, product_brand = ?, product_title = ?, product_price = ?,
                 product_desc = ?, product_image = ?, product_keywords = ?
             WHERE product_id = ?"
        );
        $stmt->bind_param("iisdsssi", $cat, $brand, $title, $price, $desc, $image, $keywords, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // Deletes the product row and its image file from disk
    public function deleteProduct($id) {
        // Fetch the image filename before deleting the row
        $stmt = $this->conn->prepare("SELECT product_image FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            return false;
        }

        $stmt = $this->conn->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();

        // Only remove file if the DB delete succeeded and a filename exists
        if ($ok && !empty($row['product_image'])) {
            $path = __DIR__ . '/../images/products/' . $row['product_image'];
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        return $ok;
    }

    // Returns the product plus its category and brand names
    public function getProductById($id) {
        $stmt = $this->conn->prepare(
            "SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             JOIN categories c ON p.product_cat = c.cat_id
             JOIN brands b ON p.product_brand = b.brand_id
             WHERE p.product_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ? $row : false;
    }

    private function productOrderBy($sort) {
        $orders = [
            'title_asc' => 'p.product_title ASC',
            'price_asc' => 'p.product_price ASC, p.product_title ASC',
            'price_desc' => 'p.product_price DESC, p.product_title ASC',
            'newest' => 'p.product_id DESC'
        ];
        return is_string($sort) ? ($orders[$sort] ?? $orders['title_asc']) : $orders['title_asc'];
    }

    private function collectProducts($result) {
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return $products;
    }

    public function getAllProducts($limit = null, $offset = 0, $sort = 'title_asc') {
        $sql = "SELECT p.*, c.cat_name, b.brand_name
                FROM products p
                JOIN categories c ON p.product_cat = c.cat_id
                JOIN brands b ON p.product_brand = b.brand_id
                ORDER BY " . $this->productOrderBy($sort);

        if ($limit !== null) {
            $limit = max(1, intval($limit));
            $offset = max(0, intval($offset));
            $stmt = $this->conn->prepare($sql . " LIMIT ? OFFSET ?");
            $stmt->bind_param("ii", $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            $products = $this->collectProducts($result);
            $stmt->close();
            return $products;
        }

        return $this->collectProducts($this->conn->query($sql));
    }

    public function countAllProducts() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM products");
        return intval($result->fetch_assoc()['total']);
    }

    // ---------- CUSTOMER-FACING PRODUCT QUERIES ----------

    public function getFeaturedProducts($limit = 6) {
        $stmt = $this->conn->prepare(
            "SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             JOIN categories c ON p.product_cat = c.cat_id
             JOIN brands b ON p.product_brand = b.brand_id
             ORDER BY RAND() LIMIT ?"
        );
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $stmt->close();
        return $products;
    }

    public function getProductsByCategory($cat_id, $limit = null, $offset = 0, $sort = 'title_asc') {
        $sql =
            "SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             JOIN categories c ON p.product_cat = c.cat_id
             JOIN brands b ON p.product_brand = b.brand_id
             WHERE p.product_cat = ?
             ORDER BY " . $this->productOrderBy($sort);
        if ($limit !== null) {
            $limit = max(1, intval($limit));
            $offset = max(0, intval($offset));
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iii", $cat_id, $limit, $offset);
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $cat_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $products = $this->collectProducts($result);
        $stmt->close();
        return $products;
    }

    public function getProductsByBrand($brand_id, $limit = null, $offset = 0, $sort = 'title_asc') {
        $sql =
            "SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             JOIN categories c ON p.product_cat = c.cat_id
             JOIN brands b ON p.product_brand = b.brand_id
             WHERE p.product_brand = ?
             ORDER BY " . $this->productOrderBy($sort);
        if ($limit !== null) {
            $limit = max(1, intval($limit));
            $offset = max(0, intval($offset));
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iii", $brand_id, $limit, $offset);
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $brand_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $products = $this->collectProducts($result);
        $stmt->close();
        return $products;
    }

    public function countProductsByCategory($cat_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM products WHERE product_cat = ?");
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $total = intval($stmt->get_result()->fetch_assoc()['total']);
        $stmt->close();
        return $total;
    }

    public function countProductsByBrand($brand_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM products WHERE product_brand = ?");
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        $total = intval($stmt->get_result()->fetch_assoc()['total']);
        $stmt->close();
        return $total;
    }

    public function searchProducts($query, $limit = null, $offset = 0, $sort = 'title_asc') {
        $like = '%' . $query . '%';

        $sql =
            "SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             JOIN categories c ON p.product_cat = c.cat_id
             JOIN brands b ON p.product_brand = b.brand_id
             WHERE p.product_title LIKE ? OR p.product_keywords LIKE ?
             ORDER BY " . $this->productOrderBy($sort);
        if ($limit !== null) {
            $limit = max(1, intval($limit));
            $offset = max(0, intval($offset));
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssii", $like, $like, $limit, $offset);
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $like, $like);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $products = $this->collectProducts($result);
        $stmt->close();
        return $products;
    }

    public function countSearchProducts($query) {
        $like = '%' . $query . '%';
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS total FROM products
             WHERE product_title LIKE ? OR product_keywords LIKE ?"
        );
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        $total = intval($stmt->get_result()->fetch_assoc()['total']);
        $stmt->close();
        return $total;
    }
}