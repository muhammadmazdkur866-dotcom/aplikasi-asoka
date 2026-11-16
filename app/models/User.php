<?php
/**
 * Model User
 */

require_once __DIR__ . '/../../config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT id_user, nama_lengkap, username, role, created_at FROM users ORDER BY id_user ASC");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id_user, nama_lengkap, username, role, created_at FROM users WHERE id_user = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function findByUsername(string $username): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO users (nama_lengkap, username, password, role) VALUES (?, ?, ?, ?)");
        $hashed = password_hash($data['password'], PASSWORD_BCRYPT);
        return $stmt->execute([
            $data['nama_lengkap'],
            $data['username'],
            $hashed,
            $data['role']
        ]);
    }

    public function update(int $id, array $data): bool {
        if (!empty($data['password'])) {
            $stmt = $this->db->prepare("UPDATE users SET nama_lengkap = ?, username = ?, password = ?, role = ? WHERE id_user = ?");
            $hashed = password_hash($data['password'], PASSWORD_BCRYPT);
            return $stmt->execute([
                $data['nama_lengkap'],
                $data['username'],
                $hashed,
                $data['role'],
                $id
            ]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET nama_lengkap = ?, username = ?, role = ? WHERE id_user = ?");
            return $stmt->execute([
                $data['nama_lengkap'],
                $data['username'],
                $data['role'],
                $id
            ]);
        }
    }

    public function delete(int $id): bool {
        // Cegah menghapus user diri sendiri atau id 1 jika admin utama
        $stmt = $this->db->prepare("DELETE FROM users WHERE id_user = ?");
        return $stmt->execute([$id]);
    }

    public function countAll(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }
}

