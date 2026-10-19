<?php
require_once __DIR__ . '/../core/db_class.php';

class CartClass extends Database {

    // Is this product already in this visitor's cart?
    public function isInCart($product_id, $ip) {
        $stmt = $this->conn->prepare("SELECT p_id FROM cart WHERE p_id = ? AND ip_add = ?");
        $stmt->bind_param("is", $product_id, $ip);
        $stmt->execute();
        $found = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $found;
    }

    public function addToCart($product_id, $qty, $ip, $customer_id = null) {
        $stmt = $this->conn->prepare("INSERT INTO cart (p_id, ip_add, c_id, qty) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isii", $product_id, $ip, $customer_id, $qty);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // Cart rows joined to their product details
    public function getCartItems($ip) {
        $stmt = $this->conn->prepare(
            "SELECT c.p_id, c.qty, p.product_title, p.product_price, p.product_image
             FROM cart c
             JOIN products p ON c.p_id = p.product_id
             WHERE c.ip_add = ?
             ORDER BY p.product_title ASC"
        );
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        return $items;
    }

    // How many separate products are in the cart
    public function getCartCount($ip) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM cart WHERE ip_add = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ? intval($row['total']) : 0;
    }

    // Value of everything in the cart
    public function getCartTotal($ip) {
        $stmt = $this->conn->prepare(
            "SELECT SUM(c.qty * p.product_price) AS total
             FROM cart c
             JOIN products p ON c.p_id = p.product_id
             WHERE c.ip_add = ?"
        );
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row && $row['total'] ? floatval($row['total']) : 0.0;
    }

    // ---------- TASK 12 ----------

    public function removeFromCart($product_id, $ip) {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE p_id = ? AND ip_add = ?");
        $stmt->bind_param("is", $product_id, $ip);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function updateQty($product_id, $new_qty, $ip) {
        if ($new_qty < 1) {
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ?");
        $stmt->bind_param("iis", $new_qty, $product_id, $ip);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}