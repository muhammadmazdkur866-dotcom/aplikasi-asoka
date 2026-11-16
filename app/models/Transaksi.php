<?php
/**
 * Model Transaksi
 */

require_once __DIR__ . '/../../config/database.php';

class Transaksi {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function generateInvoiceNumber(): string {
        $datePrefix = date('Ymd');
        $prefix = "INV-" . $datePrefix . "-";
        
        $stmt = $this->db->prepare("
            SELECT id_transaksi 
            FROM transaksi 
            WHERE id_transaksi LIKE ? 
            ORDER BY id_transaksi DESC 
            LIMIT 1
        ");
        $stmt->execute([$prefix . "%"]);
        $lastInvoice = $stmt->fetchColumn();

        if ($lastInvoice) {
            $lastNum = (int)substr($lastInvoice, strlen($prefix));
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Simpan Transaksi dengan ACID Database Transaction
     */
    public function create(array $header, array $items): array {
        $this->db->beginTransaction();
        try {
            $idTransaksi = !empty($header['id_transaksi']) ? $header['id_transaksi'] : $this->generateInvoiceNumber();
            $tanggalWaktu = $header['tanggal_waktu'] ?? date('Y-m-d H:i:s');

            // 1. Validasi stok sebelum insert
            foreach ($items as $item) {
                $stmtCheck = $this->db->prepare("SELECT stok, nama_barang FROM barang WHERE id_barang = ? FOR UPDATE");
                $stmtCheck->execute([$item['id_barang']]);
                $barang = $stmtCheck->fetch();

                if (!$barang) {
                    throw new Exception("Barang dengan ID {$item['id_barang']} tidak ditemukan.");
                }
                if ($barang['stok'] < $item['kuantitas']) {
                    throw new Exception("Stok untuk produk '{$barang['nama_barang']}' tidak mencukupi (Sisa: {$barang['stok']}, Diminta: {$item['kuantitas']}).");
                }
            }

            // 2. Insert ke tabel transaksi
            $stmtHeader = $this->db->prepare("
                INSERT INTO transaksi (id_transaksi, tanggal_waktu, id_user, total_bayar, uang_tunai, kembalian, catatan)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtHeader->execute([
                $idTransaksi,
                $tanggalWaktu,
                (int)$header['id_user'],
                (float)$header['total_bayar'],
                (float)$header['uang_tunai'],
                (float)$header['kembalian'],
                $header['catatan'] ?? 'Transaksi Kasir'
            ]);

            // 3. Insert ke detail_transaksi & kurangi stok barang
            $stmtDetail = $this->db->prepare("
                INSERT INTO detail_transaksi (id_transaksi, id_barang, kuantitas, harga_satuan, subtotal)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmtUpdateStok = $this->db->prepare("
                UPDATE barang 
                SET stok = stok - ? 
                WHERE id_barang = ?
            ");

            foreach ($items as $item) {
                $qty = (int)$item['kuantitas'];
                $hargaSatuan = (float)$item['harga_satuan'];
                $subtotal = (float)$item['subtotal'];

                $stmtDetail->execute([
                    $idTransaksi,
                    $item['id_barang'],
                    $qty,
                    $hargaSatuan,
                    $subtotal
                ]);

                $stmtUpdateStok->execute([$qty, $item['id_barang']]);
            }

            $this->db->commit();
            return [
                'success' => true,
                'id_transaksi' => $idTransaksi,
                'message' => 'Transaksi berhasil disimpan dan stok otomatis diperbarui.'
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getAll(?string $startDate = null, ?string $endDate = null, ?string $search = null, int $limit = 100): array {
        $sql = "
            SELECT t.*, u.nama_lengkap as nama_kasir,
                   COUNT(dt.id_detail) as total_item_jenis,
                   SUM(dt.kuantitas) as total_qty
            FROM transaksi t
            JOIN users u ON t.id_user = u.id_user
            LEFT JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
            WHERE 1=1
        ";
        $params = [];

        if (!empty($startDate)) {
            $sql .= " AND DATE(t.tanggal_waktu) >= ?";
            $params[] = $startDate;
        }

        if (!empty($endDate)) {
            $sql .= " AND DATE(t.tanggal_waktu) <= ?";
            $params[] = $endDate;
        }

        if (!empty($search)) {
            $sql .= " AND (t.id_transaksi LIKE ? OR u.nama_lengkap LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " GROUP BY t.id_transaksi ORDER BY t.tanggal_waktu DESC LIMIT " . (int)$limit;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(string $id): ?array {
        $stmt = $this->db->prepare("
            SELECT t.*, u.nama_lengkap as nama_kasir, u.username as username_kasir
            FROM transaksi t
            JOIN users u ON t.id_user = u.id_user
            WHERE t.id_transaksi = ?
        ");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getItemsByTransaksiId(string $id): array {
        $stmt = $this->db->prepare("
            SELECT dt.*, b.nama_barang, b.satuan, b.harga_beli, k.nama_kategori
            FROM detail_transaksi dt
            JOIN barang b ON dt.id_barang = b.id_barang
            JOIN kategori k ON b.id_kategori = k.id_kategori
            WHERE dt.id_transaksi = ?
            ORDER BY dt.id_detail ASC
        ");
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public function getTodaySummary(): array {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_transaksi,
                COALESCE(SUM(total_bayar), 0) as total_omset
            FROM transaksi
            WHERE DATE(tanggal_waktu) = ?
        ");
        $stmt->execute([$today]);
        return $stmt->fetch() ?: ['total_transaksi' => 0, 'total_omset' => 0];
    }

    public function getMonthSummary(?int $month = null, ?int $year = null): array {
        $m = $month ?? (int)date('m');
        $y = $year ?? (int)date('Y');

        $stmt = $this->db->prepare("
            SELECT 
                COUNT(DISTINCT t.id_transaksi) as total_transaksi,
                COALESCE(SUM(t.total_bayar), 0) as total_omset,
                COALESCE(SUM(dt.kuantitas * b.harga_beli), 0) as total_modal,
                COALESCE(SUM(dt.subtotal - (dt.kuantitas * b.harga_beli)), 0) as total_laba_kotor
            FROM transaksi t
            JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
            JOIN barang b ON dt.id_barang = b.id_barang
            WHERE MONTH(t.tanggal_waktu) = ? AND YEAR(t.tanggal_waktu) = ?
        ");
        $stmt->execute([$m, $y]);
        return $stmt->fetch() ?: ['total_transaksi' => 0, 'total_omset' => 0, 'total_modal' => 0, 'total_laba_kotor' => 0];
    }

    public function getRecentTransactions(int $limit = 5): array {
        $stmt = $this->db->prepare("
            SELECT t.*, u.nama_lengkap as nama_kasir
            FROM transaksi t
            JOIN users u ON t.id_user = u.id_user
            ORDER BY t.tanggal_waktu DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getSalesChartData(int $days = 7): array {
        $stmt = $this->db->prepare("
            SELECT DATE(tanggal_waktu) as tgl, 
                   COUNT(*) as jml_transaksi, 
                   COALESCE(SUM(total_bayar), 0) as total_omset
            FROM transaksi
            WHERE tanggal_waktu >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(tanggal_waktu)
            ORDER BY tgl ASC
        ");
        $stmt->execute([$days]);
        $rows = $stmt->fetchAll();

        // Siapkan rentang hari lengkap
        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $result[$d] = [
                'tanggal' => date('d M', strtotime($d)),
                'total_omset' => 0,
                'jml_transaksi' => 0
            ];
        }

        foreach ($rows as $row) {
            if (isset($result[$row['tgl']])) {
                $result[$row['tgl']]['total_omset'] = (float)$row['total_omset'];
                $result[$row['tgl']]['jml_transaksi'] = (int)$row['jml_transaksi'];
            }
        }

        return array_values($result);
    }

    public function getTopSellingProducts(int $limit = 5): array {
        $stmt = $this->db->prepare("
            SELECT b.id_barang, b.nama_barang, k.nama_kategori,
                   SUM(dt.kuantitas) as total_terjual,
                   SUM(dt.subtotal) as total_pendapatan
            FROM detail_transaksi dt
            JOIN barang b ON dt.id_barang = b.id_barang
            JOIN kategori k ON b.id_kategori = k.id_kategori
            GROUP BY b.id_barang
            ORDER BY total_terjual DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDetailedReport(?string $startDate = null, ?string $endDate = null): array {
        $sql = "
            SELECT dt.id_transaksi, t.tanggal_waktu, u.nama_lengkap as nama_kasir,
                   b.nama_barang, k.nama_kategori,
                   dt.kuantitas, dt.harga_satuan, dt.subtotal,
                   b.harga_beli, (dt.kuantitas * b.harga_beli) as total_modal,
                   (dt.subtotal - (dt.kuantitas * b.harga_beli)) as laba
            FROM detail_transaksi dt
            JOIN transaksi t ON dt.id_transaksi = t.id_transaksi
            JOIN users u ON t.id_user = u.id_user
            JOIN barang b ON dt.id_barang = b.id_barang
            JOIN kategori k ON b.id_kategori = k.id_kategori
            WHERE 1=1
        ";
        $params = [];

        if (!empty($startDate)) {
            $sql .= " AND DATE(t.tanggal_waktu) >= ?";
            $params[] = $startDate;
        }

        if (!empty($endDate)) {
            $sql .= " AND DATE(t.tanggal_waktu) <= ?";
            $params[] = $endDate;
        }

        $sql .= " ORDER BY t.tanggal_waktu DESC, dt.id_detail ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function delete(string $id): bool {
        // detail_transaksi on delete cascade di database
        $stmt = $this->db->prepare("DELETE FROM transaksi WHERE id_transaksi = ?");
        return $stmt->execute([$id]);
    }
}

