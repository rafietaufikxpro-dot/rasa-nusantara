# Product Requirements Document (PRD)
## Sistem Katering Nusantara V2 — Dashboard Admin dengan Optimasi Query (Nested Eager Loading)

**Versi:** 1.0
**Tanggal:** 14 September 2026
**Sumber:** LKPD "Sistem Katering Nusantara V2"
**Stack:** Laravel (PHP) + Blade + MySQL

---

## 1. Latar Belakang

Usaha katering "Rasa Nusantara" telah beroperasi di berbagai kota dan memiliki sistem pengiriman mandiri. Tim membutuhkan **Dashboard Admin** yang dapat menampilkan data pesanan secara detail dalam satu tabel: nama pelanggan beserta kota asalnya, kurir pengantar, metode pembayaran, dan daftar lengkap menu yang dipesan beserta kategorinya.

Karena relasi data pada kasus ini menembus hingga 3 tingkat kedalaman (Order → OrderItem → Menu → Category, dan Order → Customer → City), risiko utama pada fitur ini adalah **N+1 Query Problem** akibat *lazy loading* yang tidak tepat, yang berpotensi membuat server down saat data berskala besar.

## 2. Tujuan Produk

1. Menyediakan dashboard admin yang menampilkan data pesanan lengkap dan akurat dalam satu tabel.
2. Membuktikan penerapan **Nested Eager Loading** untuk menekan jumlah query database ke level optimal (di bawah 10 query untuk memuat 15 order beserta seluruh relasinya).
3. Menyediakan alur autentikasi admin (halaman **Login** dan **Register**) agar dashboard hanya bisa diakses oleh pengguna yang berwenang (asumsi tambahan, tidak disebut eksplisit di LKPD).
4. Menyediakan antarmuka modern, minimalis, dan mendukung **mode gelap (dark mode)** yang konsisten di seluruh halaman (asumsi tambahan, lihat Bab 8.3).

## 3. Scope

### 3.1 In-Scope
- Skema database 7 tabel beserta relasinya (lihat Bab 5).
- Seeder/factory untuk data dummy sesuai volume yang ditentukan.
- Halaman dashboard admin (tabel HTML/Blade) yang menampilkan data pesanan, dilengkapi pencarian & filter (asumsi tambahan).
- Halaman **Login** dan **Register** untuk admin (asumsi tambahan).
- Optimasi query menggunakan Nested Eager Loading.
- Autentikasi login untuk admin (asumsi tambahan).
- Desain UI modern-minimalis kustom dengan dukungan **dark mode** (asumsi tambahan — lihat Bab 8.3, spesifikasi lengkap di `UIUX_Spec_Dashboard_Katering.txt`).
- Dokumentasi README dengan bukti perbandingan jumlah query sebelum dan sesudah optimasi.

### 3.2 Out-of-Scope
- Fitur CRUD penuh (tambah/edit/hapus order, menu, dsb.) — LKPD hanya meminta tampilan (read-only dashboard).
- Fitur pelanggan (customer-facing app / pemesanan online).
- Integrasi pembayaran nyata (payment gateway).
- Notifikasi/tracking pengiriman real-time.
- Multi-role admin (LKPD tidak menyebutkan tingkatan hak akses).

## 4. Target Pengguna

| Peran | Deskripsi | Kebutuhan Utama |
|---|---|---|
| Admin | Staf internal yang memantau pesanan | Melihat data pesanan lengkap dengan cepat, tanpa server lambat/crash |

## 5. Skema Database

7 tabel utama beserta relasinya:

