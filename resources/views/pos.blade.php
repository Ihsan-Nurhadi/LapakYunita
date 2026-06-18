@extends('layouts.app')

@push('styles')
<style>
    *, *::before, *::after {box-sizing: border-box;}
    .pos-shell { display:grid; grid-template-columns:260px 1fr; gap:20px; align-items:start; min-height:calc(100vh - 40px); }
    .sidebar { background:#fff; border-radius:24px; border:1px solid rgba(15,23,42,.08); padding:24px; display:flex; flex-direction:column; gap:24px; }
    .sidebar .brand { font-size:1.4rem; font-weight:800; letter-spacing:.01em; }
    .sidebar .brand-desc { color:#475569; margin-top:4px; }
    .sidebar nav { display:flex; flex-direction:column; gap:10px; }
    .sidebar button.menu-item { border:0; background:#f8fafc; color:#0f172a; text-align:left; width:100%; padding:14px 16px; font-size:.98rem; border-radius:16px; transition:all .18s ease; display:flex; align-items:center; gap:10px; }
    .sidebar button.menu-item.active, .sidebar button.menu-item:hover { background:#10b981; color:#fff; box-shadow:0 20px 45px rgba(16,185,129,.18); }
    .sidebar .menu-label { display:flex; align-items:center; gap:10px; }
    .sidebar .footer-persona { margin-top:auto; display:flex; align-items:center; gap:12px; padding-top:12px; border-top:1px solid rgba(15,23,42,.08); }
    .sidebar .avatar { width:44px; height:44px; border-radius:50%; display:grid; place-items:center; background:#16a34a; color:#fff; font-weight:800; }
    .workspace { display:flex; flex-direction:column; gap:18px; }
    .page-header { display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:flex-start; }
    .page-header h2 { margin:0; font-size:1.8rem; }
    .page-header p { margin:6px 0 0; color:#475569; max-width:640px; }
    .primary-btn { background:#16a34a; color:#fff; border:0; border-radius:16px; padding:14px 20px; font-weight:700; box-shadow:0 16px 32px rgba(22,163,74,.18); }
    .secondary-btn { background:#f8fafc; color:#0f172a; border:1px solid rgba(15,23,42,.12); border-radius:16px; padding:12px 18px; font-weight:700; }
    .search-box { width:100%; display:flex; align-items:center; gap:12px; background:#fff; border:1px solid rgba(15,23,42,.08); border-radius:16px; padding:10px 14px; }
    .search-box input { width:100%; border:0; outline:none; font-size:1rem; color:#0f172a; }
    .card-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:18px; }
    .product-card { background:#fff; border-radius:24px; border:1px solid rgba(15,23,42,.06); padding:18px; display:flex; flex-direction:column; gap:16px; min-height:260px; }
    .product-card .top { display:flex; justify-content:space-between; gap:14px; }
    .product-card .icon { width:56px; height:56px; border-radius:18px; display:grid; place-items:center; font-size:1.25rem; background:#ecfdf5; color:#16a34a; }
    .product-card .meta { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .product-card .tag { background:#eef6ff; color:#2563eb; border-radius:999px; padding:.45rem .85rem; font-size:.82rem; font-weight:700; }
    .product-card h3 { margin:0; font-size:1.05rem; }
    .product-card .stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:10px; }
    .product-card .stat { background:#f8fafc; border-radius:18px; padding:14px; text-align:center; }
    .product-card .stat span { display:block; color:#475569; font-size:.85rem; }
    .product-card .stat strong { display:block; margin-top:8px; font-size:1rem; }
    .product-card .actions { display:flex; gap:10px; flex-wrap:wrap; }
    .product-card .actions button { flex:1 1 120px; border:1px solid transparent; border-radius:16px; padding:10px 12px; font-weight:700; cursor:pointer; }
    .btn-edit { background:#ecfdf5; color:#166534; border-color:#d1fae5; }
    .btn-delete { background:#fef2f2; color:#991b1b; border-color:#fecaca; }
    .btn-add { background:#eff6ff; color:#1d4ed8; border-color:#dbeafe; }
    .cart-panel { background:#fff; border-radius:24px; border:1px solid rgba(15,23,42,.08); padding:22px; display:flex; flex-direction:column; gap:18px; min-height:420px; }
    .cart-panel h3 { margin:0; }
    .cart-item { display:flex; justify-content:space-between; gap:10px; background:#f8fafc; border-radius:16px; padding:12px 14px; }
    .cart-item span { color:#0f172a; font-weight:600; }
    .cart-item-details { display:flex; flex-direction:column; gap:10px; }
    .cart-item-controls { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .cart-item-controls button { border:1px solid rgba(15,23,42,.12); background:#fff; color:#0f172a; border-radius:12px; padding:6px 10px; cursor:pointer; font-weight:700; }
    .cart-item-controls button:hover { filter:brightness(0.95); }
    .cart-item-controls .btn-remove { background:#fee2e2; color:#991b1b; border-color:#fecaca; }
    .cart-item-controls .cart-qty { min-width:28px; text-align:center; }
    .cart-summary { display:grid; gap:12px; }
    .cart-summary .row { display:flex; justify-content:space-between; color:#475569; }
    .cart-summary .row.total { font-size:1.15rem; font-weight:700; color:#0f172a; }
    .empty-state { padding:42px 0; text-align:center; color:#64748b; background:#f8fafc; border-radius:18px; }
    .report-summary-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:18px; margin-bottom:24px; }
    .report-summary-card { background:#fff; border-radius:24px; border:1px solid rgba(15,23,42,.08); padding:24px; display:flex; flex-direction:column; gap:12px; }
    .report-summary-card .label { color:#475569; font-size:.95rem; }
    .report-summary-card .value { font-size:1.5rem; font-weight:800; color:#0f172a; }
    .report-summary-card .subtitle { color:#10b981; font-size:.95rem; font-weight:700; }
    .report-card { background:#fff; border-radius:24px; border:1px solid rgba(15,23,42,.08); padding:22px; }
    .report-card h3 { margin:0 0 16px; font-size:1.1rem; }
    .report-row { display:flex; justify-content:space-between; gap:12px; padding:12px 0; border-bottom:1px solid rgba(15,23,42,.06); }
    .report-row:last-child { border-bottom:0; }
    .report-label { color:#475569; }
    .report-value { font-weight:700; color:#0f172a; }
    .report-product { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid rgba(15,23,42,.06); }
    .report-product:last-child { border-bottom:0; }
    .product-bar { height:8px; border-radius:999px; background:#d1fae5; overflow:hidden; margin-top:8px; }
    .product-bar-fill { height:100%; border-radius:999px; background:#10b981; }
    .transaction-item { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; padding:14px 0; border-bottom:1px solid rgba(15,23,42,.06); }
    .transaction-item:last-child { border-bottom:0; }
    .transaction-info { display:flex; flex-direction:column; gap:6px; }
    .transaction-id { font-weight:700; color:#0f172a; }
    .transaction-meta { color:#64748b; font-size:.95rem; }
    .note-text { margin-top:14px; color:#64748b; font-size:.92rem; }
    .export-btn { background:#ef4444; color:#fff; border:0; border-radius:999px; padding:14px 28px; font-weight:700; box-shadow:0 18px 38px rgba(239,68,68,.18); }
    .section-title { margin:0 0 12px; font-size:1rem; font-weight:700; }
    .info-card { background:#fff; border-radius:24px; border:1px solid rgba(15,23,42,.08); padding:20px; }
    .info-card .label { color:#475569; font-size:.9rem; }
    .info-card .value { font-size:1.2rem; font-weight:700; margin-top:8px; }
    .user-card { display:flex; flex-direction:column; justify-content:space-between; gap:12px; }
    .user-card .meta { color:#475569; font-size:.92rem; }

    .modal-backdrop { position:fixed; inset:0; background:rgba(15,23,42,.45); display:grid; place-items:center; padding:20px; z-index:50; }
    .modal-pane { 
        background: #fff; 
        border-radius: 24px; 
        max-width: 520px; 
        width: 100%; 
        padding: 28px; /* Sedikit diperlebar agar lebih lega */
        box-shadow: 0 34px 88px rgba(15,23,42,.18); 
    }

    .modal-pane h3 { 
        margin: 0 0 20px; 
        font-size: 1.4rem;
        font-weight: 800;
        color: #0f172a;
    }

    /* Merapikan label agar lebih tegas dan profesional */
    .modal-pane label { 
        display: block; 
        font-size: .9rem; 
        font-weight: 600; /* Membuat teks label semi-bold */
        margin-top: 16px; 
        color: #475569; /* Warna abu-abu gelap khas UI modern */
    }

    /* Merapikan input box */
    .modal-pane input { 
        width: 100%; 
        border: 1px solid rgba(15, 23, 42, .14); 
        border-radius: 14px; 
        padding: 12px 16px; 
        margin-top: 6px; 
        font-size: .98rem; 
        color: #0f172a;
        background: #f8fafc; /* Memberi warna dasar lembut agar kontras dengan modal */
        outline: none;
        transition: all 0.2s ease; /* Animasi halus saat diklik */
    }

    /* 3. TAMBAHKAN INI: Efek Interaktif saat Input Box diklik (Fokus) */
    .modal-pane input:focus {
        background: #fff;
        border-color: #10b981; /* Berubah jadi hijau emerald, senada dengan tombol Anda */
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.12); /* Efek glow lembut */
    }

    /* Mengatur placeholder agar tidak terlalu tebal */
    .modal-pane input::placeholder {
        color: #94a3b8;
        font-size: 0.9rem;
    }

    .modal-actions { 
        display: flex; 
        justify-content: flex-end; 
        gap: 12px; 
        margin-top: 28px; /* Memberi jarak aman dari input terakhir */
    }
        .hidden { display:none !important; }

    @media print {
        body * {
            visibility: hidden;
        }
        #invoice-print-area, #invoice-print-area * {
            visibility: visible;
        }
        #invoice-print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 80mm;
            margin: 0;
            padding: 10px;
            background: white;
            color: black;
        }
        .modal-backdrop {
            position: absolute !important;
            inset: 0 !important;
            background: transparent !important;
            display: block !important;
        }
        .modal-pane {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            background: transparent !important;
        }
    }
    </style>
    @endpush

@section('content')
<!-- Login Screen Overlay -->
<div id="login-screen" class="modal-backdrop hidden" style="backdrop-filter: blur(12px); background: rgba(15,23,42,0.6);">
    <div class="modal-pane" style="max-width: 400px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <div style="font-size: 3rem; margin-bottom: 8px;">🛒</div>
        <h3 style="margin: 0 0 4px; font-size: 1.6rem; font-weight: 800; color: #0f172a;">KasirPOS</h3>
        <p style="margin: 0 0 24px; color: #64748b; font-size: 0.95rem;">Pilih nama Anda dan masukkan PIN untuk mulai</p>
        
        <form id="login-form">
            <div style="text-align: left; margin-bottom: 16px;">
                <label for="login-employee-select" style="font-size: 0.9rem; font-weight: 600; color: #475569; display: block; margin-bottom: 6px;">Nama Pegawai</label>
                <select id="login-employee-select" required style="width: 100%; border: 1px solid rgba(15, 23, 42, .14); border-radius: 14px; padding: 12px 14px; font-size: 1rem; color: #0f172a; background: #f8fafc; outline: none; transition: all 0.2s ease;">
                    <option value="">-- Pilih Nama --</option>
                </select>
            </div>
            
            <div style="margin-bottom: 24px;">
                <label style="font-size: 0.9rem; font-weight: 600; color: #475569; display: block; margin-bottom: 8px; text-align: left;">Masukkan 4-Digit PIN</label>
                <div style="display: flex; justify-content: center; gap: 16px; margin-bottom: 20px;">
                    <span class="pin-dot" style="width: 16px; height: 16px; border-radius: 50%; background: #e2e8f0; border: 2px solid #cbd5e1; transition: all 0.15s ease;"></span>
                    <span class="pin-dot" style="width: 16px; height: 16px; border-radius: 50%; background: #e2e8f0; border: 2px solid #cbd5e1; transition: all 0.15s ease;"></span>
                    <span class="pin-dot" style="width: 16px; height: 16px; border-radius: 50%; background: #e2e8f0; border: 2px solid #cbd5e1; transition: all 0.15s ease;"></span>
                    <span class="pin-dot" style="width: 16px; height: 16px; border-radius: 50%; background: #e2e8f0; border: 2px solid #cbd5e1; transition: all 0.15s ease;"></span>
                </div>
                <!-- Number pad -->
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; max-width: 280px; margin: 0 auto;">
                    <button type="button" class="pin-btn" onclick="pressPin('1')" style="height: 56px; border-radius: 16px; border: 1px solid rgba(15,23,42,0.06); background: #f8fafc; font-size: 1.3rem; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">1</button>
                    <button type="button" class="pin-btn" onclick="pressPin('2')" style="height: 56px; border-radius: 16px; border: 1px solid rgba(15,23,42,0.06); background: #f8fafc; font-size: 1.3rem; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">2</button>
                    <button type="button" class="pin-btn" onclick="pressPin('3')" style="height: 56px; border-radius: 16px; border: 1px solid rgba(15,23,42,0.06); background: #f8fafc; font-size: 1.3rem; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">3</button>
                    <button type="button" class="pin-btn" onclick="pressPin('4')" style="height: 56px; border-radius: 16px; border: 1px solid rgba(15,23,42,0.06); background: #f8fafc; font-size: 1.3rem; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">4</button>
                    <button type="button" class="pin-btn" onclick="pressPin('5')" style="height: 56px; border-radius: 16px; border: 1px solid rgba(15,23,42,0.06); background: #f8fafc; font-size: 1.3rem; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">5</button>
                    <button type="button" class="pin-btn" onclick="pressPin('6')" style="height: 56px; border-radius: 16px; border: 1px solid rgba(15,23,42,0.06); background: #f8fafc; font-size: 1.3rem; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">6</button>
                    <button type="button" class="pin-btn" onclick="pressPin('7')" style="height: 56px; border-radius: 16px; border: 1px solid rgba(15,23,42,0.06); background: #f8fafc; font-size: 1.3rem; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">7</button>
                    <button type="button" class="pin-btn" onclick="pressPin('8')" style="height: 56px; border-radius: 16px; border: 1px solid rgba(15,23,42,0.06); background: #f8fafc; font-size: 1.3rem; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">8</button>
                    <button type="button" class="pin-btn" onclick="pressPin('9')" style="height: 56px; border-radius: 16px; border: 1px solid rgba(15,23,42,0.06); background: #f8fafc; font-size: 1.3rem; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">9</button>
                    <button type="button" class="pin-btn" onclick="pressPin('clear')" style="height: 56px; border-radius: 16px; border: 1px solid rgba(239,68,68,0.1); background: #fef2f2; color: #ef4444; font-size: 1rem; font-weight: 700; cursor: pointer;">C</button>
                    <button type="button" class="pin-btn" onclick="pressPin('0')" style="height: 56px; border-radius: 16px; border: 1px solid rgba(15,23,42,0.06); background: #f8fafc; font-size: 1.3rem; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">0</button>
                    <button type="button" class="pin-btn" onclick="pressPin('delete')" style="height: 56px; border-radius: 16px; border: 1px solid rgba(15,23,42,0.06); background: #f1f5f9; font-size: 1.2rem; cursor: pointer;">⌫</button>
                </div>
            </div>
            <div id="login-error" style="color: #ef4444; font-size: 0.9rem; font-weight: 600; margin-top: -12px; margin-bottom: 16px;" class="hidden"></div>
            <div style="margin-bottom: 16px; text-align: right;">
                <a href="#" onclick="openChangePinModal(event)" style="font-size:0.9rem; color:#10b981; font-weight:600; text-decoration:none;">Ganti PIN?</a>
            </div>
            <button type="submit" class="primary-btn" style="width: 100%; font-size: 1.05rem; padding: 14px;">Masuk Kerja</button>
        </form>
    </div>
</div>

<div class="pos-shell hidden" id="pos-app-shell">
    <aside class="sidebar">
        <div>
            <div class="brand">KasirPOS</div>
            <div class="brand-desc">Sistem Kasir Online</div>
        </div>

        <nav>
            <button class="menu-item active" id="menu-transaksi" data-page="transaksi">🛒 Transaksi</button>
            <button class="menu-item" id="menu-produk" data-page="produk">📦 Produk</button>
            <button class="menu-item" id="menu-pegawai" data-page="pegawai">👥 Pegawai</button>
            <button class="menu-item" id="menu-outlet" data-page="outlet">🏪 Outlet</button>
            <button class="menu-item" id="menu-laporan" data-page="laporan">📊 Laporan</button>
        </nav>

        <div class="footer-persona" style="display:flex; flex-direction:column; gap:12px; align-items:stretch; padding-top:12px; border-top:1px solid rgba(15,23,42,.08);">
            <div style="display:flex; align-items:center; gap:12px;">
                <div class="avatar" id="current-user-avatar">?</div>
                <div style="flex:1; min-width:0;">
                    <div style="font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" id="current-user-name">-</div>
                    <div style="font-size:.9rem;color:#64748b;" id="current-user-role">-</div>
                </div>
            </div>
            <button type="button" class="secondary-btn" onclick="logoutEmployee()" style="width:100%; padding:8px 12px; font-size:0.9rem; border-color:#fee2e2; color:#ef4444; background:#fffcfc; display:flex; align-items:center; justify-content:center; gap:8px;">
                <span>🚪</span> Keluar
            </button>
        </div>
    </aside>

    <main class="workspace">
        <section class="page-header">
            <div>
                <h2 id="page-title">Transaksi</h2>
                <p id="page-subtitle">Kelola penjualan, cari produk, dan selesaikan pembayaran dengan cepat.</p>
            </div>
            <button id="action-button" class="primary-btn">+ Tambah Produk</button>
        </section>

        <section id="page-content"></section>
    </main>
</div>

<div id="product-modal" class="modal-backdrop hidden" onclick="closeModal(event)">
    <div class="modal-pane" onclick="event.stopPropagation()">
        <h3 id="modal-title">Tambah Produk</h3>
        <form id="product-form">
            <label>Nama Produk</label>
            <input type="text" name="name" id="field-name" required />
            <label>Kategori</label>
            <input type="text" name="category" id="field-category" placeholder="Makanan, Minuman, Snack" />
            <label>Harga Jual</label>
            <input type="number" name="price" id="field-price" required />
            <label>Harga Modal</label>
            <input type="number" name="modal" id="field-modal" />
            <label>Stok Saat Ini</label>
            <input type="number" id="field-current-stock" disabled style="background: #cbd5e1; cursor: not-allowed;" value="0" />
            
            <label>Tambah Stok</label>
            <input type="number" id="field-add-stock" placeholder="Masukkan jumlah stok tambahan" value="0" />

            <label>Outlet</label>
            <select id="field-outlet" style="width:100%; border:1px solid rgba(15, 23, 42, .14); border-radius:14px; padding:12px 14px; margin-top:6px; font-size:1rem; color:#0f172a; background:#f8fafc; outline:none;">
            </select>

            <label>Gambar Produk</label>
            <input type="file" name="image" id="field-image" accept="image/*" />
            <div id="product-image-preview" style="margin-top:8px;color:#475569;font-size:.92rem;"></div>
            <div class="modal-actions">
                <button type="button" class="secondary-btn" onclick="closeModal()">Batal</button>
                <button type="submit" class="primary-btn">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="invoice-modal" class="modal-backdrop hidden" onclick="closeModal(event)">
    <div class="modal-pane" onclick="event.stopPropagation()" style="max-width: 400px; font-family: monospace;">
        <div id="invoice-print-area">
            <div style="text-align: center; margin-bottom: 15px;">
                <h3 style="margin: 0; font-size: 1.3rem; font-weight: 800;" id="invoice-outlet-name">Lapak Yunita</h3>
                <p style="margin: 4px 0; color: #475569; font-size: 0.9rem;" id="invoice-outlet-address">Outlet Pusat</p>
                <div style="border-top: 1px dashed #cbd5e1; margin-top: 10px;"></div>
            </div>
            
            <table style="width: 100%; font-size: 0.9rem; border-collapse: collapse; margin-bottom: 12px;">
                <tr>
                    <td style="color: #64748b; padding: 2px 0;">No:</td>
                    <td style="text-align: right; font-weight: bold;" id="invoice-trx-id">-</td>
                </tr>
                <tr>
                    <td style="color: #64748b; padding: 2px 0;">Tanggal:</td>
                    <td style="text-align: right;" id="invoice-date">-</td>
                </tr>
                <tr>
                    <td style="color: #64748b; padding: 2px 0;">Kasir:</td>
                    <td style="text-align: right;" id="invoice-cashier">-</td>
                </tr>
            </table>

            <div style="border-top: 1px dashed #cbd5e1; margin-bottom: 12px;"></div>

            <div id="invoice-items" style="font-size: 0.9rem; margin-bottom: 12px;">
                <!-- Items list -->
            </div>

            <div style="border-top: 1px dashed #cbd5e1; margin-top: 10px; padding-top: 10px;"></div>

            <table style="width: 100%; font-size: 0.9rem; border-collapse: collapse;">
                <tr>
                    <td style="padding: 4px 0; font-weight: bold;">Total</td>
                    <td style="text-align: right; font-weight: bold;" id="invoice-total">Rp 0</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #64748b;">Bayar</td>
                    <td style="text-align: right;" id="invoice-paid">Rp 0</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0; color: #64748b;">Kembali</td>
                    <td style="text-align: right;" id="invoice-change">Rp 0</td>
                </tr>
            </table>

            <div style="border-top: 1px dashed #cbd5e1; margin-top: 15px; padding-top: 10px; text-align: center;">
                <p style="margin: 0; color: #64748b; font-size: 0.85rem;">Terima Kasih atas Kunjungan Anda</p>
                <p style="margin: 4px 0 0; color: #94a3b8; font-size: 0.75rem;">LapakYunita POS</p>
            </div>
        </div>

        <div class="modal-actions no-print" style="margin-top: 20px;">
            <button type="button" class="secondary-btn" onclick="closeModal()">Tutup</button>
            <button type="button" class="primary-btn" onclick="printInvoice()">Cetak Struk</button>
        </div>
    </div>
</div>

<div id="payment-modal" class="modal-backdrop hidden" onclick="closeModal(event)">
    <div class="modal-pane" onclick="event.stopPropagation()" style="max-width: 440px;">
        <h3 style="margin: 0 0 16px; font-size: 1.4rem; font-weight: 800; color: #0f172a;">Pembayaran</h3>
        <form id="payment-form">
            <div style="background: #f8fafc; border-radius: 16px; padding: 16px; margin-bottom: 20px; border: 1px solid rgba(15,23,42,.06); display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #475569; font-weight: 600;">Total Tagihan:</span>
                <strong id="payment-total-label" style="font-size: 1.4rem; color: #0f172a; font-weight: 800;">Rp 0</strong>
            </div>
            
            <label for="payment-method-select" style="display: block; font-size: .9rem; font-weight: 600; color: #475569; margin-bottom: 8px;">Metode Pembayaran</label>
            <select id="payment-method-select" required style="width: 100%; border: 1px solid rgba(15, 23, 42, .14); border-radius: 14px; padding: 12px 14px; font-size: 1rem; color: #0f172a; background: #f8fafc; outline: none; margin-bottom: 16px;">
                <option value="cash">Cash (Uang Tunai)</option>
                <option value="qr">QRIS (QR)</option>
                <option value="tf">Transfer Bank (TF)</option>
            </select>

            <div id="qr-payment-details" class="hidden" style="margin-top: -8px; margin-bottom: 16px; text-align: center; background: #f0fdf4; padding: 16px; border-radius: 16px; border: 1px solid rgba(22,163,74,.15);">
                <p style="margin: 0 0 10px; color: #166534; font-weight: bold; font-size: 0.95rem;">Scan QRIS LapakYunita:</p>
                <img src="/qris_payment.png" alt="QRIS Payment" style="width: 200px; height: 200px; object-fit: cover; border-radius: 12px; border: 2px solid #16a34a; box-shadow: 0 10px 25px rgba(22,163,74,0.15);" />
            </div>

            <div id="tf-payment-details" class="hidden" style="margin-top: -8px; margin-bottom: 16px; background: #eff6ff; padding: 16px; border-radius: 16px; border: 1px solid rgba(37,99,235,.15); font-size: 0.9rem;">
                <p style="margin: 0 0 10px; color: #1e40af; font-weight: bold; font-size: 0.95rem;">Transfer ke Rekening Resmi:</p>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <div style="border-bottom: 1px dashed rgba(37,99,235,0.15); padding-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>BCA</strong>: <span style="font-family: monospace; font-size: 1rem; font-weight: 700;">8877-6655-44</span><br/>
                            <span style="color:#64748b; font-size:0.8rem;">a/n Lapak Yunita</span>
                        </div>
                    </div>
                    <div style="border-bottom: 1px dashed rgba(37,99,235,0.15); padding-bottom: 6px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>Mandiri</strong>: <span style="font-family: monospace; font-size: 1rem; font-weight: 700;">123-00-998877-6</span><br/>
                            <span style="color:#64748b; font-size:0.8rem;">a/n Lapak Yunita</span>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>BRI</strong>: <span style="font-family: monospace; font-size: 1rem; font-weight: 700;">0012-01-998877-50-3</span><br/>
                            <span style="color:#64748b; font-size:0.8rem;">a/n Lapak Yunita</span>
                        </div>
                    </div>
                </div>
            </div>

            <label for="payment-paid-input" style="display: block; font-size: .9rem; font-weight: 600; color: #475569; margin-bottom: 8px;">Uang Tunai (Bayar)</label>
            <input type="number" id="payment-paid-input" required placeholder="Masukkan jumlah uang" style="width: 100%; border: 1px solid rgba(15, 23, 42, .14); border-radius: 14px; padding: 14px 16px; font-size: 1.3rem; font-weight: 700; color: #0f172a; background: #f8fafc; outline: none; transition: all 0.2s ease;" />
            
            <div id="quick-cash-container" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 12px;">
                <!-- Quick cash buttons -->
            </div>

            <div style="background: #f0fdf4; border-radius: 16px; padding: 16px; margin-top: 20px; border: 1px solid rgba(22,163,74,.15); display: flex; justify-content: space-between; align-items: center;">
                <span style="color: #166534; font-weight: 600;">Kembalian:</span>
                <strong id="payment-change-label" style="font-size: 1.4rem; color: #166534; font-weight: 800;">Rp 0</strong>
            </div>

            <div class="modal-actions" style="margin-top: 24px;">
                <button type="button" class="secondary-btn" onclick="closeModal()">Batal</button>
                <button type="submit" class="primary-btn" id="confirm-payment-btn">Konfirmasi & Simpan</button>
            </div>
        </form>
    </div>
</div>

<template id="tpl-transaksi">
    <div class="grid">
        <div>
            <div class="search-box">
                <span>🔍</span>
                <input id="search" placeholder="Cari produk..." />
            </div>
            <div id="product-count" style="margin-top:18px;color:#475569;font-size:.95rem;"></div>
            <div id="products" class="card-grid" style="margin-top:16px"></div>
        </div>
        <aside class="cart-panel">
            <h3>Keranjang</h3>
            <div id="cart-items"></div>
            <div class="cart-summary">
                <div class="row"><span>Items</span><strong id="cart-count">0</strong></div>
                <div class="row total"><span>Total</span><strong id="cart-total">Rp 0</strong></div>
            </div>
            <button id="pay-btn" class="primary-btn">Bayar Sekarang</button>
        </aside>
    </div>
</template>

<template id="tpl-produk">
    <div>
        <div id="products-admin" class="card-grid"></div>
    </div>
</template>

<template id="tpl-pegawai">
    <div id="employees-admin" class="card-grid"></div>
</template>

<template id="tpl-outlet">
    <div id="outlets-admin" class="card-grid"></div>
</template>

<div id="employee-modal" class="modal-backdrop hidden" onclick="closeModal(event)">
    <div class="modal-pane" onclick="event.stopPropagation()">
        <h3>Tambah Pegawai</h3>
        <form id="employee-form">
            <label>Nama</label>
            <input type="text" id="employee-name" required />
            <label>Role</label>
            <select id="employee-role" style="width:100%;border:1px solid rgba(15,23,42,.12);border-radius:14px;padding:12px 14px;margin-top:8px;font-size:1rem;">
                <option value="Kasir">Kasir</option>
                <option value="Supervisor">Supervisor</option>
                <option value="Admin">Admin</option>
            </select>
            <label>PIN (4 Digit)</label>
            <input type="password" id="employee-pin" maxlength="4" placeholder="4 angka PIN" required />
            <label>Email</label>
            <input type="email" id="employee-email" />
            <label>Telepon</label>
            <input type="text" id="employee-phone" />
            <label>Foto Pegawai</label>
            <input type="file" id="employee-photo" accept="image/*" />
            <div id="employee-photo-preview" style="margin-top:8px;color:#475569;font-size:.92rem;"></div>
            <label>Outlet</label>
            <select id="employee-outlet" style="width:100%;border:1px solid rgba(15,23,42,.12);border-radius:14px;padding:12px 14px;margin-top:8px;font-size:1rem;"></select>
            <div class="modal-actions">
                <button type="button" class="secondary-btn" onclick="closeModal()">Batal</button>
                <button type="submit" class="primary-btn">Simpan Pegawai</button>
            </div>
        </form>
    </div>
</div>

<div id="outlet-modal" class="modal-backdrop hidden" onclick="closeModal(event)">
    <div class="modal-pane" onclick="event.stopPropagation()">
        <h3>Tambah Outlet</h3>
        <form id="outlet-form">
            <label>Nama Outlet</label>
            <input type="text" id="outlet-name" required />
            <label>Telepon</label>
            <input type="text" id="outlet-phone" />
            <label>Alamat</label>
            <input type="text" id="outlet-address" />
            <label>Foto Outlet</label>
            <input type="file" id="outlet-image" accept="image/*" />
            <div id="outlet-image-preview" style="margin-top:8px;color:#475569;font-size:.92rem;"></div>
            <label>Kelurahan</label>
            <input type="text" id="outlet-kelurahan" />
            <label>Kode Pos</label>
            <input type="text" id="outlet-kodepos" />
            <div class="modal-actions">
                <button type="button" class="secondary-btn" onclick="closeModal()">Batal</button>
                <button type="submit" class="primary-btn">Simpan Outlet</button>
            </div>
        </form>
    </div>
</div>

<div id="change-pin-modal" class="modal-backdrop hidden" onclick="closeModal(event)">
    <div class="modal-pane" onclick="event.stopPropagation()" style="max-width: 400px;">
        <h3 style="margin: 0 0 16px; font-size: 1.4rem; font-weight: 800; color: #0f172a;">Ganti PIN</h3>
        <form id="change-pin-form">
            <label>Nama Pegawai</label>
            <select id="change-pin-employee-select" required style="width: 100%; border: 1px solid rgba(15, 23, 42, .14); border-radius: 14px; padding: 12px 14px; font-size: 1rem; color: #0f172a; background: #f8fafc; outline: none; margin-bottom: 12px;">
            </select>
            
            <label>PIN Lama</label>
            <input type="password" id="change-pin-old" maxlength="4" required placeholder="Masukkan PIN lama" style="margin-bottom: 12px;" />
            
            <label>PIN Baru (4 Digit)</label>
            <input type="password" id="change-pin-new" maxlength="4" required placeholder="Masukkan PIN baru" style="margin-bottom: 12px;" />
            
            <div id="change-pin-error" style="color: #ef4444; font-size: 0.9rem; font-weight: 600; margin-bottom: 12px;" class="hidden"></div>
            
            <div class="modal-actions">
                <button type="button" class="secondary-btn" onclick="closeModal()">Batal</button>
                <button type="submit" class="primary-btn">Simpan PIN</button>
            </div>
        </form>
    </div>
</div>

<script>
const formatRupiah = n => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
const categoryIcon = cat => {
    if(!cat) return '📦';
    const key = cat.toLowerCase();
    if(key.includes('makan')) return '🍛';
    if(key.includes('minum')) return '🧋';
    if(key.includes('snack')) return '🥨';
    if(key.includes('buah')) return '🥑';
    if(key.includes('kopi')) return '☕';
    return '🛍️';
};

const resolveImageUrl = image => {
    if (!image) return null;
    if (image.startsWith('http://') || image.startsWith('https://')) return image;
    return '/storage/' + image;
};

const renderImageCircle = (image, alt, size = 56) => {
    if (!image) return null;
    const src = resolveImageUrl(image);
    return `<img src="${src}" alt="${alt}" style="width:${size}px;height:${size}px;border-radius:18px;object-fit:cover;" />`;
};

let PRODUCTS = [];
let EMPLOYEES = [];
let CART = [];
let OUTLETS = [];
let editingProduct = null;
let editingEmployee = null;
let editingOutlet = null;
let CURRENT_EMPLOYEE = null;
let pinBuffer = '';

function setActiveMenu(page) {
    document.querySelectorAll('.menu-item').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.page === page);
    });
}

function setPageHeader(title, subtitle, actionLabel = '+ Tambah Produk', showAction = true, actionFn = null) {
    document.getElementById('page-title').innerText = title;
    document.getElementById('page-subtitle').innerText = subtitle;
    const actionBtn = document.getElementById('action-button');
    actionBtn.innerText = actionLabel;
    actionBtn.style.display = showAction ? 'inline-flex' : 'none';
    actionBtn.onclick = actionFn || (() => {});
}

async function loadProducts(){
    const res = await fetch('/pos/api/products');
    PRODUCTS = await res.json();
}

function showPage(page){
    setActiveMenu(page);
    if(page === 'transaksi') {
        setPageHeader('Transaksi', 'Cari produk, tambah ke keranjang, lalu selesaikan pembayaran.', '+ Tambah Produk', false);
        renderTransaction();
    } else if(page === 'produk') {
        setPageHeader('Kelola Produk', 'Tambahkan, edit, dan hapus produk secara cepat.', '+ Tambah Produk', true, openAddProduct);
        renderProductsAdmin();
    } else if(page === 'pegawai') {
        setPageHeader('Pegawai', 'Lihat daftar pegawai dan outlet yang mereka kelola.', '+ Tambah Pegawai', true, openAddEmployee);
        renderEmployees();
    } else if(page === 'outlet') {
        setPageHeader('Outlet', 'Lihat daftar outlet, alamat, dan kontak.', '+ Tambah Outlet', true, openAddOutlet);
        renderOutlets();
    } else if(page === 'laporan') {
        setPageHeader('Laporan', 'Ringkasan penjualan dan laba saat ini.', '', false);
        renderReports();
    }
}

function renderTransaction(){
    document.getElementById('page-content').innerHTML = document.getElementById('tpl-transaksi').innerHTML;
    loadProducts().then(() => {
        const container = document.getElementById('products');
        const productCount = document.getElementById('product-count');
        productCount.innerText = `${PRODUCTS.length} produk tersedia`;
        const renderList = list => {
            container.innerHTML = list.map(p => `
                <article class="product-card">
                    <div class="top">
                        <div class="icon">${renderImageCircle(p.image, p.name) || categoryIcon(p.category)}</div>
                        <div class="meta">
                            <span class="tag">${p.category || 'Umum'}</span>
                        </div>
                    </div>
                    <div>
                        <h3>${p.name}</h3>
                        <div style="color:#64748b; margin-top:6px;">${formatRupiah(p.price)}</div>
                    </div>
                    <div class="stats">
                        <div class="stat"><span>Harga Modal</span><strong>${formatRupiah(p.modal || 0)}</strong></div>
                        <div class="stat"><span>Stok</span><strong>${p.stock ?? 0}</strong></div>
                        <div class="stat"><span>ID</span><strong>${p.id}</strong></div>
                    </div>
                    <button class="secondary-btn" style="width:100%;" onclick="addToCart(${p.id})">Tambah ke Keranjang</button>
                </article>
            `).join('');
        };
        renderList(PRODUCTS);
        document.getElementById('search').addEventListener('input', e => {
            const q = e.target.value.toLowerCase();
            const filtered = PRODUCTS.filter(p => p.name.toLowerCase().includes(q) || (p.category || '').toLowerCase().includes(q));
            renderList(filtered);
        });
    });
    updateCartUI();
}

function addToCart(id){
    const product = PRODUCTS.find(item => item.id === id);
    if(!product) return;
    const existing = CART.find(item => item.id === id);
    if(existing) existing.qty += 1;
    else CART.push({...product, qty:1});
    updateCartUI();
}

function changeCartItemQty(id, delta){
    const item = CART.find(item => item.id === id);
    if(!item) return;
    item.qty = Math.max(0, item.qty + delta);
    if(item.qty === 0) {
        CART = CART.filter(i => i.id !== id);
    }
    updateCartUI();
}

function removeCartItem(id){
    CART = CART.filter(item => item.id !== id);
    updateCartUI();
}

function updateCartUI(){
    const count = CART.reduce((sum, item) => sum + item.qty, 0);
    const total = CART.reduce((sum, item) => sum + item.qty * item.price, 0);
    const cartCount = document.getElementById('cart-count');
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    if(cartCount) cartCount.innerText = count;
    if(cartTotal) cartTotal.innerText = formatRupiah(total);
    if(cartItems) {
        cartItems.innerHTML = CART.length ? CART.map(item => `
            <div class="cart-item">
                <div class="cart-item-details">
                    <div style="font-weight:700;">${item.name}</div>
                    <div class="cart-item-controls">
                        <button type="button" onclick="changeCartItemQty(${item.id}, -1)">-</button>
                        <span class="cart-qty">${item.qty}</span>
                        <button type="button" onclick="changeCartItemQty(${item.id}, 1)">+</button>
                        <button type="button" class="btn-remove" onclick="removeCartItem(${item.id})">Hapus</button>
                    </div>
                </div>
                <span>${formatRupiah(item.qty * item.price)}</span>
            </div>
        `).join('') : '<div class="empty-state">Keranjang kosong. Tambahkan produk terlebih dahulu.</div>';
    }
    const payBtn = document.getElementById('pay-btn');
    if(payBtn) payBtn.onclick = processPayment;
}

function processPayment(){
    if(!CART.length) return alert('Silakan tambahkan produk ke keranjang terlebih dahulu.');
    const total = CART.reduce((sum, item) => sum + item.qty * item.price, 0);
    
    document.getElementById('payment-total-label').innerText = formatRupiah(total);
    const paidInput = document.getElementById('payment-paid-input');
    paidInput.value = total;
    paidInput.readOnly = false;
    
    const methodSelect = document.getElementById('payment-method-select');
    methodSelect.value = 'cash';
    document.getElementById('quick-cash-container').style.display = 'grid';
    
    document.getElementById('qr-payment-details').classList.add('hidden');
    document.getElementById('tf-payment-details').classList.add('hidden');
    
    generateQuickCash(total);
    updatePaymentChange();
    
    document.getElementById('payment-modal').classList.remove('hidden');
    setTimeout(() => paidInput.focus(), 100);
}

function renderProductsAdmin(){
    document.getElementById('page-content').innerHTML = document.getElementById('tpl-produk').innerHTML;
    fetch('/pos/api/products').then(r=>r.json()).then(data => {
        PRODUCTS = data;
        const container = document.getElementById('products-admin');
        container.innerHTML = data.length ? data.map(p => `
            <article class="product-card">
                <div class="top">
                    <div class="icon">${renderImageCircle(p.image, p.name) || categoryIcon(p.category)}</div>
                    <div class="meta" style="display:flex; flex-direction:column; gap:4px; align-items:flex-end;">
                        <span class="tag">${p.category || 'Umum'}</span>
                        <span class="tag" style="background:#f0fdf4; color:#166534;">${p.outlet?.name || 'Semua Outlet'}</span>
                    </div>
                </div>
                <div>
                    <h3>${p.name}</h3>
                    <p style="color:#64748b; margin:8px 0 0;">Harga Jual ${formatRupiah(p.price)}</p>
                </div>
                <div class="stats">
                    <div class="stat"><span>Harga Modal</span><strong>${formatRupiah(p.modal || 0)}</strong></div>
                    <div class="stat"><span>Stok</span><strong>${p.stock ?? 0}</strong></div>
                    <div class="stat"><span>ID</span><strong>${p.id}</strong></div>
                </div>
                <div class="actions">
                    <button type="button" class="btn-edit" onclick="openEditProduct(${p.id})">Edit</button>
                    <button type="button" class="btn-delete" onclick="deleteProduct(${p.id})">Hapus</button>
                </div>
            </article>
        `).join('') : '<div class="empty-state">Belum ada produk. Tambahkan produk baru untuk menampilkan daftar.</div>';
    });
}

function renderEmployees(){
    document.getElementById('page-content').innerHTML = document.getElementById('tpl-pegawai').innerHTML;
    Promise.all([fetch('/pos/api/employees').then(r => r.json()), loadOutlets()]).then(([data]) => {
        EMPLOYEES = data;
        const container = document.getElementById('employees-admin');
        container.innerHTML = data.length ? data.map(emp => `
            <article class="product-card user-card">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    <div>${renderImageCircle(emp.photo, emp.name, 48) || '<div style="width:48px;height:48px;border-radius:18px;background:#eef2ff;display:grid;place-items:center;color:#4338ca;">👤</div>'}</div>
                    <div>
                        <h3>${emp.name}</h3>
                        <div class="meta">${emp.role || 'Staff'} • ${emp.email || '-'} • ${emp.phone || '-'}</div>
                    </div>
                </div>
                <div class="meta">Outlet: ${emp.outlet?.name || emp.outlet_id || 'Tidak tersedia'}</div>
                <div class="actions">
                    <button type="button" class="btn-edit" onclick="openEditEmployee(${emp.id})">Edit</button>
                    <button type="button" class="btn-delete" onclick="deleteEmployee(${emp.id})">Hapus</button>
                </div>
            </article>
        `).join('') : '<div class="empty-state">Belum ada data pegawai.</div>';
    });
}

function renderOutlets(){
    document.getElementById('page-content').innerHTML = document.getElementById('tpl-outlet').innerHTML;
    fetch('/pos/api/outlets').then(r => r.json()).then(data => {
        OUTLETS = data;
        const container = document.getElementById('outlets-admin');
        container.innerHTML = data.length ? data.map(out => {
            const supervisors = (out.employees || []).filter(e => e.role?.toLowerCase() === 'supervisor').map(e => e.name);
            const kasirs = (out.employees || []).filter(e => e.role?.toLowerCase() === 'kasir').map(e => e.name);
            const supervisorText = supervisors.length ? supervisors.join(', ') : '-';
            const kasirText = kasirs.length ? kasirs.join(', ') : '-';
            
            return `
                <article class="product-card user-card">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                        <div>${renderImageCircle(out.image, out.name, 48) || '<div style="width:48px;height:48px;border-radius:18px;background:#f0f9ff;display:grid;place-items:center;color:#0284c7;">🏬</div>'}</div>
                        <div>
                            <h3>${out.name}</h3>
                            <div class="meta">${out.address || '-'}${out.kelurahan ? ', ' + out.kelurahan : ''}</div>
                        </div>
                    </div>
                    <div class="meta">Telp: ${out.phone || '-'}</div>
                    <div class="meta">Kode Pos: ${out.kode_pos || '-'}</div>
                    <div class="meta" style="margin-top: 8px; border-top: 1px dashed rgba(0,0,0,0.05); padding-top: 8px; font-size: 0.9rem;">
                        <strong>Supervisor:</strong> ${supervisorText}<br/>
                        <strong>Kasir:</strong> ${kasirText}
                    </div>
                    <div class="actions">
                        <button type="button" class="btn-edit" onclick="openEditOutlet(${out.id})">Edit</button>
                        <button type="button" class="btn-delete" onclick="deleteOutlet(${out.id})">Hapus</button>
                    </div>
                </article>
            `;
        }).join('') : '<div class="empty-state">Belum ada data outlet.</div>';
    });
}

function loadOutlets(){
    return fetch('/pos/api/outlets').then(r => r.json()).then(data => {
        OUTLETS = data;
        return data;
    });
}

function openAddEmployee(){
    editingEmployee = null;
    document.getElementById('employee-modal').querySelector('h3').innerText = 'Tambah Pegawai';
    document.getElementById('employee-name').value = '';
    document.getElementById('employee-role').value = 'Kasir';
    document.getElementById('employee-email').value = '';
    document.getElementById('employee-phone').value = '';
    document.getElementById('employee-photo').value = '';
    document.getElementById('employee-photo-preview').innerHTML = '';
    document.getElementById('employee-pin').value = '';
    document.getElementById('employee-pin').required = true;
    document.getElementById('employee-pin').placeholder = '4 angka PIN';
    const outletSelect = document.getElementById('employee-outlet');
    outletSelect.innerHTML = '<option value="">Pilih outlet</option>' + OUTLETS.map(out => `<option value="${out.id}">${out.name}</option>`).join('');
    document.getElementById('employee-modal').classList.remove('hidden');
}

function openEditEmployee(employeeId){
    const employee = EMPLOYEES.find(e => e.id === employeeId);
    if(!employee) return;
    editingEmployee = employee;
    document.getElementById('employee-modal').querySelector('h3').innerText = 'Edit Pegawai';
    document.getElementById('employee-name').value = employee.name || '';
    document.getElementById('employee-role').value = employee.role || 'Kasir';
    document.getElementById('employee-email').value = employee.email || '';
    document.getElementById('employee-phone').value = employee.phone || '';
    document.getElementById('employee-photo').value = '';
    document.getElementById('employee-photo-preview').innerHTML = employee.photo ? `<div style="display:flex;align-items:center;gap:10px;"><img src="${resolveImageUrl(employee.photo)}" style="width:56px;height:56px;border-radius:18px;object-fit:cover;" alt="Foto Pegawai" /><span style="color:#475569;">Foto saat ini ditampilkan. Unggah file baru untuk mengganti.</span></div>` : '';
    document.getElementById('employee-pin').value = '';
    document.getElementById('employee-pin').required = false;
    document.getElementById('employee-pin').placeholder = 'Biarkan kosong jika tidak diubah';
    const outletSelect = document.getElementById('employee-outlet');
    outletSelect.innerHTML = '<option value="">Pilih outlet</option>' + OUTLETS.map(out => `<option value="${out.id}" ${out.id === employee.outlet_id ? 'selected' : ''}>${out.name}</option>`).join('');
    document.getElementById('employee-modal').classList.remove('hidden');
}

function openAddOutlet(){
    editingOutlet = null;
    document.getElementById('outlet-modal').querySelector('h3').innerText = 'Tambah Outlet';
    document.getElementById('outlet-name').value = '';
    document.getElementById('outlet-phone').value = '';
    document.getElementById('outlet-address').value = '';
    document.getElementById('outlet-image').value = '';
    document.getElementById('outlet-image-preview').innerHTML = '';
    document.getElementById('outlet-kelurahan').value = '';
    document.getElementById('outlet-kodepos').value = '';
    document.getElementById('outlet-modal').classList.remove('hidden');
}

function openEditOutlet(outletId){
    const outlet = OUTLETS.find(o => o.id === outletId);
    if(!outlet) return;
    editingOutlet = outlet;
    document.getElementById('outlet-modal').querySelector('h3').innerText = 'Edit Outlet';
    document.getElementById('outlet-name').value = outlet.name || '';
    document.getElementById('outlet-phone').value = outlet.phone || '';
    document.getElementById('outlet-address').value = outlet.address || '';
    document.getElementById('outlet-image').value = '';
    document.getElementById('outlet-image-preview').innerHTML = outlet.image ? `<div style="display:flex;align-items:center;gap:10px;"><img src="${resolveImageUrl(outlet.image)}" style="width:56px;height:56px;border-radius:18px;object-fit:cover;" alt="Foto Outlet" /><span style="color:#475569;">Foto saat ini ditampilkan. Unggah file baru untuk mengganti.</span></div>` : '';
    document.getElementById('outlet-kelurahan').value = outlet.kelurahan || '';
    document.getElementById('outlet-kodepos').value = outlet.kode_pos || '';
    document.getElementById('outlet-modal').classList.remove('hidden');
}

function deleteEmployee(id){
    if(!confirm('Hapus pegawai ini?')) return;
    fetch(`/pos/api/employees/${id}`, {method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(res => {
        if(res.ok) renderEmployees();
        else alert('Gagal menghapus pegawai.');
    });
}

function deleteOutlet(id){
    if(!confirm('Hapus outlet ini?')) return;
    fetch(`/pos/api/outlets/${id}`, {method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(res => {
        if(res.ok) {
            renderOutlets();
            loadOutlets();
        } else alert('Gagal menghapus outlet.');
    });
}

let currentHistoryPage = 1;
const historyPerPage = 10;

function renderReports(){
    document.getElementById('page-content').innerHTML = `
        <div style="display:grid;gap:24px;">
            <div class="report-summary-grid">
                <div class="report-summary-card">
                    <div class="label">Total Pendapatan</div>
                    <div class="value" id="gross-sales">Rp 0</div>
                    <div style="font-size:0.9rem; margin-top:8px; display:flex; flex-direction:column; gap:4px; border-top:1px solid rgba(0,0,0,0.05); padding-top:8px;">
                        <div style="display:flex; justify-content:space-between;"><span>Offline (Cash):</span><strong id="gross-sales-offline" style="color: #64748b;">Rp 0</strong></div>
                        <div style="display:flex; justify-content:space-between;"><span>Online (QR & TF):</span><strong id="gross-sales-online" style="color: #64748b;">Rp 0</strong></div>
                    </div>
                </div>
                <div class="report-summary-card">
                    <div class="label">Total Transaksi</div>
                    <div class="value" id="transaction-count">0 transaksi</div>
                    <div class="subtitle">Jumlah transaksi yang tercatat</div>
                </div>
                <div class="report-summary-card">
                    <div class="label">Total Item Terjual</div>
                    <div class="value" id="items-sold">0 item</div>
                    <div class="subtitle">Semua jumlah kuantitas produk</div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1.4fr 1fr;gap:18px;">
                <div class="report-card">
                    <h3>Produk Terlaris</h3>
                    <div id="top-products"></div>
                </div>
                <div class="report-card">
                    <h3>Riwayat Transaksi</h3>
                    <div id="transaction-history"></div>
                    <div id="history-pagination" style="display:flex; justify-content:space-between; align-items:center; margin-top:12px; font-size:0.9rem;"></div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
                <div class="report-card">
                    <div class="section-title">Keuntungan</div>
                    <div class="report-row"><div class="report-label">Total Penjualan</div><div class="report-value" id="profit-sales">Rp 0</div></div>
                    <div class="report-row"><div class="report-label">Harga Modal</div><div class="report-value" id="cost-price">Rp 0</div></div>
                    <div class="report-row"><div class="report-label">Total Keuntungan</div><div class="report-value" id="profit-value">Rp 0</div></div>
                    <div class="note-text">*Biaya operasional belum termasuk dalam perhitungan ini.</div>
                </div>
                <div class="info-card" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;width:100%;">
                    <div style="display:flex; gap:16px; flex-wrap:wrap;">
                        <div>
                            <div class="label">Dari Tanggal</div>
                            <input type="date" id="report-start-date" style="border:1px solid rgba(15,23,42,.12);border-radius:12px;padding:8px 12px;font-size:0.95rem;outline:none;background:#f8fafc;margin-top:4px;" />
                        </div>
                        <div>
                            <div class="label">Sampai Tanggal</div>
                            <input type="date" id="report-end-date" style="border:1px solid rgba(15,23,42,.12);border-radius:12px;padding:8px 12px;font-size:0.95rem;outline:none;background:#f8fafc;margin-top:4px;" />
                        </div>
                    </div>
                    <div>
                        <div class="label">Filter Tipe</div>
                        <select id="report-payment-filter" style="border:1px solid rgba(15,23,42,.12);border-radius:12px;padding:8px 12px;font-size:0.95rem;outline:none;background:#f8fafc;margin-top:4px;">
                            <option value="all">Semua (Offline & Online)</option>
                            <option value="offline">Offline (Cash)</option>
                            <option value="online">Online (QR & TF)</option>
                        </select>
                    </div>
                    <button type="button" class="export-btn" onclick="exportReportToPdf()">Export ke PDF</button>
                </div>
            </div>
        </div>
    `;

    Promise.all([loadProducts(), fetch('/pos/api/transactions').then(r => r.json())]).then(([_, transactions]) => {
        window.ALL_TRANSACTIONS = transactions;
        window.CURRENT_TRANSACTIONS = transactions; // for viewing invoices
        currentHistoryPage = 1;
        
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const formatYYYYMMDD = d => d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        
        document.getElementById('report-start-date').value = formatYYYYMMDD(firstDay);
        document.getElementById('report-end-date').value = formatYYYYMMDD(today);
        
        document.getElementById('report-payment-filter').addEventListener('change', updateReportData);
        document.getElementById('report-start-date').addEventListener('change', updateReportData);
        document.getElementById('report-end-date').addEventListener('change', updateReportData);
        
        updateReportData();
    });
}

function updateReportData() {
    const allTransactions = window.ALL_TRANSACTIONS || [];
    const filterVal = document.getElementById('report-payment-filter').value;
    const startVal = document.getElementById('report-start-date').value;
    const endVal = document.getElementById('report-end-date').value;
    
    let filteredTransactions = allTransactions;
    
    // Filter by payment method
    if (filterVal === 'offline') {
        filteredTransactions = allTransactions.filter(tx => !tx.payment_method || tx.payment_method === 'cash');
    } else if (filterVal === 'online') {
        filteredTransactions = allTransactions.filter(tx => tx.payment_method === 'qr' || tx.payment_method === 'tf');
    }
    
    // Filter by start date
    if (startVal) {
        const startDate = new Date(startVal);
        startDate.setHours(0, 0, 0, 0);
        filteredTransactions = filteredTransactions.filter(tx => {
            const txDate = new Date(tx.created_at);
            return txDate >= startDate;
        });
    }
    
    // Filter by end date
    if (endVal) {
        const endDate = new Date(endVal);
        endDate.setHours(23, 59, 59, 999);
        filteredTransactions = filteredTransactions.filter(tx => {
            const txDate = new Date(tx.created_at);
            return txDate <= endDate;
        });
    }
    
    const itemCount = filteredTransactions.reduce((sum, tx) => sum + (tx.items || []).reduce((count, item) => count + (item.qty || 0), 0), 0);
    const grossSales = filteredTransactions.reduce((sum, tx) => sum + (tx.total || 0), 0);
    const productSales = filteredTransactions.reduce((map, tx) => {
        (tx.items || []).forEach(item => {
            const id = item.product_id || item.id;
            map[id] = map[id] || { name: item.name, qty: 0, revenue: 0 };
            map[id].qty += item.qty || 0;
            map[id].revenue += (item.qty || 0) * (item.price || 0);
        });
        return map;
    }, {});
    const sortedProducts = Object.values(productSales).sort((a, b) => b.qty - a.qty).slice(0, 5);
    const maxQty = sortedProducts[0]?.qty || 1;
    const transactionCount = filteredTransactions.length;
    const costPrice = filteredTransactions.reduce((sum, tx) => {
        return sum + (tx.items || []).reduce((itemSum, item) => {
            const product = PRODUCTS.find(p => p.id === item.product_id);
            return itemSum + (item.qty || 0) * (product?.modal || 0);
        }, 0);
    }, 0);
    const profit = grossSales - costPrice;

    document.getElementById('gross-sales').innerText = formatRupiah(grossSales);
    document.getElementById('transaction-count').innerText = `${transactionCount} transaksi`;
    document.getElementById('items-sold').innerText = `${itemCount} item`;
    document.getElementById('profit-sales').innerText = formatRupiah(grossSales);
    document.getElementById('cost-price').innerText = formatRupiah(costPrice);
    document.getElementById('profit-value').innerText = formatRupiah(profit);

    const topProductsHtml = sortedProducts.length ? sortedProducts.map(prod => `
        <div class="report-product">
            <div style="flex:1;">
                <div style="font-weight:700;color:#0f172a;">${prod.name}</div>
                <div class="product-bar"><div class="product-bar-fill" style="width:${Math.round((prod.qty / maxQty) * 100)}%"></div></div>
            </div>
            <div style="text-align:right;min-width:90px;">
                <div style="font-weight:700;color:#0f172a;">${prod.qty} item</div>
                <div style="color:#64748b;font-size:.95rem;">${formatRupiah(prod.revenue)}</div>
            </div>
        </div>
    `).join('') : '<div class="empty-state">Belum ada data produk terjual.</div>';
    document.getElementById('top-products').innerHTML = topProductsHtml;

    // Offline / Online Breakdown (Always calculated on all transactions)
    const grossOffline = allTransactions.filter(tx => !tx.payment_method || tx.payment_method === 'cash').reduce((sum, tx) => sum + (tx.total || 0), 0);
    const grossOnline = allTransactions.filter(tx => tx.payment_method === 'qr' || tx.payment_method === 'tf').reduce((sum, tx) => sum + (tx.total || 0), 0);
    document.getElementById('gross-sales-offline').innerText = formatRupiah(grossOffline);
    document.getElementById('gross-sales-online').innerText = formatRupiah(grossOnline);

    renderHistoryPagination(filteredTransactions);
}

function renderHistoryPagination(filteredTransactions) {
    const totalItems = filteredTransactions.length;
    const totalPages = Math.ceil(totalItems / historyPerPage) || 1;
    
    if (currentHistoryPage > totalPages) {
        currentHistoryPage = totalPages;
    }
    if (currentHistoryPage < 1) {
        currentHistoryPage = 1;
    }
    
    const start = (currentHistoryPage - 1) * historyPerPage;
    const end = start + historyPerPage;
    const paginatedTransactions = filteredTransactions.slice(start, end);

    const historyHtml = paginatedTransactions.length ? paginatedTransactions.map(tx => {
        const methodLabel = tx.payment_method ? tx.payment_method.toUpperCase() : 'CASH';
        return `
            <div class="transaction-item">
                <div class="transaction-info">
                    <div class="transaction-id">${tx.trx_id || 'TRX-'+tx.id}</div>
                    <div class="transaction-meta">${tx.created_at ? new Date(tx.created_at).toLocaleDateString('id-ID') : '-'} • ${((tx.items || []).reduce((sum, item) => sum + (item.qty || 0), 0))} item • via <strong>${methodLabel}</strong></div>
                </div>
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="font-weight:700;color:#16a34a;">${formatRupiah(tx.total || 0)}</div>
                    <button class="secondary-btn" style="padding: 6px 12px; font-size: 0.85rem;" onclick="viewTransactionInvoice(${tx.id})">🧾 Struk</button>
                </div>
            </div>
        `;
    }).join('') : '<div class="empty-state">Belum ada riwayat transaksi.</div>';
    document.getElementById('transaction-history').innerHTML = historyHtml;

    const paginationContainer = document.getElementById('history-pagination');
    if (totalItems > 0) {
        paginationContainer.innerHTML = `
            <button class="secondary-btn" style="padding: 6px 12px;" ${currentHistoryPage === 1 ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : 'onclick="changeHistoryPage(-1)"'}>Sebelumnya</button>
            <span>Halaman <strong>${currentHistoryPage}</strong> dari <strong>${totalPages}</strong></span>
            <button class="secondary-btn" style="padding: 6px 12px;" ${currentHistoryPage === totalPages ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : 'onclick="changeHistoryPage(1)"'}>Selanjutnya</button>
        `;
    } else {
        paginationContainer.innerHTML = '';
    }
}

function changeHistoryPage(delta) {
    currentHistoryPage += delta;
    const allTransactions = window.ALL_TRANSACTIONS || [];
    const filterVal = document.getElementById('report-payment-filter').value;
    const startVal = document.getElementById('report-start-date').value;
    const endVal = document.getElementById('report-end-date').value;
    
    let filteredTransactions = allTransactions;
    if (filterVal === 'offline') {
        filteredTransactions = allTransactions.filter(tx => !tx.payment_method || tx.payment_method === 'cash');
    } else if (filterVal === 'online') {
        filteredTransactions = allTransactions.filter(tx => tx.payment_method === 'qr' || tx.payment_method === 'tf');
    }
    
    if (startVal) {
        const startDate = new Date(startVal);
        startDate.setHours(0, 0, 0, 0);
        filteredTransactions = filteredTransactions.filter(tx => new Date(tx.created_at) >= startDate);
    }
    if (endVal) {
        const endDate = new Date(endVal);
        endDate.setHours(23, 59, 59, 999);
        filteredTransactions = filteredTransactions.filter(tx => new Date(tx.created_at) <= endDate);
    }
    
    renderHistoryPagination(filteredTransactions);
}

function exportReportToPdf() {
    const startVal = document.getElementById('report-start-date').value;
    const endVal = document.getElementById('report-end-date').value;
    const filterVal = document.getElementById('report-payment-filter').value;
    
    const allTransactions = window.ALL_TRANSACTIONS || [];
    
    let filteredTransactions = allTransactions;
    if (filterVal === 'offline') {
        filteredTransactions = allTransactions.filter(tx => !tx.payment_method || tx.payment_method === 'cash');
    } else if (filterVal === 'online') {
        filteredTransactions = allTransactions.filter(tx => tx.payment_method === 'qr' || tx.payment_method === 'tf');
    }
    
    if (startVal) {
        const startDate = new Date(startVal);
        startDate.setHours(0, 0, 0, 0);
        filteredTransactions = filteredTransactions.filter(tx => new Date(tx.created_at) >= startDate);
    }
    if (endVal) {
        const endDate = new Date(endVal);
        endDate.setHours(23, 59, 59, 999);
        filteredTransactions = filteredTransactions.filter(tx => new Date(tx.created_at) <= endDate);
    }
    
    const grossSales = filteredTransactions.reduce((sum, tx) => sum + (tx.total || 0), 0);
    const transactionCount = filteredTransactions.length;
    const itemCount = filteredTransactions.reduce((sum, tx) => sum + (tx.items || []).reduce((count, item) => count + (item.qty || 0), 0), 0);
    
    const costPrice = filteredTransactions.reduce((sum, tx) => {
        return sum + (tx.items || []).reduce((itemSum, item) => {
            const product = PRODUCTS.find(p => p.id === item.product_id);
            return itemSum + (item.qty || 0) * (product?.modal || 0);
        }, 0);
    }, 0);
    const profit = grossSales - costPrice;
    
    const printDiv = document.createElement('div');
    printDiv.id = 'report-print-area';
    printDiv.style.position = 'absolute';
    printDiv.style.left = '0';
    printDiv.style.top = '0';
    printDiv.style.width = '100%';
    printDiv.style.padding = '30px';
    printDiv.style.background = '#fff';
    printDiv.style.color = '#000';
    printDiv.style.fontFamily = 'Arial, sans-serif';
    
    let filterText = 'Semua (Offline & Online)';
    if (filterVal === 'offline') filterText = 'Offline (Cash)';
    if (filterVal === 'online') filterText = 'Online (QR & TF)';
    
    printDiv.innerHTML = `
        <div style="text-align: center; margin-bottom: 25px;">
            <h2 style="margin: 0; font-size: 1.8rem;">LAPORAN TRANSAKSI PENJUALAN</h2>
            <h3 style="margin: 5px 0; color: #475569;">Lapak Yunita POS</h3>
            <p style="margin: 5px 0; font-size: 0.95rem;">Periode: ${startVal || '-'} s/d ${endVal || '-'}</p>
            <p style="margin: 5px 0; font-size: 0.95rem;">Filter Pembayaran: ${filterText}</p>
            <hr style="border: 1px dashed #cbd5e1; margin-top: 15px;"/>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 1rem;">
            <tr>
                <td style="padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Total Pendapatan</td>
                <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold; color: #16a34a;">${formatRupiah(grossSales)}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Total Harga Modal</td>
                <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right;">${formatRupiah(costPrice)}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #cbd5e1; font-weight: bold; background: #f8fafc;">Total Keuntungan</td>
                <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right; font-weight: bold; color: #2563eb;">${formatRupiah(profit)}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #cbd5e1; background: #f8fafc;">Total Transaksi</td>
                <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right;">${transactionCount} Transaksi</td>
            </tr>
            <tr>
                <td style="padding: 8px; border: 1px solid #cbd5e1; background: #f8fafc;">Total Item Terjual</td>
                <td style="padding: 8px; border: 1px solid #cbd5e1; text-align: right;">${itemCount} Item</td>
            </tr>
        </table>
        
        <h4 style="margin: 20px 0 10px;">Daftar Transaksi</h4>
        <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: left;">ID Transaksi</th>
                    <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: left;">Tanggal</th>
                    <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: left;">Kasir</th>
                    <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: left;">Outlet</th>
                    <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: center;">Metode</th>
                    <th style="border: 1px solid #cbd5e1; padding: 8px; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                ${filteredTransactions.map(tx => `
                    <tr>
                        <td style="border: 1px solid #cbd5e1; padding: 8px;">${tx.trx_id || 'TRX-'+tx.id}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 8px;">${tx.created_at ? new Date(tx.created_at).toLocaleString('id-ID') : '-'}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 8px;">${tx.cashier || '-'}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 8px;">${tx.outlet || '-'}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 8px; text-align: center;">${(tx.payment_method || 'CASH').toUpperCase()}</td>
                        <td style="border: 1px solid #cbd5e1; padding: 8px; text-align: right; font-weight: bold;">${formatRupiah(tx.total || 0)}</td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
        
        <div style="margin-top: 40px; text-align: right; font-size: 0.85rem; color: #64748b;">
            Laporan dicetak pada tanggal: ${new Date().toLocaleString('id-ID')}
        </div>
    `;
    
    document.body.appendChild(printDiv);
    
    const style = document.createElement('style');
    style.id = 'report-print-style';
    style.innerHTML = `
        @media print {
            body * { visibility: hidden !important; }
            #report-print-area, #report-print-area * { visibility: visible !important; }
            #report-print-area { position: absolute !important; left: 0 !important; top: 0 !important; width: 100% !important; display: block !important; }
        }
    `;
    document.head.appendChild(style);
    
    window.print();
    
    setTimeout(() => {
        document.body.removeChild(printDiv);
        document.head.removeChild(style);
    }, 500);
}

function closeModal(event){
    if(event) event.stopPropagation();
    document.getElementById('product-modal').classList.add('hidden');
    document.getElementById('employee-modal').classList.add('hidden');
    document.getElementById('outlet-modal').classList.add('hidden');
    document.getElementById('invoice-modal').classList.add('hidden');
    document.getElementById('payment-modal').classList.add('hidden');
    document.getElementById('change-pin-modal').classList.add('hidden');
}

function bindEmployeeForm(){
    const form = document.getElementById('employee-form');
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData();
        formData.append('name', document.getElementById('employee-name').value.trim());
        formData.append('role', document.getElementById('employee-role').value);
        formData.append('email', document.getElementById('employee-email').value.trim());
        formData.append('phone', document.getElementById('employee-phone').value.trim());
        const outletValue = document.getElementById('employee-outlet').value;
        if (outletValue) formData.append('outlet_id', outletValue);
        const photoFile = document.getElementById('employee-photo').files[0];
        if (photoFile) formData.append('photo', photoFile);
        
        const pinValue = document.getElementById('employee-pin').value;
        if (pinValue) {
            formData.append('pin', pinValue);
        }

        const url = editingEmployee ? `/pos/api/employees/${editingEmployee.id}` : '/pos/api/employees';
        if (editingEmployee) formData.append('_method', 'PUT');
        const res = await fetch(url, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: formData
        });
        if(res.ok){
            closeModal();
            editingEmployee = null;
            renderEmployees();
        } else {
            alert('Gagal menyimpan pegawai. Periksa input Anda.');
        }
    });
}

function bindOutletForm(){
    const form = document.getElementById('outlet-form');
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData();
        formData.append('name', document.getElementById('outlet-name').value.trim());
        formData.append('phone', document.getElementById('outlet-phone').value.trim());
        formData.append('address', document.getElementById('outlet-address').value.trim());
        formData.append('kelurahan', document.getElementById('outlet-kelurahan').value.trim());
        formData.append('kode_pos', document.getElementById('outlet-kodepos').value.trim());
        const imageFile = document.getElementById('outlet-image').files[0];
        if (imageFile) formData.append('image', imageFile);
        const url = editingOutlet ? `/pos/api/outlets/${editingOutlet.id}` : '/pos/api/outlets';
        if (editingOutlet) formData.append('_method', 'PUT');
        const res = await fetch(url, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: formData
        });
        if(res.ok){
            closeModal();
            editingOutlet = null;
            renderOutlets();
            loadOutlets();
        } else {
            alert('Gagal menyimpan outlet. Periksa input Anda.');
        }
    });
}

function openAddProduct(){
    editingProduct = null;
    document.getElementById('modal-title').innerText = 'Tambah Produk';
    document.getElementById('field-name').value = '';
    document.getElementById('field-category').value = '';
    document.getElementById('field-price').value = '';
    document.getElementById('field-modal').value = '';
    document.getElementById('field-current-stock').value = 0;
    document.getElementById('field-add-stock').value = 0;
    document.getElementById('field-image').value = '';
    
    const outletSelect = document.getElementById('field-outlet');
    outletSelect.innerHTML = '<option value="">Semua Outlet</option>' + OUTLETS.map(out => `<option value="${out.id}">${out.name}</option>`).join('');
    outletSelect.value = '';

    document.getElementById('product-image-preview').innerHTML = '';
    document.getElementById('product-modal').classList.remove('hidden');
}

function openEditProduct(productId){
    const product = PRODUCTS.find(p => p.id === productId);
    if(!product) return;
    editingProduct = product;
    document.getElementById('modal-title').innerText = 'Edit Produk';
    document.getElementById('field-name').value = product.name || '';
    document.getElementById('field-category').value = product.category || '';
    document.getElementById('field-price').value = product.price || '';
    document.getElementById('field-modal').value = product.modal || '';
    document.getElementById('field-current-stock').value = product.stock || 0;
    document.getElementById('field-add-stock').value = 0;
    document.getElementById('field-image').value = '';
    
    const outletSelect = document.getElementById('field-outlet');
    outletSelect.innerHTML = '<option value="">Semua Outlet</option>' + OUTLETS.map(out => `<option value="${out.id}" ${out.id === product.outlet_id ? 'selected' : ''}>${out.name}</option>`).join('');
    outletSelect.value = product.outlet_id || '';

    document.getElementById('product-image-preview').innerHTML = product.image ? `<div style="display:flex;align-items:center;gap:10px;"><img src="${resolveImageUrl(product.image)}" style="width:56px;height:56px;border-radius:18px;object-fit:cover;" alt="Gambar Produk" /><span style="color:#475569;">Gambar saat ini ditampilkan. Unggah file baru untuk mengganti.</span></div>` : '';
    document.getElementById('product-modal').classList.remove('hidden');
}

function bindProductForm(){
    const form = document.getElementById('product-form');
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData();
        formData.append('name', document.getElementById('field-name').value.trim());
        formData.append('category', document.getElementById('field-category').value.trim());
        formData.append('price', parseInt(document.getElementById('field-price').value, 10) || 0);
        formData.append('modal', parseInt(document.getElementById('field-modal').value, 10) || 0);
        
        const currentStock = parseInt(document.getElementById('field-current-stock').value, 10) || 0;
        const addStock = parseInt(document.getElementById('field-add-stock').value, 10) || 0;
        formData.append('stock', currentStock + addStock);
        
        const outletVal = document.getElementById('field-outlet').value;
        if (outletVal) {
            formData.append('outlet_id', outletVal);
        }
        
        const imageFile = document.getElementById('field-image').files[0];
        if (imageFile) formData.append('image', imageFile);
        const url = editingProduct ? `/pos/api/products/${editingProduct.id}` : '/pos/api/products';
        if (editingProduct) formData.append('_method', 'PUT');
        const res = await fetch(url, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: formData
        });
        if(res.ok) {
            closeModal();
            renderProductsAdmin();
            loadProducts();
        } else {
            alert('Gagal menyimpan produk. Periksa kembali data Anda.');
        }
    });
}

function showInvoice(tx){
    if(!tx) return;
    document.getElementById('invoice-trx-id').innerText = tx.trx_id || ('TRX-' + tx.id);
    document.getElementById('invoice-date').innerText = tx.created_at ? new Date(tx.created_at).toLocaleString('id-ID') : new Date().toLocaleString('id-ID');
    document.getElementById('invoice-cashier').innerText = tx.cashier || 'Kasir';
    document.getElementById('invoice-outlet-name').innerText = tx.outlet || 'Lapak Yunita';
    
    // Render items
    const itemsContainer = document.getElementById('invoice-items');
    if(tx.items && tx.items.length) {
        itemsContainer.innerHTML = tx.items.map(item => `
            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                <div>${item.name} x${item.qty}</div>
                <div>${formatRupiah(item.qty * item.price)}</div>
            </div>
        `).join('');
    } else {
        itemsContainer.innerHTML = '<div style="text-align:center;color:#64748b;">Tidak ada produk</div>';
    }
    
    document.getElementById('invoice-total').innerText = formatRupiah(tx.total || 0);
    document.getElementById('invoice-paid').innerText = formatRupiah(tx.paid || 0);
    document.getElementById('invoice-change').innerText = formatRupiah(tx.change || 0);
    
    document.getElementById('invoice-modal').classList.remove('hidden');
}

function printInvoice(){
    window.print();
}

function viewTransactionInvoice(id) {
    if(!window.CURRENT_TRANSACTIONS) return;
    const tx = window.CURRENT_TRANSACTIONS.find(t => t.id === id);
    if(tx) {
        showInvoice(tx);
    }
}

async function deleteProduct(id){
    if(!confirm('Hapus produk ini?')) return;
    const res = await fetch(`/pos/api/products/${id}`, {method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}});
    if(res.ok) {
        renderProductsAdmin();
        loadProducts();
    }
}

function generateQuickCash(total) {
    const container = document.getElementById('quick-cash-container');
    const nominals = [total];
    
    // Add next high denominations
    const standards = [10000, 20000, 50000, 100000, 200000];
    standards.forEach(std => {
        if (std > total && !nominals.includes(std)) {
            nominals.push(std);
        }
    });
    
    // Sort and take first unique nominals
    const uniqueNominals = [...new Set(nominals)].sort((a,b) => a-b).slice(0, 6);
    
    container.innerHTML = uniqueNominals.map(val => `
        <button type="button" class="secondary-btn" style="padding: 10px 4px; font-size: 0.85rem; font-weight: bold; text-align: center;" onclick="setPaymentPaid(${val})">
            ${val === total ? 'Uang Pas' : formatRupiah(val)}
        </button>
    `).join('');
}

function setPaymentPaid(val) {
    const paidInput = document.getElementById('payment-paid-input');
    paidInput.value = val;
    updatePaymentChange();
}

function updatePaymentChange() {
    const total = CART.reduce((sum, item) => sum + item.qty * item.price, 0);
    const paid = parseInt(document.getElementById('payment-paid-input').value, 10) || 0;
    const change = Math.max(0, paid - total);
    document.getElementById('payment-change-label').innerText = formatRupiah(change);
    
    const confirmBtn = document.getElementById('confirm-payment-btn');
    if (paid < total) {
        confirmBtn.disabled = true;
        confirmBtn.style.opacity = '0.5';
        confirmBtn.style.cursor = 'not-allowed';
    } else {
        confirmBtn.disabled = false;
        confirmBtn.style.opacity = '1';
        confirmBtn.style.cursor = 'pointer';
    }
}

function bindPaymentForm(){
    const form = document.getElementById('payment-form');
    const paidInput = document.getElementById('payment-paid-input');
    const methodSelect = document.getElementById('payment-method-select');
    
    paidInput.addEventListener('input', updatePaymentChange);
    
    methodSelect.addEventListener('change', () => {
        const method = methodSelect.value;
        const total = CART.reduce((sum, item) => sum + item.qty * item.price, 0);
        const quickCash = document.getElementById('quick-cash-container');
        const qrDetails = document.getElementById('qr-payment-details');
        const tfDetails = document.getElementById('tf-payment-details');
        
        if (method === 'qr') {
            paidInput.value = total;
            paidInput.readOnly = true;
            quickCash.style.display = 'none';
            qrDetails.classList.remove('hidden');
            tfDetails.classList.add('hidden');
        } else if (method === 'tf') {
            paidInput.value = total;
            paidInput.readOnly = true;
            quickCash.style.display = 'none';
            qrDetails.classList.add('hidden');
            tfDetails.classList.remove('hidden');
        } else {
            paidInput.value = total;
            paidInput.readOnly = false;
            quickCash.style.display = 'grid';
            qrDetails.classList.add('hidden');
            tfDetails.classList.add('hidden');
        }
        updatePaymentChange();
    });
    
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const total = CART.reduce((sum, item) => sum + item.qty * item.price, 0);
        const paid = parseInt(paidInput.value, 10);
        if(isNaN(paid) || paid < total) return;
        
        closeModal();
        
        const response = await fetch('/pos/api/transactions', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body:JSON.stringify({
                items:CART,
                total,
                paid,
                payment_method: methodSelect.value,
                cashier: CURRENT_EMPLOYEE ? CURRENT_EMPLOYEE.name : 'Kasir',
                outlet: (CURRENT_EMPLOYEE && CURRENT_EMPLOYEE.outlet) ? CURRENT_EMPLOYEE.outlet.name : 'Outlet Pusat'
            })
        });
        if(response.ok){
            const txData = await response.json();
            alert('Transaksi berhasil!');
            CART = [];
            renderTransaction();
            showInvoice(txData);
        } else {
            alert('Gagal menyimpan transaksi. Coba lagi.');
        }
    });
}

function pressPin(val) {
    const errorDiv = document.getElementById('login-error');
    errorDiv.classList.add('hidden');
    if (val === 'clear') {
        pinBuffer = '';
    } else if (val === 'delete') {
        pinBuffer = pinBuffer.slice(0, -1);
    } else {
        if (pinBuffer.length < 4) {
            pinBuffer += val;
        }
    }
    updatePinDots();
}

function updatePinDots() {
    const dots = document.querySelectorAll('.pin-dot');
    dots.forEach((dot, index) => {
        if (index < pinBuffer.length) {
            dot.style.background = '#10b981';
            dot.style.borderColor = '#10b981';
            dot.style.transform = 'scale(1.2)';
        } else {
            dot.style.background = '#e2e8f0';
            dot.style.borderColor = '#cbd5e1';
            dot.style.transform = 'scale(1)';
        }
    });
}

function openChangePinModal(event) {
    if (event) event.preventDefault();
    const loginSelect = document.getElementById('login-employee-select');
    const changeSelect = document.getElementById('change-pin-employee-select');
    
    // Copy options and value
    changeSelect.innerHTML = loginSelect.innerHTML;
    changeSelect.value = loginSelect.value;
    
    document.getElementById('change-pin-old').value = '';
    document.getElementById('change-pin-new').value = '';
    document.getElementById('change-pin-error').classList.add('hidden');
    document.getElementById('change-pin-modal').classList.remove('hidden');
}

function bindChangePinForm() {
    const form = document.getElementById('change-pin-form');
    form.addEventListener('submit', async e => {
        e.preventDefault();
        const employeeId = document.getElementById('change-pin-employee-select').value;
        const oldPin = document.getElementById('change-pin-old').value;
        const newPin = document.getElementById('change-pin-new').value;
        
        if (!employeeId) return alert('Silakan pilih nama pegawai.');
        if (oldPin.length !== 4 || newPin.length !== 4) return alert('PIN harus terdiri dari 4 digit angka.');
        
        const res = await fetch('/pos/api/change-pin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ employee_id: employeeId, old_pin: oldPin, new_pin: newPin })
        });
        
        if (res.ok) {
            alert('PIN berhasil diperbarui!');
            closeModal();
            initLoginScreen();
        } else {
            const err = await res.json();
            const errDiv = document.getElementById('change-pin-error');
            errDiv.innerText = err.message || 'Gagal mengganti PIN.';
            errDiv.classList.remove('hidden');
        }
    });
}

async function handleLogin(e) {
    if (e) e.preventDefault();
    const employeeId = document.getElementById('login-employee-select').value;
    if (!employeeId) return alert('Silakan pilih pegawai terlebih dahulu.');
    if (pinBuffer.length !== 4) return alert('Masukkan 4 digit PIN Anda.');
    
    const res = await fetch('/pos/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ employee_id: employeeId, pin: pinBuffer })
    });
    
    if (res.ok) {
        const data = await res.json();
        CURRENT_EMPLOYEE = data.employee;
        pinBuffer = '';
        updatePinDots();
        document.getElementById('login-screen').classList.add('hidden');
        document.getElementById('pos-app-shell').classList.remove('hidden');
        applyEmployeeRBAC();
        showPage('transaksi');
    } else {
        const err = await res.json();
        const errorDiv = document.getElementById('login-error');
        errorDiv.innerText = err.message || 'PIN yang dimasukkan salah.';
        errorDiv.classList.remove('hidden');
        pinBuffer = '';
        updatePinDots();
    }
}

async function logoutEmployee() {
    if (!confirm('Apakah Anda yakin ingin keluar?')) return;
    const res = await fetch('/pos/api/logout', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });
    if (res.ok) {
        CURRENT_EMPLOYEE = null;
        document.getElementById('pos-app-shell').classList.add('hidden');
        document.getElementById('login-screen').classList.remove('hidden');
        initLoginScreen();
    }
}

async function checkSession() {
    const res = await fetch('/pos/api/me');
    if (res.ok) {
        CURRENT_EMPLOYEE = await res.json();
        document.getElementById('login-screen').classList.add('hidden');
        document.getElementById('pos-app-shell').classList.remove('hidden');
        applyEmployeeRBAC();
        showPage('transaksi');
    } else {
        document.getElementById('pos-app-shell').classList.add('hidden');
        document.getElementById('login-screen').classList.remove('hidden');
        initLoginScreen();
    }
}

async function initLoginScreen() {
    const res = await fetch('/pos/api/employees');
    const employees = await res.json();
    const select = document.getElementById('login-employee-select');
    select.innerHTML = '<option value="">-- Pilih Nama --</option>' + 
        employees.map(emp => {
            const outletName = emp.outlet ? emp.outlet.name : 'Tanpa Outlet';
            return `<option value="${emp.id}">${emp.name} (${emp.role} - ${outletName})</option>`;
        }).join('');
    pinBuffer = '';
    updatePinDots();
    document.getElementById('login-error').classList.add('hidden');
}

function applyEmployeeRBAC() {
    if (!CURRENT_EMPLOYEE) return;
    
    const nameEl = document.getElementById('current-user-name');
    const roleEl = document.getElementById('current-user-role');
    const avatarEl = document.getElementById('current-user-avatar');
    
    nameEl.innerText = CURRENT_EMPLOYEE.name;
    roleEl.innerText = CURRENT_EMPLOYEE.role;
    
    const initials = CURRENT_EMPLOYEE.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    avatarEl.innerText = initials;
    
    const access = (CURRENT_EMPLOYEE.access || CURRENT_EMPLOYEE.role || '').toLowerCase();
    
    const menuProduk = document.getElementById('menu-produk');
    const menuPegawai = document.getElementById('menu-pegawai');
    const menuOutlet = document.getElementById('menu-outlet');
    const menuLaporan = document.getElementById('menu-laporan');
    
    if (access === 'admin') {
        menuProduk.classList.remove('hidden');
        menuPegawai.classList.remove('hidden');
        menuOutlet.classList.remove('hidden');
        menuLaporan.classList.remove('hidden');
    } else if (access === 'supervisor') {
        menuProduk.classList.add('hidden');
        menuPegawai.classList.add('hidden');
        menuOutlet.classList.add('hidden');
        menuLaporan.classList.remove('hidden');
    } else {
        menuProduk.classList.add('hidden');
        menuPegawai.classList.add('hidden');
        menuOutlet.classList.add('hidden');
        menuLaporan.classList.add('hidden');
    }
}

window.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.menu-item').forEach(btn => btn.addEventListener('click', () => showPage(btn.dataset.page)));
    bindProductForm();
    bindEmployeeForm();
    bindOutletForm();
    bindPaymentForm();
    bindChangePinForm();
    loadOutlets();
    
    // Bind login form
    document.getElementById('login-form').addEventListener('submit', handleLogin);
    
    // Check active session
    checkSession();
});
</script>
@endsection
