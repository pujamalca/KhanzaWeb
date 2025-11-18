<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resep Obat - {{ $regPeriksa->pasien->nm_pasien ?? '' }}</title>
    <style>
        @page {
            size: A5;
            margin: 1cm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 18pt;
            color: #2c3e50;
            font-weight: bold;
        }

        .header p {
            margin: 3px 0;
            font-size: 10pt;
            color: #555;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            width: 120px;
            font-weight: bold;
            padding: 3px 0;
        }

        .info-value {
            display: table-cell;
            padding: 3px 0;
        }

        .rx-symbol {
            font-size: 24pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 20px 0 10px 0;
        }

        .medication-list {
            margin: 15px 0;
        }

        .medication-item {
            border-left: 3px solid #3498db;
            padding: 10px 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
            page-break-inside: avoid;
        }

        .med-name {
            font-weight: bold;
            font-size: 12pt;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .med-detail {
            margin: 3px 0;
            padding-left: 10px;
        }

        .med-detail-label {
            display: inline-block;
            width: 90px;
            font-weight: 600;
            color: #555;
        }

        .med-instructions {
            background: #fff;
            border: 1px dashed #ccc;
            padding: 8px;
            margin-top: 5px;
            font-style: italic;
            color: #555;
        }

        .footer {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            float: right;
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }

        .notes {
            clear: both;
            margin-top: 30px;
            padding: 10px;
            background: #fff3cd;
            border: 1px solid #ffc107;
            font-size: 9pt;
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

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">No. Rawat</div>
            <div class="info-value">: {{ $regPeriksa->no_rawat }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nama Pasien</div>
            <div class="info-value">: {{ $regPeriksa->pasien->nm_pasien ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">No. RM</div>
            <div class="info-value">: {{ $regPeriksa->no_rkm_medis }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Umur</div>
            <div class="info-value">: {{ $regPeriksa->pasien->umur ?? '-' }} tahun</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal</div>
            <div class="info-value">: {{ \Carbon\Carbon::parse($regPeriksa->tgl_registrasi)->format('d F Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Dokter</div>
            <div class="info-value">: {{ $regPeriksa->dokter->nm_dokter ?? '-' }}</div>
        </div>
    </div>

    <div class="rx-symbol">℞</div>

    <div class="medication-list">
        @forelse($medications as $index => $med)
        <div class="medication-item">
            <div class="med-name">{{ $index + 1 }}. {{ $med->databarang->nama_brng ?? 'Unknown' }}</div>

            <div class="med-detail">
                <span class="med-detail-label">Jumlah</span>: {{ number_format($med->jml, 0) }} {{ $med->databarang->kodesatuan->satuan ?? '' }}
            </div>

            @if($med->dosis)
            <div class="med-detail">
                <span class="med-detail-label">Dosis</span>: {{ $med->dosis }}
            </div>
            @endif

            @if($med->frekuensi)
            <div class="med-detail">
                <span class="med-detail-label">Frekuensi</span>: {{ $med->frekuensi }}
            </div>
            @endif

            @if($med->aturan_pakai)
            <div class="med-instructions">
                <strong>Aturan Pakai:</strong> {{ $med->aturan_pakai }}
            </div>
            @endif
        </div>
        @empty
        <p style="text-align: center; color: #999;">Tidak ada obat yang diresepkan</p>
        @endforelse
    </div>

    <div class="footer">
        <div class="signature-box">
            <p>{{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <p>Dokter Pemeriksa,</p>
            <div class="signature-line">
                {{ $regPeriksa->dokter->nm_dokter ?? '-' }}
            </div>
        </div>
    </div>

    <div class="notes">
        <strong>Catatan:</strong>
        <ul style="margin: 5px 0; padding-left: 20px;">
            <li>Gunakan obat sesuai petunjuk dokter</li>
            <li>Habiskan antibiotik yang diresepkan</li>
            <li>Simpan obat di tempat sejuk dan kering</li>
            <li>Jauhkan dari jangkauan anak-anak</li>
        </ul>
    </div>

    <script>
        // Auto-print when loaded
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