| Tabel | Kolom | Relasi |
|---|---|---|
| `cities` | id, name | — |
| `categories` | id, name | — |
| `payment_methods` | id, name | — |
| `couriers` | id, name, phone | — (melayani semua kota, tidak dibatasi city_id — dikonfirmasi) |
| `customers` | id, name, phone, city_id | belongsTo `City` |
| `menus` | id, name, price, category_id | belongsTo `Category` |
| `orders` | id, customer_id, payment_method_id, courier_id (NOT NULL), status | belongsTo `Customer`, `PaymentMethod`, `Courier` — `courier_id` wajib terisi sejak order dibuat (dikonfirmasi) |
| `order_items` | id, order_id, menu_id, qty, subtotal | belongsTo `Order`, `Menu` |
| `users` *(asumsi tambahan)* | id, name, email, password, is_approved (boolean, default false) | Digunakan untuk login admin (tabel default Laravel + 1 kolom tambahan `is_approved`, tidak dihitung dalam "7 tabel" inti LKPD) |

**Catatan relasi turunan (untuk eager loading bertingkat):**
- `Order` → `Customer` → `City`
- `Order` → `OrderItems` → `Menu` → `Category`

**Definisi nilai `orders.status` (diperbaiki — sebelumnya tidak didefinisikan):**
Enum: `pending`, `diproses`, `dikirim`, `selesai`, `dibatalkan`. Digenerate acak saat seeding, tidak wajib ditampilkan di kolom dashboard (di luar scope LKPD) tapi harus tersimpan agar data konsisten.

**Aturan `order_items.subtotal` (diperbaiki — sebelumnya tidak ada aturan bisnis):**
`subtotal = qty * menus.price` dihitung **sekali saat data dibuat** dan disimpan sebagai nilai statis (bukan dihitung ulang setiap request). Ini menjaga histori harga tetap akurat meskipun harga menu berubah di kemudian hari.

## 6. Data Seeding (Factory & Seeder)

Urutan eksekusi seeder harus mengikuti dependency antar tabel:

1. 50 Cities
2. 10 Categories
3. 3 Payment Methods
4. 10 Couriers
5. 150 Menus (bergantung pada Categories)
6. 200 Customers (bergantung pada Cities)
7. 150 Orders (bergantung pada Customers, Payment Methods, Couriers)
8. Untuk setiap Order: 3–5 Order Items secara acak (bergantung pada Orders & Menus)
9. *(Asumsi tambahan)* 1 akun admin di tabel `users` dengan kredensial tetap (bukan acak) dan `is_approved = true`, agar bisa dipakai login saat demo/penilaian — misalnya `admin@rasanusantara.test` dengan password yang didokumentasikan di README.

## 7. Functional Requirements — Dashboard Admin

Halaman dashboard menampilkan tabel dengan kolom berikut:

| Kolom | Isi |
|---|---|
| ID Order | ID unik pesanan |
| Pelanggan | Nama pelanggan + nama kota |
| Pesanan | Daftar item: `qty x Nama Menu (Kategori)` — ditampilkan via loop di dalam baris |
| Total Harga *(diperbaiki — sebelumnya tidak ada)* | Jumlah seluruh `subtotal` dari `order_items` milik order tersebut (`SUM(order_items.subtotal)`) |
| Pengiriman & Pembayaran | Nama kurir – metode pembayaran |

**Kebutuhan tambahan (asumsi, sudah tercermin di demo UI):**
- Pagination pada tabel order (mengikuti hint LKPD: `paginate(15)`).
- Halaman hanya bisa diakses setelah admin login.
- Kolom "Pesanan" menampilkan item pertama, sisanya dapat di-*expand* (tidak me-render seluruh item sekaligus agar tabel tetap ringkas dibaca).
- Pencarian (nama pelanggan/kota) dan filter (status, kota) di atas tabel — dijalankan sebagai query terfilter di controller, tetap memakai eager loading yang sama agar tidak membuka celah N+1 baru.
- Strip ringkasan statistik di atas tabel: total pesanan, total omzet, jumlah pesanan *pending*, rata-rata nilai per pesanan — dihitung via query agregat (`count()`, `sum()`), bukan diambil dari koleksi yang sudah di-paginate.

## 7B. Functional Requirements — Login & Register

