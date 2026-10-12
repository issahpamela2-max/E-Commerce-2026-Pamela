<?php
require_once __DIR__ . '/../core/db_class.php';

class CustomerClass extends Database {

    // Check if an email is already registered
    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT customer_email FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $found = $result->num_rows > 0;
        $stmt->close();
        return $found;
    }

    // Add a new customer
    public function addCustomer($name, $email, $pass, $country, $city, $contact) {
        $hash = password_hash($pass, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare(
            "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssss", $name, $email, $hash, $country, $city, $contact);
        $ok = $stmt->execute();
        $new_id = $this->conn->insert_id;
        $stmt->close();

        return $ok ? $new_id : false;
    }

    // Fetch one customer by email
    public function getCustomerByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row ? $row : false;
    }

    // Fetch one customer by id
    public function getCustomerById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ? $row : false;
    }

    // Check email + password together
    public function login($email, $pass) {
        $row = $this->getCustomerByEmail($email);

        if (!$row) {
            return false;
        }

        if (password_verify($pass, $row['customer_pass'])) {
            return $row;
        }

        return false;
    }

    // Update profile details (not email, not password)
    public function updateProfile($id, $name, $country, $city, $contact) {
        $stmt = $this->conn->prepare(
            "UPDATE customer
             SET customer_name = ?, customer_country = ?, customer_city = ?, customer_contact = ?
             WHERE customer_id = ?"
        );
        $stmt->bind_param("ssssi", $name, $country, $city, $contact, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // Replace the stored password hash
    public function updatePassword($id, $new_pass) {
        $hash = password_hash($new_pass, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare("UPDATE customer SET customer_pass = ? WHERE customer_id = ?");
        $stmt->bind_param("si", $hash, $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}