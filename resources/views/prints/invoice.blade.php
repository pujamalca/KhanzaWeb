<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $billing->no_rawat }}</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .clinic-info h1 {
            margin: 0;
            font-size: 20pt;
            color: #2c3e50;
            font-weight: bold;
        }

        .clinic-info p {
            margin: 3px 0;
            font-size: 9pt;
            color: #555;
        }

        .invoice-badge {
            text-align: right;
        }

        .invoice-badge h2 {
            margin: 0;
            font-size: 24pt;
            color: #e74c3c;
            font-weight: bold;
        }

        .invoice-badge p {
            margin: 5px 0;
            font-size: 10pt;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .detail-box {
            width: 48%;
        }

        .detail-box h3 {
            margin: 0 0 10px 0;
            font-size: 11pt;
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 5px;
        }

        .detail-row {
            display: flex;
            margin: 5px 0;
        }

        .detail-label {
            width: 110px;
            font-weight: 600;
            color: #555;
        }

        .detail-value {
            flex: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        thead {
            background: #2c3e50;
            color: white;
        }

        thead th {
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 10pt;
        }

        tbody td {
            padding: 10px 8px;
            border-bottom: 1px solid #ecf0f1;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-section {
            float: right;
            width: 350px;
            margin-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 15px;
            border-bottom: 1px solid #ecf0f1;
        }

        .total-row.subtotal {
            font-size: 11pt;
        }

        .total-row.grand-total {
            background: #2c3e50;
            color: white;
            font-size: 14pt;
            font-weight: bold;
            margin-top: 5px;
        }

        .total-row.paid {
            background: #27ae60;
            color: white;
            font-weight: 600;
        }

        .total-row.remaining {
            background: #e74c3c;
            color: white;
            font-weight: 600;
            font-size: 13pt;
        }

        .payment-history {
            clear: both;
            margin-top: 100px;
            page-break-inside: avoid;
        }

        .payment-history h3 {
            margin: 0 0 15px 0;
            color: #2c3e50;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 5px;
        }

        .payment-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 10px;
            background: #f8f9fa;
            margin-bottom: 5px;
            border-left: 3px solid #27ae60;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 10pt;
            font-weight: 600;
        }

        .status-lunas {
            background: #27ae60;
            color: white;
        }

        .status-cicilan {
            background: #f39c12;
            color: white;
        }

        .status-belum {
            background: #e74c3c;
            color: white;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ecf0f1;
            font-size: 9pt;
            color: #777;
            text-align: center;
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
        <div class="clinic-info">
            <h1>{{ config('app.name', 'KLINIK PRATAMA') }}</h1>
            <p>{{ config('app.clinic_address', 'Alamat Klinik') }}</p>
            <p>Telp: {{ config('app.clinic_phone', '-') }}</p>
            <p>Email: {{ config('app.clinic_email', '-') }}</p>
        </div>
        <div class="invoice-badge">
            <h2>INVOICE</h2>
            <p><strong>No:</strong> #{{ str_pad($billing->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($billing->tgl_billing)->format('d/m/Y') }}</p>
        </div>
    </div>

    <div class="invoice-details">
        <div class="detail-box">
            <h3>Informasi Pasien</h3>
            <div class="detail-row">
                <span class="detail-label">No. RM</span>
                <span class="detail-value">: {{ $billing->regPeriksa->no_rkm_medis ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Nama</span>
                <span class="detail-value">: <strong>{{ $billing->regPeriksa->pasien->nm_pasien ?? '-' }}</strong></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Alamat</span>
                <span class="detail-value">: {{ $billing->regPeriksa->pasien->alamat ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">No. Telp</span>
                <span class="detail-value">: {{ $billing->regPeriksa->pasien->no_tlp ?? '-' }}</span>
            </div>
        </div>

        <div class="detail-box">
            <h3>Detail Layanan</h3>
            <div class="detail-row">
                <span class="detail-label">No. Rawat</span>
                <span class="detail-value">: {{ $billing->no_rawat }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tanggal Rawat</span>
                <span class="detail-value">: {{ \Carbon\Carbon::parse($billing->regPeriksa->tgl_registrasi)->format('d F Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Poli</span>
                <span class="detail-value">: {{ $billing->regPeriksa->poliklinik->nm_poli ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Dokter</span>
                <span class="detail-value">: {{ $billing->regPeriksa->dokter->nm_dokter ?? '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value">:
                    <span class="status-badge status-{{
                        $billing->status_bayar === 'Lunas' ? 'lunas' :
                        ($billing->status_bayar === 'Cicilan' ? 'cicilan' : 'belum')
                    }}">
                        {{ $billing->status_bayar }}
                    </span>
                </span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 50%;">Deskripsi Layanan</th>
                <th style="width: 15%;" class="text-center">Qty</th>
                <th style="width: 15%;" class="text-right">Harga Satuan</th>
                <th style="width: 15%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp

            @if($billing->biaya_registrasi > 0)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>Biaya Registrasi</td>
                <td class="text-center">1</td>
                <td class="text-right">Rp {{ number_format($billing->biaya_registrasi, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($billing->biaya_registrasi, 0, ',', '.') }}</td>
            </tr>
            @endif

            @if($billing->biaya_pemeriksaan > 0)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>Biaya Pemeriksaan Dokter</td>
                <td class="text-center">1</td>
                <td class="text-right">Rp {{ number_format($billing->biaya_pemeriksaan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($billing->biaya_pemeriksaan, 0, ',', '.') }}</td>
            </tr>
            @endif

            @if($billing->biaya_obat > 0)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>
                    Biaya Obat & BHP
                    @if($billing->regPeriksa && $billing->regPeriksa->detailPemberianObat->count() > 0)
                    <div style="font-size: 9pt; color: #777; margin-top: 5px;">
                        @foreach($billing->regPeriksa->detailPemberianObat as $obat)
                        - {{ $obat->databarang->nama_brng ?? 'Unknown' }} ({{ number_format($obat->jml, 0) }})<br>
                        @endforeach
                    </div>
                    @endif
                </td>
                <td class="text-center">-</td>
                <td class="text-right">-</td>
                <td class="text-right">Rp {{ number_format($billing->biaya_obat, 0, ',', '.') }}</td>
            </tr>
            @endif

            @if($billing->biaya_tindakan > 0)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>Biaya Tindakan</td>
                <td class="text-center">1</td>
                <td class="text-right">Rp {{ number_format($billing->biaya_tindakan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($billing->biaya_tindakan, 0, ',', '.') }}</td>
            </tr>
            @endif

            @if($billing->biaya_laborat > 0)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>Biaya Laboratorium</td>
                <td class="text-center">1</td>
                <td class="text-right">Rp {{ number_format($billing->biaya_laborat, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($billing->biaya_laborat, 0, ',', '.') }}</td>
            </tr>
            @endif

            @if($billing->biaya_radiologi > 0)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>Biaya Radiologi</td>
                <td class="text-center">1</td>
                <td class="text-right">Rp {{ number_format($billing->biaya_radiologi, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($billing->biaya_radiologi, 0, ',', '.') }}</td>
            </tr>
            @endif

            @if($billing->biaya_lainnya > 0)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>
                    Biaya Lainnya
                    @if($billing->keterangan)
                    <div style="font-size: 9pt; color: #777;">{{ $billing->keterangan }}</div>
                    @endif
                </td>
                <td class="text-center">1</td>
                <td class="text-right">Rp {{ number_format($billing->biaya_lainnya, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($billing->biaya_lainnya, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="totals-section">
        <div class="total-row subtotal">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($billing->subtotal, 0, ',', '.') }}</span>
        </div>

        @if($billing->diskon > 0)
        <div class="total-row">
            <span>Diskon ({{ number_format($billing->diskon_persen, 1) }}%):</span>
            <span>- Rp {{ number_format($billing->diskon, 0, ',', '.') }}</span>
        </div>
        @endif

        @if($billing->pajak > 0)
        <div class="total-row">
            <span>Pajak ({{ number_format($billing->pajak_persen, 1) }}%):</span>
            <span>Rp {{ number_format($billing->pajak, 0, ',', '.') }}</span>
        </div>
        @endif

        <div class="total-row grand-total">
            <span>TOTAL TAGIHAN:</span>
            <span>Rp {{ number_format($billing->total_biaya, 0, ',', '.') }}</span>
        </div>

        @if($billing->pembayaran && $billing->pembayaran->count() > 0)
        <div class="total-row paid">
            <span>Total Dibayar:</span>
            <span>Rp {{ number_format($billing->total_bayar, 0, ',', '.') }}</span>
        </div>
        @endif

        @if($billing->sisa_tagihan > 0)
        <div class="total-row remaining">
            <span>SISA TAGIHAN:</span>
            <span>Rp {{ number_format($billing->sisa_tagihan, 0, ',', '.') }}</span>
        </div>
        @endif
    </div>

    @if($billing->pembayaran && $billing->pembayaran->count() > 0)
    <div class="payment-history">
        <h3>Riwayat Pembayaran</h3>
        @foreach($billing->pembayaran as $payment)
        <div class="payment-item">
            <div>
                <strong>{{ \Carbon\Carbon::parse($payment->tgl_bayar)->format('d/m/Y H:i') }}</strong>
                - {{ $payment->metode_bayar }}
                @if($payment->no_referensi)
                (Ref: {{ $payment->no_referensi }})
                @endif
            </div>
            <div style="font-weight: bold; color: #27ae60;">
                Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        <p>Terima kasih atas kepercayaan Anda menggunakan layanan kami.</p>
        <p>Invoice ini dicetak otomatis oleh sistem pada {{ \Carbon\Carbon::now()->format('d F Y H:i') }}</p>
        <p><em>Untuk informasi lebih lanjut, hubungi bagian kasir/administrasi.</em></p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
