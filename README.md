# SecureChain-Docs: Penyimpanan Berbasis Komposisi

## Daftar Isi
- [Pengenalan](#pengenalan)
- [Input Hash (Cryptographic Binding)](#input-hash-cryptographic-binding)
- [Rantai Blok (Chaining)](#rantai-blok-chaining)
- [Recovery (Majority Rule)](#recovery-majority-rule)
- [Arsitektur Sistem](#arsitektur-sistem)

## Pengenalan

SecureChain-Docs adalah sistem penyimpanan dokumen berbasis blockchain yang menerapkan konsep **Komposisi** untuk memastikan integritas data menyeluruh. Sistem ini menggabungkan berbagai komponen seperti metadata, konten file, dan hash kriptografi dalam satu kesatuan yang saling ketergantungan.

Komposisi memungkinkan setiap elemen (metadata, dokumen, hash) bekerja bersama secara harmonis dalam membentuk satu kesatuan yang tidak dapat dipisahkan.

---

## Input Hash (Cryptographic Binding)

### Konsep Dasar

Untuk memastikan integritas menyeluruh antara metadata dan isi file, hash tidak dibentuk secara sembarangan. Sistem menggabungkan (concatenate) variabel berikut sebelum dienkripsi menggunakan algoritma SHA-256:

### Komponen Input Hash

1. **Nomor Permohonan** (`no_permohonan`)
   - Identitas unik pengajuan dokumen
   - Memastikan setiap permohonan memiliki identitas yang berbeda

2. **Nomor Dokumen** (`no_dokumen`)
   - Kode dokumen yang spesifik
   - Membedakan jenis dan urutan dokumen

3. **Tanggal Dokumen** (`tgl_dokumen`)
   - Waktu pembuatan/penerbitan dokumen
   - Mencatat kapan dokumen dibuat secara formal

4. **Tanggal Filing** (`tgl_filing`)
   - Waktu pengajuan dokumen ke sistem
   - Mencatat kapan dokumen dimasukkan ke penyimpanan

5. **Dokumen Base64** (`dokumen_base64`)
   - Isi file yang telah terenkode dalam Base64
   - Memastikan seluruh konten file tercakup dalam hash

### Rumus Logika Pembentukan Hash

**CurrentHash = SHA256(No.Permohonan + No.Dokumen + Tgl.Dokumen + Tgl.Filing + DokumenBase64)**

Dimana `+` merepresentasikan operasi concatenation (penggabungan string).

### Keunggulan Pendekatan Ini

✅ **Integritas Menyeluruh**: Setiap perubahan pada komponen apapun akan mengubah hash  
✅ **Deterministik**: Input yang sama selalu menghasilkan hash yang sama  
✅ **Non-Reversible**: Hash tidak dapat dikembalikan ke nilai asli  
✅ **Komposisi Kuat**: Semua elemen metadata dan konten terikat dalam satu hash  

---

## Rantai Blok (Chaining)

### Konsep Dasar

Validasi diperkuat dengan menyertakan hash dari data sebelumnya (**Previous Hash**) ke dalam data baru. Hal ini menciptakan ketergantungan berantai. Jika data lama diubah, rantai akan putus (broken chain).

### Mekanisme Validasi

**Blok Genesis** (Blok Pertama)
- `PreviousHash = 0` atau `null`
- Hanya bergantung pada dirinya sendiri
- Menjadi fondasi untuk seluruh rantai

**Blok Berikutnya**
- `PreviousHash = CurrentHash` dari blok sebelumnya
- Membentuk rantai yang tidak terputus
- Setiap blok terikat pada blok sebelumnya

**Deteksi Kecurangan**
- Jika ada modifikasi pada data lama, `CurrentHash` akan berubah
- Hash yang sebelumnya mereferensi data lama akan tidak valid
- Rantai terputus → **Intrusion Detection**

### Rumus Validasi

**PreviousHash(n) = CurrentHash(n-1)**

Jika kondisi di atas terpenuhi, data valid. Jika tidak, ada indikasi manipulasi.

---

## Recovery (Majority Rule)

### Konsep Self-Healing dan Manual Healing

Untuk menangani kerusakan data (data corruption), sistem dirancang dengan konsep **Self-Healing** dan **Manual Healing** menggunakan **3 (tiga) sumber data**:

- **Database A** (Primary)
- **Database B** (Secondary)
- **Database C** (Tertiary)

### Mekanisme Pemulihan

#### 1. Deteksi Anomali

Sistem membaca Hash dari ketiga database secara bersamaan dan membandingkan nilainya:

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│ Database A   │     │ Database B   │     │ Database C   │
│ Hash: H1     │     │ Hash: H1     │     │ Hash: H2     │
└──────────────┘     └──────────────┘     └──────────────┘
       ✓                   ✓                   ✗
```

#### 2. Majority Vote (Voting Algorithm)

Jika ditemukan perbedaan (misal: Database C memiliki hash berbeda):

- **Database A**: Hash = `H1` ✓
- **Database B**: Hash = `H1` ✓
- **Database C**: Hash = `H2` ✗

**Keputusan**: Suara mayoritas (2 dari 3) mengatakan `H1` adalah nilai yang benar.

#### 3. Otomatis Perbaikan (Self-Healing)

Sistem secara otomatis memperbaiki Database C dengan nilai dari mayoritas:
- Membandingkan hash dari ketiga database
- Menentukan hash yang benar (mayoritas)
- Memperbarui database yang tidak sesuai
- Mencatat proses perbaikan dalam audit trail

#### 4. Manual Healing (Jika Mayoritas Juga Rusak)

Jika 2 atau lebih database menunjukkan perbedaan, diperlukan intervensi manual:

1. **Administrator Review**
   - Memeriksakan dokumen fisik atau sumber terpercaya lainnya
   - Melakukan validasi terhadap data asli

2. **Rekonstruksi Hash**
   - Melakukan validasi manual terhadap komponen input hash
   - Menghitung ulang hash dengan data yang sudah diverifikasi

3. **Update Database Korektif**
   - Memperbarui semua database dengan nilai yang sudah diverifikasi
   - Memastikan konsistensi di ketiga database

4. **Audit Trail**
   - Mencatat siapa, kapan, dan mengapa dilakukan koreksi
   - Menyimpan riwayat lengkap untuk keperluan audit

### Tabel Status Recovery

| Skenario | Database A | Database B | Database C | Aksi |
|----------|-----------|-----------|-----------|------|
| Normal | H1 | H1 | H1 | Lanjut |
| Self-Healing | H1 | H1 | H2 | Perbaiki C |
| Self-Healing | H1 | H2 | H1 | Perbaiki B |
| Manual Review | H1 | H2 | H3 | Admin Review |

---

## Arsitektur Sistem

### Komponen Utama

```
┌─────────────────────────────────────────────┐
│         Frontend (Web Interface)             │
└────────────────────┬────────────────────────┘
                     │
┌────────────────────▼────────────────────────┐
│      API Layer (RESTful Endpoints)          │
└────────────────────┬────────────────────────┘
                     │
┌────────────────────▼────────────────────────┐
│    Business Logic Layer                     │
│  - Hash Generation & Validation             │
│  - Chain Management                         │
│  - Recovery & Healing                       │
└────────────────────┬────────────────────────┘
                     │
        ┌────────────┼────────────┐
        │            │            │
┌───────▼────┐ ┌────▼──────┐ ┌──▼─────────┐
│ Database A │ │ Database B│ │ Database C │
│ (Primary)  │ │(Secondary)│ │ (Tertiary) │
└────────────┘ └───────────┘ └────────────┘
```

### Data Flow

1. **Input Dokumen** → Konversi Base64
2. **Komposisi Hash** → Kombinasi metadata + dokumen
3. **Enkripsi SHA-256** → Menghasilkan current hash
4. **Chain Binding** → Referensikan previous hash
5. **Multi-Database Storage** → Simpan ke 3 database
6. **Continuous Monitoring** → Deteksi perubahan
7. **Auto Healing/Manual Recovery** → Perbaiki jika ada anomali

---

## Keamanan & Best Practices

- ✅ **Hash Immutable**: Setelah dibuat, hash tidak dapat diubah
- ✅ **Chain Integrity**: Setiap perubahan pada blok lama terdeteksi
- ✅ **Redundancy**: 3 database untuk fault tolerance
- ✅ **Auto-Healing**: Sistem mampu memperbaiki kesalahan sendiri
- ✅ **Audit Trail**: Setiap transaksi tercatat dan teraudit
- ✅ **Majority Consensus**: Keputusan berdasarkan suara mayoritas
- ✅ **Tamper-Proof**: Manipulasi data akan langsung terdeteksi

---

## Informasi Proyek

- **Author**: Rifqi Novrian Hadi
- **Email**: Gaminggege601@gmail.com
- **Repository**: https://github.com/Ardnord/SecureChain-Docs
- **Framework**: CodeIgniter 4
- **Database**: MySQL (Multi-Database Architecture)

---

## Lisensi

Proyek ini dilindungi oleh lisensi. Lihat file [LICENSE](LICENSE) untuk detailnya.

---

