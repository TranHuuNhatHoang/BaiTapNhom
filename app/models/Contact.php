<?php
// File: app/models/Contact.php

class Contact {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Lưu thông tin liên hệ mới từ người dùng (ĐÃ THÊM PHONE)
     * Trạng thái mặc định: 'new' (Mới)
     */
    public function saveContact($name, $email, $phone, $message) {
        $sql = "INSERT INTO contacts (name, email, phone, message, status) 
                VALUES (?, ?, ?, ?, 'new')";
        $stmt = $this->conn->prepare($sql);
        // "ssss" nghĩa là string, string, string, string (name, email, phone, message)
        $stmt->bind_param("ssss", $name, $email, $phone, $message);
        return $stmt->execute();
    }

    /**
     * Lấy tất cả các liên hệ (cho Admin)
     */
    public function getAllContacts() {
        // Ưu tiên hiển thị liên hệ Mới và đang Xử lý trước
        $sql = "SELECT * FROM contacts ORDER BY FIELD(status, 'new', 'pending', 'resolved'), created_at DESC";
        $result = $this->conn->query($sql);
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    /**
     * Lấy chi tiết một liên hệ
     */
    public function getContactById($contact_id) {
        $sql = "SELECT * FROM contacts WHERE contact_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $contact_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Cập nhật trạng thái liên hệ (new, pending, resolved)
     */
    public function updateStatus($contact_id, $status) {
        $sql = "UPDATE contacts SET status = ? WHERE contact_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $contact_id);
        return $stmt->execute();
    }

    /**
     * Đếm liên hệ mới chưa xử lý
     */
    public function countNewContacts() {
        $sql = "SELECT COUNT(contact_id) AS new_count FROM contacts WHERE status = 'new'";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['new_count'] ?? 0;
    }
}
?>