<?php
require_once __DIR__ . '/../core/db_class.php';

class CartClass extends Database {

    // Is this product already in this visitor's cart?
    public function isInCart($product_id, $ip, $customer_id = null) {
        if ($customer_id !== null) {
            $stmt = $this->conn->prepare("SELECT p_id FROM cart WHERE p_id = ? AND c_id = ?");
            $stmt->bind_param("ii", $product_id, $customer_id);
        } else {
            $stmt = $this->conn->prepare(
                "SELECT p_id FROM cart WHERE p_id = ? AND ip_add = ? AND (c_id IS NULL OR c_id = 0)"
            );
            $stmt->bind_param("is", $product_id, $ip);
        }
        $stmt->execute();
        $found = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $found;
    }

    public function addToCart($product_id, $qty, $ip, $customer_id = null) {
        $stmt = $this->conn->prepare(
            "INSERT INTO cart (p_id, ip_add, c_id, qty)
             SELECT ?, ?, ?, ?
             FROM products
             WHERE product_id = ?"
        );
        $stmt->bind_param("isiii", $product_id, $ip, $customer_id, $qty, $product_id);
        $ok = $stmt->execute();
        $added = $ok && $stmt->affected_rows === 1;
        $stmt->close();
        return $added;
    }

    // Cart rows joined to their product details
    public function getCartItems($ip, $customer_id = null) {
        $where = $customer_id !== null
            ? "c.c_id = ?"
            : "c.ip_add = ? AND (c.c_id IS NULL OR c.c_id = 0)";
        $stmt = $this->conn->prepare(
            "SELECT c.p_id, c.qty, p.product_title, p.product_price, p.product_image
             FROM cart c
             JOIN products p ON c.p_id = p.product_id
             WHERE $where
             ORDER BY p.product_title ASC"
        );
        if ($customer_id !== null) {
            $stmt->bind_param("i", $customer_id);
        } else {
            $stmt->bind_param("s", $ip);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        return $items;
    }

    // Total number of units in the cart
    public function getCartCount($ip, $customer_id = null) {
        if ($customer_id !== null) {
            $stmt = $this->conn->prepare("SELECT COALESCE(SUM(qty), 0) AS total FROM cart WHERE c_id = ?");
            $stmt->bind_param("i", $customer_id);
        } else {
            $stmt = $this->conn->prepare(
                "SELECT COALESCE(SUM(qty), 0) AS total FROM cart WHERE ip_add = ? AND (c_id IS NULL OR c_id = 0)"
            );
            $stmt->bind_param("s", $ip);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ? intval($row['total']) : 0;
    }

    // Value of everything in the cart
    public function getCartTotal($ip, $customer_id = null) {
        $where = $customer_id !== null
            ? "c.c_id = ?"
            : "c.ip_add = ? AND (c.c_id IS NULL OR c.c_id = 0)";
        $stmt = $this->conn->prepare(
            "SELECT SUM(c.qty * p.product_price) AS total
             FROM cart c
             JOIN products p ON c.p_id = p.product_id
             WHERE $where"
        );
        if ($customer_id !== null) {
            $stmt->bind_param("i", $customer_id);
        } else {
            $stmt->bind_param("s", $ip);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row && $row['total'] ? floatval($row['total']) : 0.0;
    }

    // ---------- TASK 12 ----------

    public function removeFromCart($product_id, $ip, $customer_id = null) {
        if ($customer_id !== null) {
            $stmt = $this->conn->prepare("DELETE FROM cart WHERE p_id = ? AND c_id = ?");
            $stmt->bind_param("ii", $product_id, $customer_id);
        } else {
            $stmt = $this->conn->prepare(
                "DELETE FROM cart WHERE p_id = ? AND ip_add = ? AND (c_id IS NULL OR c_id = 0)"
            );
            $stmt->bind_param("is", $product_id, $ip);
        }
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function updateQty($product_id, $new_qty, $ip, $customer_id = null) {
        if ($new_qty < 1) {
            return false;
        }

        $cart_where = $customer_id !== null
            ? "c.c_id = ?"
            : "c.ip_add = ? AND (c.c_id IS NULL OR c.c_id = 0)";
        $stmt = $this->conn->prepare(
            "SELECT c.p_id
             FROM cart c
             WHERE c.p_id = ? AND $cart_where"
        );
        if ($customer_id !== null) {
            $stmt->bind_param("ii", $product_id, $customer_id);
        } else {
            $stmt->bind_param("is", $product_id, $ip);
        }
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            return false;
        }

        if ($customer_id !== null) {
            $stmt = $this->conn->prepare("UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?");
            $stmt->bind_param("iii", $new_qty, $product_id, $customer_id);
        } else {
            $stmt = $this->conn->prepare(
                "UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ? AND (c_id IS NULL OR c_id = 0)"
            );
            $stmt->bind_param("iis", $new_qty, $product_id, $ip);
        }
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function mergeGuestCart($ip, $customer_id) {
        $this->conn->begin_transaction();

        $guest_stmt = $this->conn->prepare(
            "SELECT p_id, qty FROM cart
             WHERE ip_add = ? AND (c_id IS NULL OR c_id = 0)
             FOR UPDATE"
        );
        $guest_stmt->bind_param("s", $ip);
        $ok = $guest_stmt->execute();
        $result = $ok ? $guest_stmt->get_result() : false;
        $guest_items = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $guest_items[] = $row;
            }
        }
        $guest_stmt->close();

        if (!$ok) {
            $this->conn->rollback();
            return false;
        }

        foreach ($guest_items as $item) {
            $product_id = intval($item['p_id']);
            $qty = intval($item['qty']);

            $existing_stmt = $this->conn->prepare(
                "SELECT qty FROM cart WHERE p_id = ? AND c_id = ? FOR UPDATE"
            );
            $existing_stmt->bind_param("ii", $product_id, $customer_id);
            $ok = $existing_stmt->execute();
            $existing = $ok ? $existing_stmt->get_result()->fetch_assoc() : false;
            $existing_stmt->close();

            if (!$ok) {
                $this->conn->rollback();
                return false;
            }

            if ($existing) {
                $merged_qty = intval($existing['qty']) + $qty;
                $update_stmt = $this->conn->prepare(
                    "UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?"
                );
                $update_stmt->bind_param("iii", $merged_qty, $product_id, $customer_id);
                $ok = $update_stmt->execute();
                $update_stmt->close();

                if ($ok) {
                    $delete_stmt = $this->conn->prepare(
                        "DELETE FROM cart
                         WHERE p_id = ? AND ip_add = ? AND (c_id IS NULL OR c_id = 0)"
                    );
                    $delete_stmt->bind_param("is", $product_id, $ip);
                    $ok = $delete_stmt->execute();
                    $delete_stmt->close();
                }
            } else {
                $claim_stmt = $this->conn->prepare(
                    "UPDATE cart SET c_id = ?
                     WHERE p_id = ? AND ip_add = ? AND (c_id IS NULL OR c_id = 0)"
                );
                $claim_stmt->bind_param("iis", $customer_id, $product_id, $ip);
                $ok = $claim_stmt->execute();
                $claim_stmt->close();
            }

            if (!$ok) {
                $this->conn->rollback();
                return false;
            }
        }

        $this->conn->commit();
        return true;
    }

    // ---------- TASK 13: PAYMENT ----------

    // Order items, payment, and cart cleanup commit or roll back together.
    public function processPayment($customer_id, $amount) {

        $this->conn->begin_transaction();

        $stmt = $this->conn->prepare(
            "SELECT c.p_id, c.qty, p.product_price
             FROM cart c
             JOIN products p ON p.product_id = c.p_id
             WHERE c.c_id = ?
             ORDER BY c.p_id
             FOR UPDATE"
        );
        $stmt->bind_param("i", $customer_id);
        $ok = $stmt->execute();
        $result = $ok ? $stmt->get_result() : false;
        $cart_rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $cart_rows[] = $row;
            }
        }
        $stmt->close();

        if (!$ok || empty($cart_rows)) {
            $this->conn->rollback();
            return false;
        }

        $cart_total_cents = 0;
        foreach ($cart_rows as $cart_row) {
            $qty = intval($cart_row['qty']);
            if ($qty < 1) {
                $this->conn->rollback();
                return false;
            }
            $cart_total_cents += (int) round(floatval($cart_row['product_price']) * 100) * $qty;
        }
        if ($cart_total_cents !== (int) round($amount * 100)) {
            $this->conn->rollback();
            return false;
        }

        $invoice_no = mt_rand(100000, 999999);
        $today      = date('Y-m-d');
        $currency   = 'GHS';

        // 1. Create the order
        $stmt = $this->conn->prepare(
            "INSERT INTO orders (customer_id, invoice_no, order_date, order_status)
             VALUES (?, ?, ?, 'paid')"
        );
        $stmt->bind_param("iis", $customer_id, $invoice_no, $today);
        $ok = $stmt->execute();
        $order_id = $this->conn->insert_id;
        $stmt->close();

        if (!$ok || $order_id <= 0) {
            $this->conn->rollback();
            return false;
        }

        // 2. Copy every cart row into orderdetails
        $stmt = $this->conn->prepare(
            "INSERT INTO orderdetails (order_id, product_id, qty)
             SELECT ?, p_id, qty FROM cart WHERE c_id = ?"
        );
        $stmt->bind_param("ii", $order_id, $customer_id);
        $ok = $stmt->execute();
        $rows_copied = $stmt->affected_rows;
        $stmt->close();

        if (!$ok || $rows_copied < 1) {
            $this->conn->rollback();
            return false;
        }

        // 3. Record the payment
        $stmt = $this->conn->prepare(
            "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("diiss", $amount, $customer_id, $order_id, $currency, $today);
        $ok = $stmt->execute();
        $stmt->close();

        if (!$ok) {
            $this->conn->rollback();
            return false;
        }

        // 4. Empty the cart
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE c_id = ?");
        $stmt->bind_param("i", $customer_id);
        $ok = $stmt->execute();
        $stmt->close();

        if (!$ok) {
            $this->conn->rollback();
            return false;
        }

        $this->conn->commit();
        return $order_id;
    }

    // The order header, for the invoice
    public function getOrderById($order_id) {
        $stmt = $this->conn->prepare(
            "SELECT o.*, c.customer_name, c.customer_email, p.amt, p.currency
             FROM orders o
             JOIN customer c ON o.customer_id = c.customer_id
             LEFT JOIN payment p ON p.order_id = o.order_id
             WHERE o.order_id = ?"
        );
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ? $row : false;
    }

    // The line items on that order
    public function getOrderItems($order_id) {
        $stmt = $this->conn->prepare(
            "SELECT od.qty, p.product_id, p.product_title, p.product_price, p.product_image
             FROM orderdetails od
             JOIN products p ON od.product_id = p.product_id
             WHERE od.order_id = ?"
        );
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        return $items;
    }

    // All orders placed by one customer, newest first.
    // The WHERE clause is what stops customer A from reading customer B's history.
    public function getOrdersByCustomer($customer_id) {
        $stmt = $this->conn->prepare(
            "SELECT o.order_id, o.invoice_no, o.order_date, o.order_status,
                    COALESCE(p.amt, 0) AS total
             FROM orders o
             LEFT JOIN payment p ON p.order_id = o.order_id
             WHERE o.customer_id = ?
             ORDER BY o.order_date DESC, o.order_id DESC"
        );
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        $stmt->close();
        return $orders;
    }

    // Admin: every order with customer name, newest first
    public function getAllOrders() {
        $result = $this->conn->query(
            "SELECT o.order_id, o.invoice_no, o.order_date, o.order_status,
                    c.customer_name, c.customer_email,
                    COALESCE(p.amt, 0) AS total
             FROM orders o
             JOIN customer c ON o.customer_id = c.customer_id
             LEFT JOIN payment p ON p.order_id = o.order_id
             ORDER BY o.order_date DESC, o.order_id DESC"
        );
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return $orders;
    }

    private function orderStatusTransitions() {
        return [
            'paid' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered'],
            'delivered' => [],
            'cancelled' => []
        ];
    }

    public function getAvailableOrderStatuses($current_status) {
        $transitions = $this->orderStatusTransitions();
        if (!array_key_exists($current_status, $transitions)) {
            return [];
        }
        return $transitions[$current_status];
    }

    // Admin: apply only valid forward transitions, while guarding against stale status forms.
    public function updateOrderStatus($order_id, $status) {
        $transitions = $this->orderStatusTransitions();
        if (!in_array($status, ['paid', 'processing', 'shipped', 'delivered', 'cancelled'], true)) {
            return false;
        }

        $stmt = $this->conn->prepare("SELECT order_status FROM orders WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$order || !in_array($status, $transitions[$order['order_status']] ?? [], true)) {
            return false;
        }

        $current_status = $order['order_status'];
        $stmt = $this->conn->prepare(
            "UPDATE orders SET order_status = ? WHERE order_id = ? AND order_status = ?"
        );
        $stmt->bind_param("sis", $status, $order_id, $current_status);
        $ok = $stmt->execute();
        $updated = $ok && $stmt->affected_rows === 1;
        $stmt->close();
        return $updated;
    }
}