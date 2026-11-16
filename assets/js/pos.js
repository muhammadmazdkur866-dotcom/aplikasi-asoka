/**
 * POS Kasir Interaktivitas & State Keranjang Belanja
 * Toko Sembako Asoka
 */

let cart = [];
let currentCategory = '';
let currentSearch = '';

// Inisialisasi Event Listener
document.addEventListener('DOMContentLoaded', function () {
    renderCart();

    // Event Search Input
    const searchInput = document.getElementById('searchProductInput');
    if (searchInput) {
        searchInput.addEventListener('input', function (e) {
            currentSearch = e.target.value.toLowerCase().trim();
            filterProducts();
        });

        // Barcode enter key trigger
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const term = e.target.value.trim();
                if (term) {
                    // Cari produk dengan kode id_barang tepat sama
                    const match = window.allProducts.find(p => p.id_barang.toLowerCase() === term.toLowerCase());
                    if (match) {
                        addToCart(match);
                        e.target.value = '';
                        currentSearch = '';
                        filterProducts();
                    }
                }
            }
        });
    }

    // Input Uang Tunai Realtime Calc Kembalian
    const inputUangTunai = document.getElementById('inputUangTunai');
    if (inputUangTunai) {
        inputUangTunai.addEventListener('input', function () {
            calculateKembalian();
        });
    }

    // Keyboard Shortcuts Global
    window.addEventListener('keydown', function (e) {
        // F2: Fokus ke input pencarian
        if (e.key === 'F2') {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        // F8: Buka modal pembayaran jika keranjang ada isi
        if (e.key === 'F8') {
            e.preventDefault();
            if (cart.length > 0) {
                openPaymentModal();
            }
        }
        // Escape: Tutup modal atau batalkan
        if (e.key === 'Escape') {
            const payModalEl = document.getElementById('modalPembayaran');
            const payModal = bootstrap.Modal.getInstance(payModalEl);
            if (payModal) {
                payModal.hide();
            }
        }
    });
});

/**
 * Filter produk di grid berdasarkan kategori & kata kunci
 */
function setCategoryFilter(kategoriId, element) {
    currentCategory = kategoriId ? String(kategoriId) : '';

    // Update active tab button style
    document.querySelectorAll('.cat-pill').forEach(btn => {
        btn.classList.remove('btn-dark');
        btn.classList.add('btn-light');
    });
    if (element) {
        element.classList.remove('btn-light');
        element.classList.add('btn-dark');
    }

    filterProducts();
}

