# BPJS PCare FKTP Bridging - Complete Documentation

## 📋 Overview

Implementasi bridging BPJS PCare untuk **FKTP (Fasilitas Kesehatan Tingkat Pertama)** berdasarkan SIMRS Khanza desktop. Sistem ini memungkinkan klinik untuk mengirim data pelayanan pasien BPJS ke aplikasi PCare secara real-time.

**Status**: ✅ Core Foundation Complete
**Version**: 1.0
**Date**: 2025-11-18

---

## 🎯 Key Features

### ✅ Yang Sudah Diimplementasikan:

1. **PCare Pendaftaran** - Registrasi pasien BPJS
2. **PCare Kunjungan** - Data kunjungan dengan diagnosa & therapy
3. **PCare Tindakan** - Tindakan medis yang dilakukan
4. **PCare Obat** - Obat yang diberikan (racikan & non-racikan)
5. **PCare Rujukan** - Rujukan ke spesialis/RS
6. **Code Mapping** - Mapping kode internal ke kode PCare
7. **Activity Logging** - Log semua aktivitas API untuk audit
8. **Dashboard Widget** - Statistik PCare real-time
9. **Filament Resources** - UI untuk monitoring PCare

---

## 🗄️ Database Structure

### Tables Created:

| Table | Purpose | Primary Key |
|-------|---------|-------------|
| `pcare_pendaftaran` | Pendaftaran pasien BPJS | `no_rawat` |
| `pcare_kunjungan` | Kunjungan dengan diagnosa | `no_kunjungan` |
| `pcare_tindakan` | Tindakan medis | `id` (auto-increment) |
| `pcare_obat_diberikan` | Obat yang diberikan | `id` (auto-increment) |
| `pcare_rujukan` | Rujukan spesialis/RS | `no_rujukan` |
| `pcare_mapping_poli` | Mapping kode poli | `kd_poli_internal` |
| `pcare_mapping_dokter` | Mapping kode dokter | `kd_dokter_internal` |
| `pcare_mapping_diagnosa` | Mapping ICD-10 | `kd_diagnosa_internal` |
| `pcare_mapping_tindakan` | Mapping tindakan | `id` |
| `pcare_mapping_obat` | Mapping obat | `kd_obat_internal` |
| `pcare_activity_log` | Log aktivitas API | `id` (auto-increment) |

---

## 🔧 Configuration

### File: `config/bpjs.php`

```php
'pcare' => [
    'base_url' => env('BPJS_PCARE_URL'),
    'username' => env('BPJS_PCARE_USERNAME'),
    'password' => env('BPJS_PCARE_PASSWORD'),
    'app_code' => env('BPJS_PCARE_APP_CODE', '095'),
    'user_key' => env('BPJS_PCARE_USER_KEY'),
],
```

### Environment Variables (`.env`):

```bash
# BPJS PCare Configuration
BPJS_ENABLED=false

# PCare API Credentials
BPJS_PCARE_URL=https://apijkn-dev.bpjs-kesehatan.go.id/pcare-rest-dev
BPJS_PCARE_USERNAME=YourPCareUsername
BPJS_PCARE_PASSWORD=YourPCarePassword
BPJS_PCARE_APP_CODE=095
BPJS_PCARE_USER_KEY=YourPCareUserKey

# Facility Information
BPJS_KODE_PPK=0000000
BPJS_NAMA_PPK="KLINIK PRATAMA"
BPJS_JENIS_FASKES=1

# Settings
BPJS_TIMEOUT=30
BPJS_LOG_ENABLED=true
```

---

## 📊 Workflow PCare FKTP

### Complete Flow:

```
1. PENDAFTARAN (Registration)
   ├─ Pasien datang dengan kartu BPJS
   ├─ Register di sistem internal (reg_periksa)
   ├─ Kirim data pendaftaran ke PCare
   └─ Dapatkan no_kunjungan dari PCare

2. KUNJUNGAN (Visit)
   ├─ Dokter periksa pasien
   ├─ Input tanda vital, keluhan, pemeriksaan
   ├─ Tentukan diagnosa (primer, sekunder, tersier)
   ├─ Beri therapy/tindakan
   └─ Kirim data kunjungan ke PCare

3. TINDAKAN (Optional)
   └─ Jika ada tindakan medis, kirim ke PCare

4. OBAT (Optional)
   └─ Jika ada obat, kirim detail obat ke PCare

5. RUJUKAN (Optional)
   ├─ Jika perlu rujuk ke spesialis/RS
   └─ Kirim data rujukan ke PCare
```

---

## 🔐 Authentication

PCare menggunakan **HMAC SHA256 signature** untuk authentication.

### Header Requirements:

```php
X-cons-id: {username}
X-timestamp: {unix_timestamp}
X-signature: {HMAC_SHA256(username&timestamp, user_key)}
X-authorization: Basic {base64(username:password:app_code)}
user_key: {user_key}
Content-Type: application/json
```

### Implementation (PCareService):

```php
private function generateHeaders(): array
{
    $timestamp = time();
    $auth = base64_encode($this->username . ':' . $this->password . ':' . $this->appCode);
    $signature = hash_hmac('sha256', $this->username . '&' . $timestamp, $this->userKey, false);

    return [
        'X-cons-id' => $this->username,
        'X-timestamp' => (string)$timestamp,
        'X-signature' => $signature,
        'X-authorization' => 'Basic ' . $auth,
        'user_key' => $this->userKey,
        'Content-Type' => 'application/json',
    ];
}
```

---

## 🚀 API Endpoints

### Pendaftaran:

| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/pendaftaran` | Insert pendaftaran |
| GET | `/pendaftaran/{tglDaftar}/{start}/{limit}` | Get list pendaftaran |
| DELETE | `/pendaftaran/{noUrut}/{noKartu}/{tglDaftar}/{kdProvider}` | Delete pendaftaran |

### Kunjungan:

| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/kunjungan` | Insert kunjungan |
| PUT | `/kunjungan` | Update kunjungan |
| GET | `/kunjungan/{noKunjungan}` | Get detail kunjungan |

### Tindakan:

| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/tindakan` | Insert tindakan |

### Rujukan:

| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/rujukan` | Insert rujukan |

### Referensi:

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/diagnosa/{keyword}/{start}/{limit}` | Get diagnosa |
| GET | `/obat/{keyword}/{start}/{limit}` | Get obat |
| GET | `/tindakan/{keyword}/{start}/{limit}` | Get tindakan |
| GET | `/provider/{start}/{limit}` | Get provider |
| GET | `/spesialis` | Get spesialis |
| GET | `/spesialis/{kode}/subspesialis` | Get subspesialis |
| GET | `/sarana` | Get sarana |
| GET | `/khusus` | Get rujukan khusus |

---

## 💻 Usage Examples

### 1. Get Peserta (Check BPJS Card):

```php
use App\Services\PCareService;

$pcareService = new PCareService();
$result = $pcareService->getPeserta('0001234567890');

if ($result['success']) {
    $peserta = $result['data'];
    echo "Nama: " . $peserta['nama'];
    echo "Status: " . $peserta['statusPeserta'];
}
```

### 2. Insert Pendaftaran:

```php
$data = [
    'kdProviderPeserta' => '0401R001',
    'tglDaftar' => '01-11-2025',
    'noKartu' => '0001234567890',
    'kdPoli' => '001', // Umum
    'keluhan' => 'Demam dan batuk',
    'kunjSakit' => true,
    'sistole' => 120,
    'diastole' => 80,
    'beratBadan' => 65,
    'tinggiBadan' => 165,
    'respRate' => 20,
    'heartRate' => 80,
    'kdTkp' => '10', // Rujuk RS
];

$result = $pcareService->insertPendaftaran($data, $noRawat);
```

### 3. Insert Kunjungan:

```php
$data = [
    'noKunjungan' => '0000000001',
    'noKartu' => '0001234567890',
    'tglDaftar' => '01-11-2025',
    'kdPoli' => '001',
    'keluhan' => 'Demam tinggi 3 hari',
    'kdSadar' => '01', // Composmentis
    'sistole' => 120,
    'diastole' => 80,
    'beratBadan' => 65,
    'tinggiBadan' => 165,
    'respRate' => 22,
    'heartRate' => 85,
    'terapi' => 'Paracetamol 500mg, Amoxicillin 500mg',
    'kdStatusPulang' => '0', // Selesai
    'tglPulang' => '01-11-2025',
    'kdDokter' => 'D001',
    'kdDiag1' => 'A00', // ICD-10 primer
    'kdDiag2' => '', // ICD-10 sekunder
    'kdDiag3' => '', // ICD-10 tersier
    'kdPoliRujukInternal' => '',
    'kdTacc' => '0',
    'alasanTacc' => '',
];