**Halaman Login:**
- Field: email, kata sandi, checkbox "Ingat saya", link "Lupa kata sandi?" (tautan saja, fitur reset password **di luar scope** kecuali dikonfirmasi lain).
- Validasi: email terdaftar & kata sandi cocok → redirect ke dashboard; gagal → tampilkan pesan error di bawah field terkait (bukan alert generik).

**Halaman Register:**
- Field: nama lengkap, email, kata sandi, konfirmasi kata sandi.
- Validasi: email unik, kata sandi minimal 8 karakter, konfirmasi harus cocok.
- **Alur persetujuan (dikonfirmasi):** Register bersifat publik (siapa saja bisa mengisi form), tapi akun baru tersimpan dengan `is_approved = false` dan **tidak bisa login** sampai disetujui oleh admin yang sudah ada. Setelah submit, tampilkan pesan "Akun berhasil dibuat, menunggu persetujuan admin" — bukan langsung redirect ke dashboard.
- Admin yang sudah login (`is_approved = true`) dapat melihat daftar akun yang menunggu persetujuan dan menyetujuinya (halaman/menu sederhana, lihat Bab 9 poin 6).
- Saat percobaan login dengan akun yang belum disetujui, tampilkan pesan error yang jelas: "Akun Anda belum disetujui admin" — bukan pesan generik "email/password salah".

**Lupa kata sandi (dikonfirmasi — di luar scope):**
- Link "Lupa kata sandi?" tetap ditampilkan di UI untuk kelengkapan tampilan, tapi **tidak perlu fungsional** (tidak perlu alur kirim email reset). Klik link ini boleh menuju halaman placeholder atau tidak melakukan apa-apa.

## 8. Non-Functional Requirements

### 8.1 Performa (Kritis)
- **Wajib menggunakan Nested Eager Loading** dengan notasi titik, contoh:
  ```php
  $orders = Order::with([
      'customer.city',
      'orderItems.menu.category',
      'paymentMethod',
      'courier'
  ])->paginate(15);
  ```
- Aktifkan `Model::preventLazyLoading(!app()->isProduction());` di `AppServiceProvider` agar lazy loading yang tidak sengaja langsung terdeteksi saat development.
- Target: memuat 1 halaman (15 order + seluruh relasi) dalam **kurang dari 10 query database** (dibuktikan lewat Laravel Debugbar).
- Sebagai pembanding, versi awal (lazy loading naif) harus didokumentasikan menghasilkan ratusan hingga ribuan query.

### 8.2 Keamanan (Asumsi Tambahan — diperbarui)
- Route dashboard admin dilindungi middleware `auth`.
- Password admin di-hash (default Laravel `Hash::make`).
- **Login ditolak jika `is_approved = false`** (dikonfirmasi): tambahkan pengecekan di proses login (custom guard/listener atau logika tambahan di controller login) agar akun yang belum disetujui tidak bisa masuk meskipun email/password benar.
- Menu persetujuan akun baru hanya bisa diakses oleh admin yang statusnya sudah `is_approved = true` (tidak perlu role terpisah — LKPD tidak meminta tingkatan hak akses, lihat Bab 3.2 Out-of-Scope).

### 8.3 UI/Styling (Asumsi Tambahan — diperbarui)
> **Catatan:** bagian ini sebelumnya berasumsi styling default Bootstrap/Tailwind tanpa desain kustom. Setelah konsep UI/UX dibuat, asumsi ini **diganti** dengan yang berikut agar konsisten dengan demo yang sudah dirancang.