function filterProducts() {
    const cards = document.querySelectorAll('.product-item-col');
    cards.forEach(card => {
        const catId = card.getAttribute('data-category');
        const pName = card.getAttribute('data-name').toLowerCase();
        const pCode = card.getAttribute('data-code').toLowerCase();

        const matchCat = (!currentCategory || catId === currentCategory);
        const matchSearch = (!currentSearch || pName.includes(currentSearch) || pCode.includes(currentSearch));

        if (matchCat && matchSearch) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

/**
 * Tambah barang ke keranjang
 */
function addToCart(product) {
    const existingIndex = cart.findIndex(item => item.id_barang === product.id_barang);

    if (existingIndex > -1) {
        // Cek stok maksimum
        if (cart[existingIndex].kuantitas + 1 > product.stok) {
            alert(`Stok produk "${product.nama_barang}" hanya tersisa ${product.stok} ${product.satuan}.`);
            return;
        }
        cart[existingIndex].kuantitas += 1;
        cart[existingIndex].subtotal = cart[existingIndex].kuantitas * cart[existingIndex].harga_jual;
    } else {
        if (product.stok <= 0) {
            alert(`Produk "${product.nama_barang}" sedang habis!`);
            return;
        }
        cart.push({
            id_barang: product.id_barang,
            nama_barang: product.nama_barang,
            harga_jual: parseFloat(product.harga_jual),
            kuantitas: 1,
            stok: parseInt(product.stok),
            satuan: product.satuan,
            subtotal: parseFloat(product.harga_jual)
        });
    }

    renderCart();
}

/**
 * Update kuantitas barang
 */
function updateQty(idBarang, delta) {
    const index = cart.findIndex(item => item.id_barang === idBarang);
    if (index > -1) {
        const newQty = cart[index].kuantitas + delta;
        if (newQty <= 0) {
            removeFromCart(idBarang);
            return;
        }
        if (newQty > cart[index].stok) {
            alert(`Stok tidak mencukupi! Maksimal: ${cart[index].stok} ${cart[index].satuan}.`);
            return;
        }
        cart[index].kuantitas = newQty;
        cart[index].subtotal = cart[index].kuantitas * cart[index].harga_jual;
        renderCart();
    }
}

function setQtyDirect(idBarang, inputEl) {
    let val = parseInt(inputEl.value);
    const index = cart.findIndex(item => item.id_barang === idBarang);
    if (index > -1) {
        if (isNaN(val) || val <= 0) {
            val = 1;
        }
        if (val > cart[index].stok) {
            alert(`Stok tidak mencukupi! Maksimal: ${cart[index].stok} ${cart[index].satuan}.`);
            val = cart[index].stok;
        }
        cart[index].kuantitas = val;
        cart[index].subtotal = cart[index].kuantitas * cart[index].harga_jual;
        renderCart();
    }
}

/**
 * Hapus barang dari keranjang
 */
function removeFromCart(idBarang) {
    cart = cart.filter(item => item.id_barang !== idBarang);
    renderCart();
}

/**
 * Reset Keranjang
 */
function clearCart() {
    if (cart.length === 0) return;
    if (confirm('Apakah Anda yakin ingin mengosongkan keranjang belanja kasir?')) {
        cart = [];
        renderCart();
    }
}

/**
 * Format Rupiah JS Helper
 */
function formatRupiahJS(number) {
    return 'Rp ' + Number(number).toLocaleString('id-ID');
}

/**
 * Render Tabel Keranjang & Total
 */
function renderCart() {
    const tbody = document.getElementById('cartTableBody');
    const grandTotalEl = document.getElementById('cartGrandTotal');
    const totalItemsEl = document.getElementById('cartTotalItems');
    const btnBayar = document.getElementById('btnBayarModal');

    if (!tbody) return;

    tbody.innerHTML = '';
    let grandTotal = 0;
    let totalItems = 0;

    if (cart.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                    <i class="bi bi-cart-x fs-1 d-block mb-2 text-secondary"></i>
                    Keranjang masih kosong.<br>Pilih produk di sebelah kiri atau ketik barcode (F2).
                </td>
            </tr>
        `;
        if (btnBayar) btnBayar.disabled = true;
    } else {
        cart.forEach((item, idx) => {
            grandTotal += item.subtotal;
            totalItems += item.kuantitas;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="ps-3">
                    <div class="fw-semibold text-dark">${item.nama_barang}</div>
                    <small class="text-muted">${formatRupiahJS(item.harga_jual)} / ${item.satuan}</small>
                </td>
                <td class="text-center" style="width: 140px;">
                    <div class="input-group input-group-sm">
                        <button class="btn btn-outline-secondary" type="button" onclick="updateQty('${item.id_barang}', -1)">-</button>
                        <input type="number" class="form-control text-center px-1" value="${item.kuantitas}" min="1" max="${item.stok}" onchange="setQtyDirect('${item.id_barang}', this)">
                        <button class="btn btn-outline-secondary" type="button" onclick="updateQty('${item.id_barang}', 1)">+</button>
                    </div>
                </td>
                <td class="text-end fw-bold text-dark">
                    ${formatRupiahJS(item.subtotal)}
                </td>
                <td class="text-center pe-3" style="width: 40px;">
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeFromCart('${item.id_barang}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
        if (btnBayar) btnBayar.disabled = false;
    }

    if (grandTotalEl) grandTotalEl.textContent = formatRupiahJS(grandTotal);
    if (totalItemsEl) totalItemsEl.textContent = totalItems + ' Item';
}

/**
 * Buka Modal Pembayaran
 */
function openPaymentModal() {
    if (cart.length === 0) return;

    let grandTotal = cart.reduce((acc, it) => acc + it.subtotal, 0);

    document.getElementById('modalTotalBayarText').textContent = formatRupiahJS(grandTotal);
    document.getElementById('modalTotalBayarRaw').value = grandTotal;

    const inputUangTunai = document.getElementById('inputUangTunai');
    inputUangTunai.value = '';
    calculateKembalian();

    const modal = new bootstrap.Modal(document.getElementById('modalPembayaran'));
    modal.show();

    setTimeout(() => {
        inputUangTunai.focus();
    }, 400);
}

/**
 * Tombol Uang Pecahan Cepat
 */
function setUangCepat(nominal) {
    const inputUangTunai = document.getElementById('inputUangTunai');
    const grandTotal = parseFloat(document.getElementById('modalTotalBayarRaw').value) || 0;

    if (nominal === 'pas') {
        inputUangTunai.value = grandTotal;
    } else {
        inputUangTunai.value = nominal;
    }

    calculateKembalian();
}

/**
 * Hitung Kembalian
 */
function calculateKembalian() {
    const grandTotal = parseFloat(document.getElementById('modalTotalBayarRaw').value) || 0;
    const uangTunai = parseFloat(document.getElementById('inputUangTunai').value) || 0;
    const kembalian = uangTunai - grandTotal;

    const kembalianText = document.getElementById('modalKembalianText');
    const kembalianAlert = document.getElementById('kembalianAlert');
    const btnSubmit = document.getElementById('btnSubmitTransaksi');

    if (uangTunai >= grandTotal && grandTotal > 0) {
        kembalianText.textContent = formatRupiahJS(kembalian);
        kembalianText.className = 'fs-4 fw-bold text-dark';
        kembalianAlert.classList.add('d-none');
        btnSubmit.disabled = false;
    } else {
        kembalianText.textContent = 'Rp 0';
        kembalianText.className = 'fs-4 fw-bold text-muted';
        if (uangTunai > 0 && uangTunai < grandTotal) {
            kembalianAlert.classList.remove('d-none');
            kembalianAlert.textContent = `Uang tunai kurang ${formatRupiahJS(grandTotal - uangTunai)}`;
        } else {
            kembalianAlert.classList.add('d-none');
        }
        btnSubmit.disabled = true;
    }
}

/**
 * Simpan Transaksi ke Server via AJAX
 */
function submitTransaksi() {
    const grandTotal = parseFloat(document.getElementById('modalTotalBayarRaw').value) || 0;
    const uangTunai = parseFloat(document.getElementById('inputUangTunai').value) || 0;
    const kembalian = uangTunai - grandTotal;
    const catatan = document.getElementById('catatanTransaksi').value;

    if (uangTunai < grandTotal) {
        alert('Uang pembayaran masih kurang!');
        return;
    }

    const payload = {
        total_bayar: grandTotal,
        uang_tunai: uangTunai,
        kembalian: kembalian,
        catatan: catatan,
        items: cart
    };

    const btnSubmit = document.getElementById('btnSubmitTransaksi');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    fetch(window.BASE_URL + 'index.php?page=pos&action=simpan_transaksi', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="bi bi-printer me-1"></i> Bayar & Cetak Struk';

        if (data.status === 'success') {
            // Tutup modal bayar
            const modalEl = document.getElementById('modalPembayaran');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            // Buka struk di popup print window
            const printUrl = data.redirect_print;
            const printWindow = window.open(printUrl, 'Struk Kasir', 'width=420,height=600');
            if (printWindow) {
                printWindow.focus();
            }

            // Reset cart
            cart = [];
            renderCart();

            alert('Transaksi sukses disimpan!');
            // Refresh halaman agar stok di katalog kasir terupdate
            window.location.reload();
        } else {
            alert('Gagal menyimpan transaksi: ' + data.message);
        }
    })
    .catch(err => {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="bi bi-printer me-1"></i> Bayar & Cetak Struk';
        alert('Terjadi kesalahan jaringan: ' + err.message);
    });
}

