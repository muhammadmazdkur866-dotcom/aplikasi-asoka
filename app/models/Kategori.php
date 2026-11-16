<?php
/**
 * Model Kategori
 */

require_once __DIR__ . '/../../config/database.php';

class Kategori {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT k.id_kategori, k.nama_kategori, k.created_at, 
                   COUNT(b.id_barang) as total_barang
            FROM kategori k
            LEFT JOIN barang b ON k.id_kategori = b.id_kategori
            GROUP BY k.id_kategori
            ORDER BY k.id_kategori ASC
        ");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(string $nama): bool {
        $stmt = $this->db->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
        return $stmt->execute([trim($nama)]);
    }

    public function update(int $id, string $nama): bool {
        $stmt = $this->db->prepare("UPDATE kategori SET nama_kategori = ? WHERE id_kategori = ?");
        return $stmt->execute([trim($nama), $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM kategori WHERE id_kategori = ?");
        return $stmt->execute([$id]);
    }

    public function countAll(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
    }
}