$result = $pcareService->insertKunjungan($data, $noRawat, $noKunjungan);
```

---

## 📱 Filament Resources

### 1. PcarePendaftaranResource

**Location**: `app/Filament/Resources/PcarePendaftaranResource.php`

**Features**:
- List pendaftaran with tabs (Semua, Belum Kirim, Sudah Kirim, Gagal, Hari Ini)
- View/Edit pendaftaran
- Action: Kirim ke PCare
- Action: Hapus dari PCare (jika sudah terkirim)
- Bulk action: Kirim multiple pendaftaran
- Filters: Status kirim, Tanggal range

**Columns**:
- No. Rawat, Tanggal Daftar, No. Kartu, Nama Pasien
- Poli, Status Kirim, No. Kunjungan

### 2. PcareKunjunganResource

**Location**: `app/Filament/Resources/PcareKunjunganResource.php`

**Features**:
- List kunjungan with tabs (Semua, Hari Ini, Belum Kirim, Sudah Kirim, Dengan Rujukan)
- View detail kunjungan lengkap
- Action: Kirim ke PCare
- Filters: Status kirim, Dengan rujukan, Tanggal range

**Info Display**:
- Informasi Kunjungan (no, tanggal, pasien, poli)
- Tanda Vital (TD, BB, TB, lingkar perut, resp rate, heart rate)
- Keluhan & Pemeriksaan (subjective, objective)
- Diagnosa (primer, sekunder, tersier)
- Terapi & Tindak Lanjut (therapy, TKP, status pulang)
- Rujukan (jika ada)

### 3. PcareActivityLogResource

**Location**: `app/Filament/Resources/PcareActivityLogResource.php`

**Features**:
- List semua aktivitas API
- Tabs: Semua, Hari Ini, Success, Failed
- View detail request & response
- Filters: Status, Activity type, Action, Tanggal

**Columns**:
- Waktu, Aktivitas, Aksi, Status, HTTP Code
- No. Rawat, No. Kunjungan, User, Endpoint

---

## 📊 Dashboard Widget

### PcareStatsWidget

**Location**: `app/Filament/Widgets/PcareStatsWidget.php`

**Stats Displayed**:
1. **Pendaftaran Hari Ini** - Jumlah pendaftaran + belum terkirim
2. **Kunjungan Hari Ini** - Jumlah kunjungan + belum terkirim
3. **Rujukan Hari Ini** - Jumlah rujukan ke spesialis/RS
4. **API Calls Hari Ini** - Total API calls + yang gagal
5. **Pendaftaran Gagal** - Yang perlu dikirim ulang (conditional)

**Features**:
- Auto-refresh every 30 seconds
- Sparkline charts
- Color-coded indicators
- Description with counts

---

## 🗺️ Code Mapping

### Why Mapping Needed?

Kode internal sistem mungkin berbeda dengan kode PCare. Mapping table memastikan konversi yang benar.

### Mapping Tables:

#### 1. pcare_mapping_poli

```php
kd_poli_internal  → kd_poli_pcare
'001'             → '001'  // Umum
'002'             → '014'  // Gigi
```

#### 2. pcare_mapping_dokter

```php
kd_dokter_internal → kd_dokter_pcare
'D001'             → 'DK001'
```

#### 3. pcare_mapping_diagnosa

```php
kd_diagnosa_internal → kd_diagnosa_pcare
'A00'                → 'A00'  // Biasanya sama (ICD-10)
```

#### 4. pcare_mapping_tindakan

```php
kd_tindakan_internal → kd_tindakan_pcare
'T001'               → '89.03'
```

#### 5. pcare_mapping_obat

```php
kd_obat_internal → kd_obat_pcare
'OBT001'         → '91000001'
```

### Usage in Code:

```php
// Get PCare code from internal code
$kdPoliPcare = DB::table('pcare_mapping_poli')
    ->where('kd_poli_internal', $kdPoliInternal)
    ->value('kd_poli_pcare');