- UI menggunakan **design system kustom** (bukan tema default Bootstrap), dibangun dengan Tailwind CSS sebagai utility layer (paling cocok untuk implementasi token warna & tipografi kustom di Blade/Laravel).
- Palet warna, tipografi (Fraunces + IBM Plex Sans), dan struktur komponen mengikuti spesifikasi di `UIUX_Spec_Dashboard_Katering.txt`.
- **Dark mode wajib didukung** di seluruh halaman (Login, Register, Dashboard), dengan toggle manual (ikon matahari/bulan). Implementasi: gunakan Tailwind `dark:` variant dengan strategi `class` (bukan hanya `prefers-color-scheme`), agar toggle manual dari pengguna bisa mengesampingkan preferensi sistem.
- **Persistensi pilihan tema:** disimpan di `localStorage` browser (asumsi — tidak per-akun di database, karena LKPD tidak meminta preferensi tersimpan lintas perangkat). Jika dibutuhkan tersimpan per-akun, perlu kolom tambahan di tabel `users` (lihat Bab 12).
- Tabel responsif: di layar sempit, header kolom disembunyikan dan setiap baris menjadi blok vertikal (lihat spec bagian 6 "Responsive").

## 9. Deliverables

1. Migration untuk 7 tabel inti beserta foreign key, ditambah tabel `users` (default Laravel) untuk login admin.
2. Model Eloquent dengan relasi yang didefinisikan.
3. Factory + `DatabaseSeeder` dengan urutan sesuai Bab 6, termasuk seeding 1 akun admin.
4. Controller dashboard admin dengan implementasi Nested Eager Loading, termasuk logika pencarian/filter dan query agregat untuk strip statistik.
5. View Blade untuk tabel dashboard, mengikuti design system di `UIUX_Spec_Dashboard_Katering.txt`.
6. View Blade untuk halaman **Login** dan **Register**, layout split-screen sesuai spec (lihat Bab 7B), termasuk halaman/menu sederhana bagi admin untuk menyetujui akun baru (`is_approved`).
7. Middleware/auth (`auth`) untuk login admin, memakai scaffolding Laravel bawaan (Breeze/Fortify) atau implementasi login manual sederhana.
8. Implementasi **dark mode toggle** (Tailwind `dark:` class strategy + JS untuk simpan preferensi ke `localStorage`).
9. Konfigurasi token desain (warna, font Fraunces & IBM Plex Sans) di `tailwind.config.js`.
10. `README.md` repository dengan format:
    - Judul: `Sistem Katering Nusantara (Advanced Eager Loading)`
    - Nama & Kelas
    - Deskripsi proyek
    - Screenshot bukti query lazy loading (sebelum optimasi)
    - Screenshot bukti query eager loading (setelah optimasi, di bawah 10 query)

## 10. Acceptance Criteria

- [ ] Seluruh 7 tabel dan relasinya berhasil dimigrasikan tanpa error.
- [ ] Seeder berjalan sukses menghasilkan volume data sesuai Bab 6.
- [ ] Dashboard menampilkan seluruh kolom yang diminta (ID, Pelanggan+Kota, Pesanan dengan kategori, Total Harga, Kurir+Pembayaran) dengan data yang benar.
- [ ] Total Harga per order = penjumlahan seluruh `subtotal` item pesanan pada order tersebut.
- [ ] Setiap order memiliki `courier_id` terisi (tidak null) sejak dibuat.
- [ ] Admin harus login sebelum bisa mengakses dashboard.
- [ ] Jumlah query saat memuat dashboard (15 order per halaman) berada di bawah 10 query, dibuktikan via Laravel Debugbar.
- [ ] `Model::preventLazyLoading` aktif di non-production dan tidak memicu error saat dashboard diakses (artinya tidak ada lazy loading tersisa).
- [ ] Halaman Login berfungsi: kredensial benar → masuk ke dashboard; kredensial salah → pesan error tampil di form, bukan redirect diam-diam.
- [ ] Halaman Register berfungsi: validasi email unik & kata sandi minimal 8 karakter berjalan; akun baru tersimpan dengan `is_approved = false` dan **tidak bisa login** sampai disetujui.
- [ ] Admin yang sudah disetujui bisa melihat & menyetujui akun baru yang menunggu; setelah disetujui, akun tersebut baru bisa login.
- [ ] Link "Lupa kata sandi?" tampil di UI tapi tidak perlu fungsional (tidak wajib ada alur reset email).
- [ ] Toggle dark mode berfungsi konsisten di ketiga halaman (Login, Register, Dashboard) dan pilihan tema bertahan saat halaman di-refresh (via `localStorage`).
- [ ] Tampilan (warna, tipografi, layout) sesuai dengan `UIUX_Spec_Dashboard_Katering.txt`.
- [ ] README repository sudah diperbarui sesuai format yang ditentukan, lengkap dengan 2 screenshot bukti optimasi.

