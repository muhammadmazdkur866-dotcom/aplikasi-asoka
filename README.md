# 🛒 Aplikasi Kasir Toko Sembako Asoka & Market Basket Analysis (Algoritma Apriori)

Aplikasi Point of Sales (POS) berbasis web yang terintegrasi langsung dengan modul kecerdasan buatan Data Mining menggunakan **Algoritma Apriori**. Sistem ini dirancang untuk mendigitalisasi operasional kasir toko sembako sekaligus menganalisis pola asosiasi keranjang belanja konsumen (*Market Basket Analysis*).

---

## 📌 Ringkasan Sistem & Tujuan Bisnis

Sistem tidak hanya mencatat transaksi harian kasir, tetapi juga menganalisis riwayat transaksi untuk menghasilkan aturan asosiasi (*Association Rules*) seperti:
> *"Jika pelanggan membeli Beras Pandan Wangi dan Telur Ayam Negeri, maka 100% kemungkinan ia juga membeli Minyak Goreng Bimoli (Lift Ratio: 2.40)."*

### 3 Pilar Rekomendasi Bisnis Otomatis:
1. **📍 Strategi Penempatan Barang (Product Layout):** Menempatkan produk-produk yang sering dibeli bersamaan pada rak atau lorong yang berdekatan untuk meningkatkan kenyamanan pelanggan dan memicu *impulse buying*.
2. **🎁 Paket Promo Bundling:** Membuat paket promo belanja hemat (*bundling*) dengan potongan harga khusus untuk meningkatkan *average order value*.
3. **📦 Manajemen Persediaan Stok Antisipatif:** Mengantisipasi pengadaan dan restok barang pelengkap ketika stok barang utama ditambah, mencegah terjadinya *lost sales*.

---

## 🛠️ Tech Stack & Arsitektur

* **Front-end:** HTML5, CSS3, Bootstrap 5.3, Bootstrap Icons, Chart.js (Grafik Penjualan), JavaScript ES6.
* **Back-end:** PHP 8.2 (Arsitektur Model-View-Controller / MVC yang rapi, modular, dan secure).
* **Algoritma Data Mining:** Algoritma Apriori *pure* PHP (perhitungan C1 & L1, C2 & L2, C3 & L3, Association Rules, Confidence & Lift Ratio).
* **Database:** MySQL / MariaDB dengan koneksi PDO (*Prepared Statements* & ACID Transactions).
* **Printer Support:** Cetak struk belanja thermal printer 58mm / 80mm.

---

## 🔑 Kredensial Login Sistem

Aplikasi memiliki 2 role pengguna dengan pembatasan hak akses:

| Role | Username | Password | Hak Akses |
|---|---|---|---|
| **Admin** | `admin` | `admin123` | Akses Penuh: Dashboard, Master Kategori, Master Barang, Master Pengguna, POS Kasir, Riwayat Transaksi, **Modul Apriori**, Laporan Penjualan, Laporan Stok. |
| **Kasir** | `kasir` | `kasir123` | Akses Terbatas: Transaksi Kasir (POS) & Riwayat Transaksi Kasir. |
| **Kasir 2** | `kasir2` | `kasir123` | Akses Terbatas: Kasir Staf 2. |

---

## 🚀 Panduan Menjalankan Aplikasi di XAMPP

1. **Jalankan Apache & MySQL** pada aplikasi **XAMPP Control Panel**.
2. Pastikan folder proyek berada di:
   ```
   C:\xampp\htdocs\asoka
   ```