```

---

## 🔍 Activity Logging

### Purpose:

- **Audit Trail**: Track all API interactions
- **Debugging**: View request/response for troubleshooting
- **Monitoring**: Identify failed requests
- **Reporting**: Generate API usage reports

### Log Structure:

```php
PcareActivityLog {
    no_rawat: string
    no_kunjungan: string
    activity_type: string  // 'insert_pendaftaran', 'insert_kunjungan', etc
    action: string         // 'get', 'post', 'put', 'delete'
    endpoint: string       // '/pendaftaran', '/kunjungan', etc
    status: string         // 'success', 'failed'
    http_code: int         // 200, 201, 400, 500, etc
    request_data: json     // Request payload
    response_data: json    // Response from PCare
    error_message: string  // Error message if failed
    user: string           // Who initiated
}
```

### Automatic Logging:

All API calls through `PCareService` are automatically logged.

```php
// In PCareService
private function logActivity(...) {
    PcareActivityLog::create([
        'activity_type' => $activityType,
        'action' => $action,
        'endpoint' => $endpoint,
        'status' => $status,
        'http_code' => $httpCode,
        'request_data' => json_encode($requestData),
        'response_data' => json_encode($responseData),
        'error_message' => $errorMessage,
        'user' => auth()->user()->username ?? 'system',
    ]);
}
```

---

## ⚠️ Important Notes

### 1. Status Tracking

Setiap record memiliki `status_kirim`:
- **Belum**: Belum dikirim ke PCare
- **Sudah**: Berhasil dikirim ke PCare
- **Gagal**: Gagal kirim (perlu dikirim ulang)

### 2. No Kunjungan

`no_kunjungan` adalah key dari PCare yang didapat setelah pendaftaran berhasil. **Simpan dengan baik** karena digunakan untuk semua transaksi selanjutnya.

### 3. Kode TKP (Tindak Lanjut)

| Kode | Arti |
|------|------|
| 10 | Rujuk ke RS |
| 20 | Rujuk ke Puskesmas |
| 40 | Rujuk ke Spesialis |
| 50 | Kembali ke Faskes Perujuk |

### 4. Kunjungan Sakit

- `0` = Sehat (pemeriksaan rutin, medical check-up)
- `1` = Sakit (kunjungan karena keluhan)

### 5. Jenis Obat

- `23` = Non Racikan
- `24` = Racikan

### 6. Timestamp Format

PCare menggunakan format: `dd-MM-yyyy` untuk tanggal.

```php
$tglDaftar = date('d-m-Y'); // 18-11-2025
```

---

## 🚧 Next Steps / TODO

### Integration with Registration Workflow:

1. **Auto-create pendaftaran** saat pasien BPJS register
2. **Auto-send to PCare** setelah pemeriksaan selesai
3. **Validation** no kartu BPJS sebelum register
4. **Sync mapping codes** dari PCare referensi

### UI Enhancements:

1. **Action button** "Kirim ke PCare" di RawatJalanResource
2. **Badge indicator** status PCare di registration list
3. **Quick send** untuk kunjungan yang sudah lengkap
4. **Bulk retry** untuk yang gagal

### Advanced Features:

1. **Auto-retry** untuk gagal kirim dengan exponential backoff
2. **Queue system** untuk pengiriman asynchronous
3. **Notification** jika ada yang gagal kirim
4. **Export** activity log untuk reporting
5. **Dashboard charts** untuk trend analisis

---

## 📚 References

- **SIMRS Khanza Desktop**: https://github.com/mas-elkhanza/SIMRS-Khanza
- **BPJS PCare Documentation**: (Official docs dari BPJS)
- **PCare Web Service Guide**: (Developer guide dari BPJS)

---

## 🐛 Troubleshooting

### 1. Signature Mismatch

**Error**: "Signature tidak valid"

**Solution**:
- Pastikan `user_key` benar
- Pastikan format timestamp unix time
- Cek format signature HMAC SHA256

### 2. Connection Timeout

**Error**: "Connection timeout"

**Solution**:
- Increase `BPJS_TIMEOUT` di .env
- Cek koneksi internet
- Pastikan firewall tidak block

### 3. Invalid Provider Code

**Error**: "Kode provider tidak valid"

**Solution**:
- Cek `BPJS_KODE_PPK` di .env
- Pastikan kode provider benar dari BPJS
- Sync dengan data referensi PCare

### 4. Data Tidak Sesuai

**Error**: "Data tidak sesuai format"

**Solution**:
- Cek mapping codes
- Validasi required fields
- Pastikan format tanggal benar (dd-MM-yyyy)

---

## ✅ Testing Checklist

### Manual Testing:

- [ ] Insert pendaftaran ke PCare
- [ ] Get peserta by no kartu
- [ ] Insert kunjungan dengan diagnosa
- [ ] Insert tindakan
- [ ] Insert obat (racikan & non-racikan)
- [ ] Insert rujukan
- [ ] Delete pendaftaran
- [ ] View activity log
- [ ] Check dashboard stats
- [ ] Filter by date range
- [ ] Bulk send pendaftaran

### Integration Testing:

- [ ] Register pasien BPJS → auto create pendaftaran
- [ ] Complete examination → auto create kunjungan
- [ ] Add diagnosa → update kunjungan
- [ ] Prescribe medication → insert obat
- [ ] Create rujukan → insert rujukan
- [ ] Check all data synced to PCare

---

## 📝 Changelog

### Version 1.0 (2025-11-18)

**Added**:
- Initial PCare FKTP bridging implementation
- 6 database migrations (pendaftaran, kunjungan, tindakan, obat, rujukan, mapping)
- 6 Eloquent models with relationships
- PCareService with complete API integration
- 3 Filament Resources (Pendaftaran, Kunjungan, Activity Log)
- PcareStatsWidget for dashboard
- Activity logging system
- HMAC SHA256 authentication
- Comprehensive documentation

**Status**: Core Foundation Complete ✅

---

**Created**: 2025-11-18
**Author**: KhanzaWeb Development Team
**Version**: 1.0
**License**: Private - For Internal Use Only