## 11. Risiko & Mitigasi

| Risiko | Mitigasi |
|---|---|
| N+1 Query menyebabkan server down saat data besar | Nested eager loading wajib + `preventLazyLoading` di non-production |
| Urutan seeder salah menyebabkan foreign key error | Ikuti urutan eksekusi di Bab 6 secara ketat |
| Developer lupa relasi tersembunyi (mis. akses `$order->customer->city` di view tanpa eager load) | Aktifkan `preventLazyLoading` sehingga error langsung muncul saat development |

## 12. Asumsi & Pertanyaan Terbuka

Berikut hal-hal yang **tidak disebutkan eksplisit di LKPD** dan sudah dikonfirmasi/diasumsikan dalam PRD ini:

| Item | Asumsi yang digunakan |
|---|---|
| Format output PRD | Markdown (.md) |
| Stack teknis | Laravel + Blade + MySQL |
| Autentikasi | Login admin ditambahkan sebagai standar keamanan, memakai tabel `users` bawaan Laravel + 1 akun admin yang di-seed |
| Styling | Styling dasar (Bootstrap/Tailwind bawaan Laravel), tidak custom |
| Total Harga per order | Ditambahkan sebagai kolom dashboard, dihitung dari `SUM(order_items.subtotal)` |
| Relasi kurir–kota | Kurir tidak dibatasi kota, bisa melayani semua kota |
| `courier_id` pada orders | Wajib terisi (NOT NULL) sejak order dibuat |
| Halaman Login & Register | Ditambahkan sebagai alur autentikasi standar, layout split-screen sesuai `UIUX_Spec_Dashboard_Katering.txt` |
| UI/Styling | Diganti dari "default Bootstrap" menjadi design system kustom (Tailwind + token warna/font khusus) — lihat Bab 8.3 |
| Dark mode | Wajib ada di semua halaman, toggle manual (bukan hanya ikut preferensi sistem), disimpan di `localStorage` |
| Akses Register | **Dikonfirmasi:** dibatasi via alur persetujuan — akun baru berstatus `is_approved = false` dan tidak bisa login sampai disetujui admin lain (lihat Bab 7B & 8.2) |
| Lupa kata sandi | **Dikonfirmasi:** di luar scope — link ditampilkan tapi tidak perlu fungsional |

**Pertanyaan terbuka yang masih perlu dikonfirmasi:**

1. **Persistensi tema gelap per-akun:** saat ini diasumsikan hanya tersimpan di browser (`localStorage`). Apakah perlu tersimpan di database per akun admin (supaya konsisten walau ganti perangkat)?
2. **Approval akun baru:** apakah *semua* admin yang sudah disetujui boleh menyetujui akun baru lainnya (peer approval), atau perlu ditentukan satu akun admin "utama" yang punya hak eksklusif menyetujui? LKPD tidak menyebutkan tingkatan role, jadi saat ini diasumsikan semua admin yang `is_approved = true` punya hak yang sama termasuk menyetujui akun baru — perlu dikonfirmasi apakah ini sesuai keinginan.

Jika ada detail lain yang perlu disesuaikan (misalnya nilai spesifik status order, apakah dashboard perlu fitur filter/search, atau apakah perlu role selain admin), silakan informasikan agar PRD dapat diperbarui.
