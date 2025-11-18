<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemeriksaan - {{ $pemeriksaan->regPeriksa->pasien->nm_pasien ?? '' }}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 20pt;
            color: #2c3e50;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin: 3px 0;
            font-size: 10pt;
            color: #555;
        }

        .report-title {
            text-align: center;
            margin: 20px 0 30px 0;
        }

        .report-title h2 {
            margin: 0;
            font-size: 16pt;
            color: #2c3e50;
            text-decoration: underline;
        }

        .info-table {
            width: 100%;
            margin-bottom: 25px;
        }

        .info-table tr td:first-child {
            width: 180px;
            font-weight: 600;
            padding: 5px 10px 5px 0;
            vertical-align: top;
        }

        .info-table tr td:nth-child(2) {
            width: 20px;
            vertical-align: top;
        }

        .info-table tr td:last-child {
            vertical-align: top;
        }

        .section {
            margin: 25px 0;
            page-break-inside: avoid;
        }

        .section-title {
            background: #2c3e50;
            color: white;
            padding: 8px 15px;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .vital-signs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 15px 0;
        }

        .vital-box {
            border: 2px solid #ecf0f1;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .vital-label {
            font-size: 9pt;
            color: #777;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .vital-value {
            font-size: 18pt;
            font-weight: bold;
            color: #2c3e50;
        }

        .vital-unit {
            font-size: 10pt;
            color: #555;
            margin-left: 5px;
        }

        .soap-box {
            border: 2px solid #3498db;
            padding: 15px;
            margin: 10px 0;
            background: #fff;
            min-height: 80px;
        }

        .soap-header {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 11pt;
            border-bottom: 1px solid #ecf0f1;
            padding-bottom: 5px;
        }

        .soap-content {
            color: #333;
            white-space: pre-wrap;
            line-height: 1.8;
        }

        .diagnosis-list {
            margin: 15px 0;
        }

        .diagnosis-item {
            padding: 10px 15px;
            margin: 8px 0;
            border-left: 4px solid #e74c3c;
            background: #fff5f5;
        }

        .diagnosis-primary {
            border-left-color: #e74c3c;
            background: #fff5f5;
        }

        .diagnosis-secondary {
            border-left-color: #f39c12;
            background: #fffbf0;
        }

        .diagnosis-code {
            font-weight: bold;
            color: #e74c3c;
            font-size: 10pt;
        }

        .diagnosis-name {
            font-weight: 600;
            color: #2c3e50;
            margin: 3px 0;
        }

        .diagnosis-status {
            font-size: 9pt;
            color: #777;
        }

        .medication-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .medication-table th {
            background: #34495e;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 10pt;
        }

        .medication-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ecf0f1;
        }

        .medication-table tr:hover {
            background: #f8f9fa;
        }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 70px;
            padding-top: 5px;
            font-weight: 600;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px dashed #ccc;
            font-size: 9pt;
            color: #777;
            text-align: center;
        }

        .stamp-box {
            border: 2px dashed #999;
            width: 150px;
            height: 150px;
            display: inline-block;
            margin-top: 10px;
        }

        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'KLINIK PRATAMA') }}</h1>
        <p>{{ config('app.clinic_address', 'Alamat Klinik') }}</p>
        <p>Telp: {{ config('app.clinic_phone', '-') }} | Email: {{ config('app.clinic_email', '-') }}</p>
    </div>

    <div class="report-title">
        <h2>LAPORAN HASIL PEMERIKSAAN MEDIS</h2>
    </div>

    <table class="info-table">
        <tr>
            <td>No. Rekam Medis</td>
            <td>:</td>
            <td><strong>{{ $pemeriksaan->regPeriksa->no_rkm_medis ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td>Nama Pasien</td>
            <td>:</td>
            <td><strong>{{ $pemeriksaan->regPeriksa->pasien->nm_pasien ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td>Tanggal Lahir / Umur</td>
            <td>:</td>
            <td>{{ $pemeriksaan->regPeriksa->pasien->tgl_lahir ? \Carbon\Carbon::parse($pemeriksaan->regPeriksa->pasien->tgl_lahir)->format('d F Y') : '-' }} / {{ $pemeriksaan->regPeriksa->pasien->umur ?? '-' }} tahun</td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td>{{ $pemeriksaan->regPeriksa->pasien->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td>{{ $pemeriksaan->regPeriksa->pasien->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td>No. Rawat</td>
            <td>:</td>
            <td>{{ $pemeriksaan->no_rawat }}</td>
        </tr>
        <tr>
            <td>Tanggal Pemeriksaan</td>
            <td>:</td>
            <td><strong>{{ \Carbon\Carbon::parse($pemeriksaan->tgl_perawatan)->format('d F Y') }}</strong> pukul {{ $pemeriksaan->jam_rawat }}</td>
        </tr>
        <tr>
            <td>Poliklinik</td>
            <td>:</td>
            <td>{{ $pemeriksaan->regPeriksa->poliklinik->nm_poli ?? '-' }}</td>
        </tr>
        <tr>
            <td>Dokter Pemeriksa</td>
            <td>:</td>
            <td><strong>{{ $pemeriksaan->regPeriksa->dokter->nm_dokter ?? '-' }}</strong></td>
        </tr>
    </table>

    <div class="section">
        <div class="section-title">TANDA-TANDA VITAL</div>
        <div class="vital-signs">
            @if($pemeriksaan->suhu_tubuh)
            <div class="vital-box">
                <div class="vital-label">Suhu Tubuh</div>
                <div class="vital-value">{{ $pemeriksaan->suhu_tubuh }}<span class="vital-unit">°C</span></div>
            </div>
            @endif

            @if($pemeriksaan->tensi)
            <div class="vital-box">
                <div class="vital-label">Tekanan Darah</div>
                <div class="vital-value">{{ $pemeriksaan->tensi }}<span class="vital-unit">mmHg</span></div>
            </div>
            @endif

            @if($pemeriksaan->nadi)
            <div class="vital-box">
                <div class="vital-label">Nadi</div>
                <div class="vital-value">{{ $pemeriksaan->nadi }}<span class="vital-unit">x/menit</span></div>
            </div>
            @endif

            @if($pemeriksaan->respirasi)
            <div class="vital-box">
                <div class="vital-label">Respirasi</div>
                <div class="vital-value">{{ $pemeriksaan->respirasi }}<span class="vital-unit">x/menit</span></div>
            </div>
            @endif

            @if($pemeriksaan->tinggi)
            <div class="vital-box">
                <div class="vital-label">Tinggi Badan</div>
                <div class="vital-value">{{ $pemeriksaan->tinggi }}<span class="vital-unit">cm</span></div>
            </div>
            @endif

            @if($pemeriksaan->berat)
            <div class="vital-box">
                <div class="vital-label">Berat Badan</div>
                <div class="vital-value">{{ $pemeriksaan->berat }}<span class="vital-unit">kg</span></div>
            </div>
            @endif

            @if($pemeriksaan->bmi)
            <div class="vital-box">
                <div class="vital-label">BMI</div>
                <div class="vital-value">{{ number_format($pemeriksaan->bmi, 1) }}<span class="vital-unit">{{ $pemeriksaan->bmi_category }}</span></div>
            </div>
            @endif

            @if($pemeriksaan->spo2)
            <div class="vital-box">
                <div class="vital-label">SpO₂</div>
                <div class="vital-value">{{ $pemeriksaan->spo2 }}<span class="vital-unit">%</span></div>
            </div>
            @endif

            @if($pemeriksaan->gcs)
            <div class="vital-box">
                <div class="vital-label">GCS</div>
                <div class="vital-value">{{ $pemeriksaan->gcs }}</div>
            </div>
            @endif

            @if($pemeriksaan->kesadaran)
            <div class="vital-box">
                <div class="vital-label">Kesadaran</div>
                <div class="vital-value" style="font-size: 14pt;">{{ $pemeriksaan->kesadaran }}</div>
            </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">CATATAN MEDIS (SOAP)</div>

        <div class="soap-box">
            <div class="soap-header">S - SUBJECTIVE (Keluhan Pasien)</div>
            <div class="soap-content">{{ $pemeriksaan->keluhan ?: 'Tidak ada keluhan yang dicatat' }}</div>
        </div>

        <div class="soap-box">
            <div class="soap-header">O - OBJECTIVE (Hasil Pemeriksaan)</div>
            <div class="soap-content">{{ $pemeriksaan->pemeriksaan ?: 'Tidak ada hasil pemeriksaan yang dicatat' }}</div>
        </div>

        <div class="soap-box">
            <div class="soap-header">A - ASSESSMENT (Penilaian/Diagnosis)</div>
            <div class="soap-content">{{ $pemeriksaan->penilaian ?: 'Tidak ada penilaian yang dicatat' }}</div>
        </div>

        <div class="soap-box">
            <div class="soap-header">P - PLAN (Rencana Tindak Lanjut)</div>
            <div class="soap-content">{{ $pemeriksaan->rtl ?: 'Tidak ada rencana yang dicatat' }}</div>
        </div>
    </div>

    @if($pemeriksaan->regPeriksa->diagnosaPasien && $pemeriksaan->regPeriksa->diagnosaPasien->count() > 0)
    <div class="section">
        <div class="section-title">DIAGNOSIS (ICD-10)</div>
        <div class="diagnosis-list">
            @foreach($pemeriksaan->regPeriksa->diagnosaPasien as $diagnosis)
            <div class="diagnosis-item {{ $diagnosis->prioritas === '1' ? 'diagnosis-primary' : 'diagnosis-secondary' }}">
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <span class="diagnosis-code">{{ $diagnosis->kd_penyakit }}</span>
                        <div class="diagnosis-name">{{ $diagnosis->penyakit->nm_penyakit ?? 'Unknown' }}</div>
                        <div class="diagnosis-status">
                            {{ $diagnosis->priority_label }} | {{ $diagnosis->status_penyakit }} | {{ $diagnosis->status }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($pemeriksaan->regPeriksa->detailPemberianObat && $pemeriksaan->regPeriksa->detailPemberianObat->count() > 0)
    <div class="section">
        <div class="section-title">OBAT YANG DIBERIKAN</div>
        <table class="medication-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Nama Obat</th>
                    <th style="width: 10%;">Jumlah</th>
                    <th style="width: 15%;">Dosis</th>
                    <th style="width: 15%;">Frekuensi</th>
                    <th style="width: 20%;">Aturan Pakai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pemeriksaan->regPeriksa->detailPemberianObat as $index => $obat)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td><strong>{{ $obat->databarang->nama_brng ?? 'Unknown' }}</strong></td>
                    <td>{{ number_format($obat->jml, 0) }} {{ $obat->databarang->kodesatuan->satuan ?? '' }}</td>
                    <td>{{ $obat->dosis ?: '-' }}</td>
                    <td>{{ $obat->frekuensi ?: '-' }}</td>
                    <td>{{ $obat->aturan_pakai ?: '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <p>Pasien/Wali,</p>
            <div class="signature-line">
                {{ $pemeriksaan->regPeriksa->pasien->nm_pasien ?? '-' }}
            </div>
        </div>

        <div class="signature-box">
            <p>{{ \Carbon\Carbon::parse($pemeriksaan->tgl_perawatan)->format('d F Y') }}</p>
            <p>Dokter Pemeriksa,</p>
            <div class="stamp-box"></div>
            <div class="signature-line">
                {{ $pemeriksaan->regPeriksa->dokter->nm_dokter ?? '-' }}
            </div>
        </div>
    </div>

    <div class="footer">
        <p><strong>Catatan:</strong> Laporan ini dibuat secara elektronik dan merupakan dokumen resmi dari {{ config('app.name', 'Klinik') }}.</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }}</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