3. Database `db_kasir_apriori` sudah aktif. Jika ingin mengimpor ulang database:
   * Buka [phpMyAdmin](http://localhost/phpmyadmin).
   * Buat database baru bernama `db_kasir_apriori`.
   * Impor file SQL backup yang tersedia di folder:
     ```
     c:\xampp\htdocs\asoka\database\db_kasir_apriori.sql
     ```
4. Buka browser dan akses URL:
   ```
   http://localhost/asoka
   ```

---

## 📋 Fitur Sistem Sesuai Blueprint

### 1. Modul Hak Akses & Autentikasi
* Sistem Login / Logout dengan validasi hash password (`password_verify`).
* Otomatis mengarahkan kasir ke halaman kasir POS dan admin ke dashboard.
* Proteksi rute berbasis role (*Role-Based Access Control*).

### 2. Modul Master Data (Admin)
* **Kategori Barang:** CRUD kategori (Sembako Utama, Bumbu Dapur, Minuman, Makanan Instan, Kebersihan).
* **Data Barang Sembako:** CRUD barang, kode barcode, harga beli (HPP), harga jual, stok fisik, batas stok minimum, satuan (kg, liter, pouch, sak, pcs, dll).
* **Low Stock Alert:** Peringatan otomatis untuk produk dengan stok menipis ($\le$ stok minimum) atau habis.
* **Manajemen Pengguna:** CRUD akun admin dan kasir toko.

### 3. Modul Point of Sale (POS Kasir)
* **Pencarian Cepat & Barcode Scanner:** Input pencarian barang real-time atau scan barcode produk langsung dengan tombol pintas `F2`.
* **Keranjang Kasir Interaktif:** Tambah item, ubah kuantitas (+/- atau ketik langsung), hapus item, subtotal dan grand total otomatis.
* **Modal Pembayaran Cepat:** Tombol nominal uang pas dan pecahan cepat (10k, 20k, 50k, 100k, 200k), kalkulasi kembalian *realtime*, tombol pintas `F8`.
* **ACID Transaction:** Penyimpanan aman menggunakan database transaction (header transaksi, detail item belanja, dan pengurangan stok barang otomatis).
* **Cetak Struk Thermal:** Tampilan struk kasir 58mm/80mm yang rapi dan siap cetak langsung.
* **Riwayat Transaksi:** Filter tanggal, rincian item nota, dan cetak ulang struk (*reprint receipt*).

### 4. Modul Data Mining - Algoritma Apriori (Admin)
* **Form Parameter Fleksibel:** Filter rentang tanggal transaksi, pengaturan persentase Minimum Support (%) dan Minimum Confidence (%).
* **Eksekusi Komputasi Apriori di Backend:**
  1. *Transactional Dataset Extraction* dengan deduplikasi item per invoice belanja.
  2. Pembentukan Candidate 1-Itemset ($C_1$) dan Frequent 1-Itemset ($L_1$).
  3. Pembentukan Candidate 2-Itemset ($C_2$) dan Frequent 2-Itemset ($L_2$).
  4. Pembentukan Candidate 3-Itemset ($C_3$) dan Frequent 3-Itemset ($L_3$) menggunakan kaidah *Apriori Pruning*.
  5. Pembuatan Aturan Asosiasi ($A \implies B$) untuk semua kombinasi partisi.
  6. Perhitungan *Support*, *Confidence*, dan *Lift Ratio*.
* **Visualisasi & Transparansi Perhitungan (Step-by-Step):**
  * Tab 1: Tabel Strong Association Rules ($A \implies B$, Support %, Confidence %, Lift Ratio, Status Korelasi).
  * Tab 2: Rekomendasi Bisnis Cerdas (Tata letak rak, Paket promo bundling, Antisipasi persediaan).
  * Tab 3: Langkah Perhitungan Detail per Iterasi ($C_1, L_1, C_2, L_2, C_3, L_3$ & Rumus).
  * Tab 4: Dataset Transaksi Sumber.
* **Penyimpanan & Riwayat Analisis:** Simpan hasil analisis ke tabel `hasil_apriori` untuk dokumentasi dan cetak laporan resmi.

### 5. Modul Laporan & Dashboard
* **Dashboard Interaktif:** Grafik tren omset 7 hari terakhir, ringkasan omset hari ini, omset bulan ini, laba kotor, valuasi aset stok, dan peringatan produk menipis.
* **Laporan Penjualan:** Rekapitulasi transaksi, omset kotor, HPP modal, dan laba bersih dengan filter rentang tanggal serta fitur cetak.
* **Laporan Stok:** Monitoring stok gudang, filter status (Aman, Menipis, Habis), valuasi modal persediaan, dan cetak laporan stok.

---

## 📐 Rumus Matematika Algoritma Apriori

1. **Support Itemset ($A$):**
   $$\text{Support}(A) = \frac{\text{Jumlah Transaksi Mengandung } A}{\text{Total Transaksi } (N)} \times 100\%$$

2. **Support Gabungan ($A \cup B$):**
   $$\text{Support}(A \cup B) = \frac{\text{Jumlah Transaksi Mengandung } A \text{ dan } B}{\text{Total Transaksi } (N)} \times 100\%$$

3. **Confidence ($A \implies B$):**
   $$\text{Confidence}(A \implies B) = \frac{\text{Support}(A \cup B)}{\text{Support}(A)} \times 100\% = \frac{\text{Count}(A \cup B)}{\text{Count}(A)} \times 100\%$$

4. **Lift Ratio ($A \implies B$):**
   $$\text{Lift Ratio}(A \implies B) = \frac{\text{Confidence}(A \implies B)}{\text{Support}(B)} = \frac{P(A \cap B)}{P(A) \times P(B)}$$
   * $\text{Lift} > 1$: Korelasi positif kuat (aturan asosiasi sangat valid dan bermanfaat secara bisnis).
   * $\text{Lift} = 1$: Independen (tidak ada hubungan saling mempengaruhi).
   * $\text{Lift} < 1$: Korelasi negatif.

---

## 📁 Struktur Direktori Proyek

```
c:\xampp\htdocs\asoka\
├── config/
│   ├── config.php              # Konfigurasi aplikasi, base URL, identitas toko
│   ├── database.php            # Koneksi PDO singleton ke MySQL
│   └── auth.php                # Session & Role-Based Authorization
├── app/
│   ├── models/
│   │   ├── User.php            # Model Pengguna
│   │   ├── Kategori.php        # Model Kategori
│   │   ├── Barang.php          # Model Barang Sembako & Stok
│   │   ├── Transaksi.php       # Model Transaksi POS & Laporan
│   │   └── Apriori.php         # Engine Algoritma Apriori & Business Insights
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── KategoriController.php
│   │   ├── BarangController.php
│   │   ├── UserController.php
│   │   ├── PosController.php
│   │   ├── TransaksiController.php
│   │   ├── AprioriController.php
│   │   └── LaporanController.php
│   └── views/
│       ├── layouts/            # Header, Sidebar, Footer, Alerts
│       ├── auth/               # Halaman Login
│       ├── dashboard/          # Dashboard & Grafik Chart.js
│       ├── kategori/           # Master Kategori
│       ├── barang/             # Master Data Barang
│       ├── users/              # Manajemen Pengguna
│       ├── pos/                # Kasir POS & Struk Thermal
│       ├── transaksi/          # Riwayat Transaksi & Detail
│       ├── apriori/            # Parameter Apriori, Hasil Visualisasi & Cetak
│       └── laporan/            # Laporan Penjualan & Laporan Stok
├── assets/
│   ├── css/
│   │   ├── custom.css          # Desain antarmuka modern
│   │   └── receipt.css         # Styling struk thermal printer
│   └── js/
│       └── pos.js              # Interaktivitas keranjang kasir POS
├── database/
│   └── db_kasir_apriori.sql    # Dump SQL skema dan data sembako lengkap
├── index.php                   # Front Controller / Router
└── README.md                   # Dokumentasi lengkap sistem
```

