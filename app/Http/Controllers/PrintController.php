<?php

namespace App\Http\Controllers;

use App\Models\reg_periksa;
use App\Models\BillingPasien;
use App\Models\PemeriksaanRalan;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    /**
     * Print prescription (resep)
     */
    public function printResep($no_rawat)
    {
        $regPeriksa = reg_periksa::with([
            'pasien',
            'dokter',
            'detailPemberianObat.databarang.kodesatuan'
        ])->where('no_rawat', $no_rawat)->firstOrFail();

        $medications = $regPeriksa->detailPemberianObat;

        return view('prints.resep', compact('regPeriksa', 'medications'));
    }

    /**
     * Print invoice/billing
     */
    public function printInvoice($id)
    {
        $billing = BillingPasien::with([
            'regPeriksa.pasien',
            'regPeriksa.poliklinik',
            'regPeriksa.dokter',
            'regPeriksa.detailPemberianObat.databarang',
            'pembayaran.petugas'
        ])->findOrFail($id);

        return view('prints.invoice', compact('billing'));
    }

    /**
     * Print medical examination report
     */
    public function printMedicalReport($no_rawat)
    {
        $pemeriksaan = PemeriksaanRalan::with([
            'regPeriksa.pasien',
            'regPeriksa.poliklinik',
            'regPeriksa.dokter',
            'regPeriksa.diagnosaPasien.penyakit',
            'regPeriksa.detailPemberianObat.databarang.kodesatuan'
        ])->where('no_rawat', $no_rawat)->firstOrFail();

        return view('prints.medical-report', compact('pemeriksaan'));
    }
}
