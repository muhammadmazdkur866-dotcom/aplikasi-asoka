<?php
/**
 * Model & Engine Algoritma Apriori (Market Basket Analysis)
 */

require_once __DIR__ . '/../../config/database.php';

class Apriori {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Ambil data transaksi dalam rentang tanggal dan ubah menjadi format transaksional
     */
    public function getTransactionalDataset(string $startDate, string $endDate): array {
        $sql = "
            SELECT t.id_transaksi, t.tanggal_waktu, b.nama_barang
            FROM transaksi t
            JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
            JOIN barang b ON dt.id_barang = b.id_barang
            WHERE DATE(t.tanggal_waktu) >= ? AND DATE(t.tanggal_waktu) <= ?
            ORDER BY t.tanggal_waktu ASC, t.id_transaksi ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $rows = $stmt->fetchAll();

        $transactions = [];
        foreach ($rows as $row) {
            $tid = $row['id_transaksi'];
            if (!isset($transactions[$tid])) {
                $transactions[$tid] = [
                    'id_transaksi' => $tid,
                    'tanggal_waktu' => $row['tanggal_waktu'],
                    'items' => []
                ];
            }
            // Pastikan item unik dalam 1 transaksi (deduplikasi item)
            $item = trim($row['nama_barang']);
            if (!in_array($item, $transactions[$tid]['items'])) {
                $transactions[$tid]['items'][] = $item;
            }
        }

        // Urutkan item alfabetis di setiap transaksi agar konsisten
        foreach ($transactions as &$trans) {
            sort($trans['items']);
        }
        unset($trans);

        return $transactions;
    }

