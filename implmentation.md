# Implementation Plan: Fitur Tambahan POS Lapaknita

## 1. Supervisor Mendapat Menu Pelanggan (Customer)

### Masalah
Saat ini supervisor hanya bisa melihat menu **Transaksi**, **Draft**, dan **Laporan**. Menu **Pelanggan** hanya tersedia untuk Admin.

### Perubahan

#### [MODIFY] [pos.blade.php](file:///c:/Users/nasch/Downloads/Lapaknita/resources/views/pos.blade.php)
- Pada fungsi `applyEmployeeRBAC()` (line ~2949), ubah blok `supervisor` agar `menuPelanggan` menjadi **visible** (`classList.remove('hidden')`).
- Tombol **Edit Badge** pada header halaman Pelanggan hanya muncul jika akses = `admin` (supervisor hanya bisa lihat dan kelola customer, bukan ubah konfigurasi tier).

---

## 2. Admin Bisa Menambahkan & Menghapus Tier (Contoh: Diamond)

### Masalah
Saat ini badge modal hanya bisa mengedit 3 tier yang sudah ada (Silver, Gold, Platinum). Admin tidak bisa menambahkan tier baru (misal Diamond) atau menghapus tier.

### Perubahan

#### [MODIFY] [PosController.php](file:///c:/Users/nasch/Downloads/Lapaknita/app/Http/Controllers/PosController.php)
- Update `saveCustomerTiers()`:
  - Terima field `id` sebagai **nullable** (bukan wajib), sehingga tier baru tanpa `id` bisa dibuat.
  - Untuk tier dengan `id`, lakukan `update`.
  - Untuk tier tanpa `id` (baru), lakukan `create` dengan field `name`, `badge`, `min_spent`, `discount_percent`.
  - Hapus tier yang ada di database tapi tidak ada di payload (artinya admin menghapusnya dari UI).

#### [MODIFY] [pos.blade.php](file:///c:/Users/nasch/Downloads/Lapaknita/resources/views/pos.blade.php)
- Pada `openEditBadgeModal()`:
  - Tambahkan tombol **+ Tambah Tier** di bawah daftar tier.
  - Setiap baris tier mendapat tombol hapus ❌ (kecuali Silver sebagai tier default).
  - Baris tier baru menyertakan input untuk **Nama Tier**, **Minimal Belanja**, **Emoji Badge**, dan **Diskon (%)**.
- Pada `bindBadgeForm()`:
  - Kumpulkan data tier dari semua baris, termasuk tier baru (yang belum punya `id`), dan tier yang dihapus.
  - Kirimkan array `tiers` ke backend.

---

## 3. Pindahkan Tombol "Buat Diskon" dari Menu Produk ke Menu Outlet

### Masalah
Tombol **Buat Diskon** (Diskon Keseluruhan per-Outlet) saat ini ada di header halaman **Produk**. Secara logis, diskon per-outlet lebih cocok di halaman **Outlet**.

### Perubahan

#### [MODIFY] [pos.blade.php](file:///c:/Users/nasch/Downloads/Lapaknita/resources/views/pos.blade.php)
- Pada `showPage()` (line ~811):
  - Halaman **Produk**: Hapus parameter `showAction2` dan `openGlobalDiscountModal` dari `setPageHeader()`.
  - Halaman **Outlet**: Tambahkan parameter `showAction2 = true`, `action2Fn = openGlobalDiscountModal`, `action2Label = 'Buat Diskon'`.

---

## 4. Tampilkan ID di Paling Atas Kartu untuk Semua Entitas yang Bisa Diedit

### Masalah
ID saat ini ada di dalam kartu, tapi bukan di posisi paling atas yang mudah dilihat. User ingin ID ditampilkan di paling atas kartu untuk Produk, Pegawai, Pelanggan, dan Outlet.

### Perubahan

#### [MODIFY] [pos.blade.php](file:///c:/Users/nasch/Downloads/Lapaknita/resources/views/pos.blade.php)
Untuk setiap fungsi render di halaman admin, tambahkan baris ID pill/badge di paling atas kartu:
- **`renderProductsAdmin()`**: Tambahkan `<div style="...">ID: ${p.id}</div>` sebagai elemen pertama di dalam `<article>`.
- **`renderEmployees()`**: Tambahkan `<div style="...">ID: ${emp.id}</div>` sebagai elemen pertama di dalam `<article>`. Hapus ID dari baris meta yang sudah ada agar tidak duplikat.
- **`renderCustomersPage()`**: Tambahkan `<div style="...">ID: ${cust.id}</div>` sebagai elemen pertama di dalam `<article>`. Hapus ID dari baris meta yang sudah ada.
- **`renderOutlets()`**: Tambahkan `<div style="...">ID: ${out.id}</div>` sebagai elemen pertama di dalam `<article>`. Hapus baris `ID Outlet: ${out.id}` yang sudah ada.

Styling: Badge kecil dengan background `#f1f5f9`, border-radius, font-size kecil, dan warna `#64748b` di pojok kanan atas kartu.

---

## Verification Plan

### Manual Verification
- Login sebagai **Supervisor** → verifikasi menu **Pelanggan** muncul dan bisa diakses.
- Login sebagai **Admin** → buka **Edit Badge** → tambah tier baru "Diamond" → simpan → verifikasi tier baru muncul.
- Login sebagai **Admin** → verifikasi tombol **Buat Diskon** ada di halaman **Outlet**, bukan **Produk**.
- Verifikasi semua kartu (Produk, Pegawai, Pelanggan, Outlet) menampilkan ID di paling atas kartu.
