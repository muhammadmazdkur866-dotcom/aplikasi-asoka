<?php
/**
 * Model Barang
 */

require_once __DIR__ . '/../../config/database.php';

class Barang {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(?int $idKategori = null, ?string $keyword = null): array {
        $sql = "
            SELECT b.*, k.nama_kategori 
            FROM barang b
            JOIN kategori k ON b.id_kategori = k.id_kategori
            WHERE 1=1
        ";
        $params = [];

        if (!empty($idKategori)) {
            $sql .= " AND b.id_kategori = ?";
            $params[] = $idKategori;
        }

        if (!empty($keyword)) {
            $sql .= " AND (b.id_barang LIKE ? OR b.nama_barang LIKE ?)";
            $params[] = "%$keyword%";
            $params[] = "%$keyword%";
        }

        $sql .= " ORDER BY b.nama_barang ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(string $id): ?array {
        $stmt = $this->db->prepare("
            SELECT b.*, k.nama_kategori 
            FROM barang b
            JOIN kategori k ON b.id_kategori = k.id_kategori
            WHERE b.id_barang = ?
        ");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO barang (id_barang, id_kategori, nama_barang, harga_beli, harga_jual, stok, stok_minimum, satuan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            trim($data['id_barang']),
            (int)$data['id_kategori'],
            trim($data['nama_barang']),
            (float)$data['harga_beli'],
            (float)$data['harga_jual'],
            (int)$data['stok'],
            (int)($data['stok_minimum'] ?? 5),
            trim($data['satuan'] ?? 'pcs')
        ]);
    }

    public function update(string $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE barang 
            SET id_kategori = ?, nama_barang = ?, harga_beli = ?, harga_jual = ?, stok = ?, stok_minimum = ?, satuan = ?
            WHERE id_barang = ?
        ");
        return $stmt->execute([
            (int)$data['id_kategori'],
            trim($data['nama_barang']),
            (float)$data['harga_beli'],
            (float)$data['harga_jual'],
            (int)$data['stok'],
            (int)($data['stok_minimum'] ?? 5),
            trim($data['satuan'] ?? 'pcs'),
            $id
        ]);
    }

    public function delete(string $id): bool {
        $stmt = $this->db->prepare("DELETE FROM barang WHERE id_barang = ?");
        return $stmt->execute([$id]);
    }

    public function generateNextId(): string {
        $stmt = $this->db->query("SELECT id_barang FROM barang WHERE id_barang LIKE 'BRG%' ORDER BY id_barang DESC LIMIT 1");
        $lastId = $stmt->fetchColumn();
        if ($lastId) {
            $num = (int)substr($lastId, 3);
            return 'BRG' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
        }
        return 'BRG001';
    }

    public function getLowStock(): array {
        $stmt = $this->db->query("
            SELECT b.*, k.nama_kategori 
            FROM barang b
            JOIN kategori k ON b.id_kategori = k.id_kategori
            WHERE b.stok <= b.stok_minimum
            ORDER BY b.stok ASC
        ");
        return $stmt->fetchAll();
    }

    public function countLowStock(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM barang WHERE stok <= stok_minimum")->fetchColumn();
    }

    public function countAll(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM barang")->fetchColumn();
    }

    public function getTotalStockValue(): array {
        $stmt = $this->db->query("
            SELECT 
                SUM(stok * harga_beli) as total_modal,
                SUM(stok * harga_jual) as total_potensi_jual,
                SUM(stok) as total_stok_fisik
            FROM barang
        ");
        return $stmt->fetch() ?: ['total_modal' => 0, 'total_potensi_jual' => 0, 'total_stok_fisik' => 0];
    }
}