    /**
     * Hitung frekuensi kemunculan sebuah itemset di dalam kumpulan transaksi
     */
    private function countItemsetSupport(array $itemset, array $transactionLists): int {
        $count = 0;
        foreach ($transactionLists as $transItems) {
            // Cek apakah semua elemen $itemset ada di dalam $transItems
            $match = true;
            foreach ($itemset as $item) {
                if (!in_array($item, $transItems)) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Helper untuk membuat kombinasi k elemen dari array
     */
    private function getCombinations(array $items, int $k): array {
        if ($k === 1) {
            $res = [];
            foreach ($items as $item) {
                $res[] = [$item];
            }
            return $res;
        }

        if ($k === count($items)) {
            return [$items];
        }

        $combinations = [];
        $first = array_shift($items);

        // Kombinasi yang menyertakan elemen pertama
        $withFirst = $this->getCombinations($items, $k - 1);
        foreach ($withFirst as $comb) {
            array_unshift($comb, $first);
            $combinations[] = $comb;
        }

        // Kombinasi tanpa elemen pertama
        $withoutFirst = $this->getCombinations($items, $k);
        foreach ($withoutFirst as $comb) {
            $combinations[] = $comb;
        }

        return $combinations;
    }

    /**
     * Eksekusi Algoritma Apriori Lengkap
     */
    public function runApriori(string $startDate, string $endDate, float $minSupportPercent, float $minConfidencePercent): array {
        $startTime = microtime(true);

        $transactionsRaw = $this->getTransactionalDataset($startDate, $endDate);
        $totalTransactions = count($transactionsRaw);

        if ($totalTransactions === 0) {
            return [
                'status' => 'error',
                'message' => "Tidak ditemukan transaksi pada rentang tanggal $startDate s/d $endDate."
            ];
        }

        $transLists = [];
        foreach ($transactionsRaw as $t) {
            $transLists[] = $t['items'];
        }

        $minSupportCount = ($minSupportPercent / 100) * $totalTransactions;

        // ==========================================
        // 1. ITERASI 1: CANDIDATE 1-ITEMSET (C1 & L1)
        // ==========================================
        $c1Counts = [];
        foreach ($transLists as $items) {
            foreach ($items as $item) {
                if (!isset($c1Counts[$item])) {
                    $c1Counts[$item] = 0;
                }
                $c1Counts[$item]++;
            }
        }
        ksort($c1Counts);

        $candidateItemsets = [];
        $frequentItemsets = []; // Menyimpan itemset lolos per k
        $allFrequentItemsetMap = []; // Map string => count untuk lookup cepat saat hitung confidence

        $c1List = [];
        $l1List = [];

        foreach ($c1Counts as $item => $count) {
            $support = round(($count / $totalTransactions) * 100, 2);
            $passed = ($support >= $minSupportPercent);

            $entry = [
                'items' => [$item],
                'items_str' => $item,
                'count' => $count,
                'support' => $support,
                'passed' => $passed
            ];

            $c1List[] = $entry;
            if ($passed) {
                $l1List[] = $entry;
                $allFrequentItemsetMap[$item] = $count;
            }
        }

        $candidateItemsets[1] = $c1List;
        $frequentItemsets[1] = $l1List;

        // ==========================================
        // 2. ITERASI k-ITEMSET (C2, L2, C3, L3, dst)
        // ==========================================
        $k = 2;
        $maxK = 4; // Maksimal ukuran itemset sembako agar efisien

        while ($k <= $maxK && !empty($frequentItemsets[$k - 1])) {
            $prevFrequent = $frequentItemsets[$k - 1];

            // Kumpulkan semua item unik dari tahap sebelumnya
            $uniqueSingleItems = [];
            foreach ($prevFrequent as $itemset) {
                foreach ($itemset['items'] as $it) {
                    $uniqueSingleItems[$it] = true;
                }
            }
            $uniqueSingleItems = array_keys($uniqueSingleItems);
            sort($uniqueSingleItems);

            if (count($uniqueSingleItems) < $k) {
                break;
            }

            // Bentuk kandidat Ck dari kombinasi $uniqueSingleItems sebesar $k
            $candidatesK = $this->getCombinations($uniqueSingleItems, $k);

            // Apriori Pruning: setiap sub-kombinasi berukuran k-1 harus ada di frequent previous
            $ckList = [];
            $lkList = [];

            foreach ($candidatesK as $cand) {
                sort($cand);
                $candStr = implode(', ', $cand);

                // Validasi pruning (semua sub-itemset k-1 harus frequent)
                $pruned = false;
                if ($k > 2) {
                    $subCombs = $this->getCombinations($cand, $k - 1);
                    foreach ($subCombs as $sub) {
                        sort($sub);
                        $subStr = implode(', ', $sub);
                        $foundInPrev = false;
                        foreach ($prevFrequent as $pf) {
                            if ($pf['items_str'] === $subStr) {
                                $foundInPrev = true;
                                break;
                            }
                        }
                        if (!$foundInPrev) {
                            $pruned = true;
                            break;
                        }
                    }
                }

                if ($pruned) {
                    continue;
                }

                // Hitung support count
                $count = $this->countItemsetSupport($cand, $transLists);
                $support = round(($count / $totalTransactions) * 100, 2);
                $passed = ($support >= $minSupportPercent);

                $entry = [
                    'items' => $cand,
                    'items_str' => $candStr,
                    'count' => $count,
                    'support' => $support,
                    'passed' => $passed
                ];

                $ckList[] = $entry;

                if ($passed) {
                    $lkList[] = $entry;
                    $allFrequentItemsetMap[$candStr] = $count;
                }
            }

            if (!empty($ckList)) {
                $candidateItemsets[$k] = $ckList;
            }
            if (!empty($lkList)) {
                $frequentItemsets[$k] = $lkList;
            } else {
                break;
            }

            $k++;
        }

        // ==========================================
        // 3. PEMBENTUKAN ATURAN ASOSIASI (RULES)
        // ==========================================
        $associationRules = [];
        $businessInsights = [];

        // Rules dibentuk dari frequent itemset dengan k >= 2
        for ($size = 2; $size <= count($frequentItemsets); $size++) {
            if (empty($frequentItemsets[$size])) continue;

            foreach ($frequentItemsets[$size] as $fi) {
                $items = $fi['items'];
                $itemsetCount = $fi['count'];
                $itemsetSupport = $fi['support'];

                // Generate semua partisi Antecedent (A) dan Consequent (B)
                // di mana A + B = items, A != empty, B != empty
                for ($m = 1; $m < $size; $m++) {
                    $antecedentCombs = $this->getCombinations($items, $m);

                    foreach ($antecedentCombs as $antecedent) {
                        sort($antecedent);
                        $consequent = array_values(array_diff($items, $antecedent));
                        sort($consequent);

                        $antStr = implode(', ', $antecedent);
                        $conStr = implode(', ', $consequent);

                        // Ambil count antecedent
                        $antCount = $allFrequentItemsetMap[$antStr] ?? $this->countItemsetSupport($antecedent, $transLists);
                        if ($antCount === 0) continue;

                        $antSupport = round(($antCount / $totalTransactions) * 100, 2);

                        // Ambil count consequent untuk benchmark support
                        $conCount = $allFrequentItemsetMap[$conStr] ?? $this->countItemsetSupport($consequent, $transLists);
                        $conSupport = round(($conCount / $totalTransactions) * 100, 2);

                        // Confidence = (Count(A U B) / Count(A)) * 100%
                        $confidence = round(($itemsetCount / $antCount) * 100, 2);

                        // Lift Ratio = Confidence / Benchmark Support (Consequent)
                        // Lift = (itemsetCount / total) / ((antCount / total) * (conCount / total))
                        $liftRatio = 0.0;
                        if ($antCount > 0 && $conCount > 0) {
                            $liftRatio = round(($itemsetCount * $totalTransactions) / ($antCount * $conCount), 2);
                        }

                        // Filter Minimum Confidence
                        if ($confidence >= $minConfidencePercent) {
                            $correlation = 'Positif';
                            if ($liftRatio < 1.0) {
                                $correlation = 'Negatif';
                            } elseif ($liftRatio == 1.0) {
                                $correlation = 'Independen';
                            }

                            $rule = [
                                'antecedent' => $antecedent,
                                'antecedent_str' => $antStr,
                                'consequent' => $consequent,
                                'consequent_str' => $conStr,
                                'itemset_support' => $itemsetSupport,
                                'antecedent_support' => $antSupport,
                                'consequent_support' => $conSupport,
                                'confidence' => $confidence,
                                'lift_ratio' => $liftRatio,
                                'correlation' => $correlation,
                                'rule_text' => "Jika membeli [$antStr], maka kemungkinan juga membeli [$conStr]"
                            ];

                            $associationRules[] = $rule;
                        }
                    }
                }
            }
        }

        // Urutkan rules berdasarkan Confidence DESC, lalu Lift Ratio DESC, lalu Support DESC
        usort($associationRules, function ($a, $b) {
            if ($b['confidence'] != $a['confidence']) {
                return $b['confidence'] <=> $a['confidence'];
            }
            if ($b['lift_ratio'] != $a['lift_ratio']) {
                return $b['lift_ratio'] <=> $a['lift_ratio'];
            }
            return $b['itemset_support'] <=> $a['itemset_support'];
        });

        // ==========================================
        // 4. GENERATE REKOMENDASI BISNIS (INSIGHTS)
        // ==========================================
        $seenCombinations = [];
        foreach ($associationRules as $rule) {
            $key = $rule['antecedent_str'] . ' -> ' . $rule['consequent_str'];
            if (isset($seenCombinations[$key])) continue;
            $seenCombinations[$key] = true;

            $antText = implode(' dan ', $rule['antecedent']);
            $conText = implode(' dan ', $rule['consequent']);

            $narasi = "Berdasarkan analisis riwayat transaksi kasir, konsumen yang membeli <strong>{$antText}</strong> memiliki tingkat kecenderungan sebesar <strong>{$rule['confidence']}%</strong> untuk juga membeli <strong>{$conText}</strong> (Nilai Lift Ratio: {$rule['lift_ratio']}).";

            $strategiPenempatan = "Tempatkan rak display untuk <strong>{$antText}</strong> bersebelahan atau satu lorong dengan <strong>{$conText}</strong> agar memudahkan konsumen menjangkau keduanya dan meningkatkan pembelian impulsif.";

            $promoBundling = "Buat paket promo bundling hemat: <em>\"Paket Belanja Hemat: Beli {$antText} dapatkan potongan khusus untuk {$conText}\"</em>.";

            $manajemenStok = "Saat menambah restok untuk <strong>{$antText}</strong>, pastikan ketersediaan stok <strong>{$conText}</strong> di gudang juga aman untuk mengantisipasi lonjakan pembelian bersamaan.";

            $businessInsights[] = [
                'antecedent' => $antText,
                'consequent' => $conText,
                'confidence' => $rule['confidence'],
                'lift_ratio' => $rule['lift_ratio'],
                'narasi' => $narasi,
                'strategi_penempatan' => $strategiPenempatan,
                'promo_bundling' => $promoBundling,
                'manajemen_stok' => $manajemenStok
            ];
        }

        $endTime = microtime(true);
        $executionTimeMs = round(($endTime - $startTime) * 1000, 2);

        // Hitung total itemset lolos di semua level
        $totalFrequentItemsets = 0;
        foreach ($frequentItemsets as $fiList) {
            $totalFrequentItemsets += count($fiList);
        }

        return [
            'status' => 'success',
            'periode_awal' => $startDate,
            'periode_akhir' => $endDate,
            'total_transactions' => $totalTransactions,
            'min_support_percent' => $minSupportPercent,
            'min_confidence_percent' => $minConfidencePercent,
            'min_support_count' => round($minSupportCount, 2),
            'candidate_itemsets' => $candidateItemsets,
            'frequent_itemsets' => $frequentItemsets,
            'total_frequent_itemsets' => $totalFrequentItemsets,
            'association_rules' => $associationRules,
            'total_rules' => count($associationRules),
            'business_insights' => $businessInsights,
            'execution_time_ms' => $executionTimeMs,
            'transactions_dataset' => array_values($transactionsRaw)
        ];
    }

    /**
     * Simpan hasil analisis ke tabel hasil_apriori
     */
    public function saveAnalysis(array $result, int $userId): int {
        $stmt = $this->db->prepare("
            INSERT INTO hasil_apriori (
                tanggal_analisis, periode_transaksi_mulai, periode_transaksi_akhir,
                min_support, min_confidence, total_transaksi,
                total_itemset_lolos, total_rules, aturan_asosiasi, id_user
            ) VALUES (
                NOW(), ?, ?,
                ?, ?, ?,
                ?, ?, ?, ?
            )
        ");

        $aturanJson = json_encode($result, JSON_UNESCAPED_UNICODE);

        $stmt->execute([
            $result['periode_awal'],
            $result['periode_akhir'],
            $result['min_support_percent'],
            $result['min_confidence_percent'],
            $result['total_transactions'],
            $result['total_frequent_itemsets'],
            $result['total_rules'],
            $aturanJson,
            $userId
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Ambil riwayat analisis
     */
    public function getHistory(int $limit = 20): array {
        $stmt = $this->db->prepare("
            SELECT h.*, u.nama_lengkap as nama_user
            FROM hasil_apriori h
            LEFT JOIN users u ON h.id_user = u.id_user
            ORDER BY h.tanggal_analisis DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ambil detail riwayat analisis berdasarkan ID
     */
    public function getHistoryById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT h.*, u.nama_lengkap as nama_user
            FROM hasil_apriori h
            LEFT JOIN users u ON h.id_user = u.id_user
            WHERE h.id_analisis = ?
        ");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        if ($res && !empty($res['aturan_asosiasi'])) {
            $res['data_decoded'] = json_decode($res['aturan_asosiasi'], true);
        }
        return $res ?: null;
    }

    public function deleteHistory(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM hasil_apriori WHERE id_analisis = ?");
        return $stmt->execute([$id]);
    }
}

