<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Form --}}
        <x-filament::card>
            <form wire:submit.prevent="$refresh">
                {{ $this->form }}

                <div class="mt-4">
                    <x-filament::button type="submit" wire:click="$refresh">
                        Generate Laporan
                    </x-filament::button>
                </div>
            </form>
        </x-filament::card>

        @php
            $reportData = $this->getReportData();
        @endphp

        {{-- Patient Statistics --}}
        <x-filament::card>
            <h2 class="text-xl font-bold mb-4">Statistik Kunjungan Pasien</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Registrasi</div>
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($reportData['total_registrations']) }}</div>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Pasien Baru</div>
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($reportData['new_patients']) }}</div>
                </div>
                <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Pasien Lama</div>
                    <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($reportData['old_patients']) }}</div>
                </div>
            </div>
        </x-filament::card>

        {{-- Examination Statistics --}}
        <x-filament::card>
            <h2 class="text-xl font-bold mb-4">Statistik Pemeriksaan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Pemeriksaan</div>
                    <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ number_format($reportData['total_examinations']) }}</div>
                </div>
                <div class="p-4 bg-teal-50 dark:bg-teal-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">SOAP Lengkap</div>
                    <div class="text-3xl font-bold text-teal-600 dark:text-teal-400">{{ number_format($reportData['complete_soap']) }}</div>
                </div>
            </div>
        </x-filament::card>

        {{-- Diagnosis Statistics --}}
        <x-filament::card>
            <h2 class="text-xl font-bold mb-4">Statistik Diagnosis</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Diagnosis</div>
                    <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ number_format($reportData['total_diagnoses']) }}</div>
                </div>
                <div class="p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Diagnosis Utama</div>
                    <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($reportData['primary_diagnoses']) }}</div>
                </div>
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Kasus Baru</div>
                    <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($reportData['new_cases']) }}</div>
                </div>
            </div>
        </x-filament::card>

        {{-- Prescription Statistics --}}
        <x-filament::card>
            <h2 class="text-xl font-bold mb-4">Statistik Resep Obat</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-pink-50 dark:bg-pink-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Item Resep</div>
                    <div class="text-3xl font-bold text-pink-600 dark:text-pink-400">{{ number_format($reportData['total_prescriptions']) }}</div>
                </div>
                <div class="p-4 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Quantity Obat</div>
                    <div class="text-3xl font-bold text-cyan-600 dark:text-cyan-400">{{ number_format($reportData['total_medication_qty']) }}</div>
                </div>
            </div>
        </x-filament::card>

        {{-- Financial Statistics --}}
        <x-filament::card>
            <h2 class="text-xl font-bold mb-4">Statistik Keuangan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Total Pendapatan</div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($reportData['total_revenue'], 0, ',', '.') }}</div>
                </div>
                <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Pembayaran Tunai</div>
                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($reportData['cash_payments'], 0, ',', '.') }}</div>
                </div>
                <div class="p-4 bg-sky-50 dark:bg-sky-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Pembayaran Non-Tunai</div>
                    <div class="text-2xl font-bold text-sky-600 dark:text-sky-400">Rp {{ number_format($reportData['non_cash_payments'], 0, ',', '.') }}</div>
                </div>
                <div class="p-4 bg-rose-50 dark:bg-rose-900/20 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Piutang Outstanding</div>
                    <div class="text-2xl font-bold text-rose-600 dark:text-rose-400">Rp {{ number_format($reportData['outstanding_balance'], 0, ',', '.') }}</div>
                </div>
            </div>
        </x-filament::card>

        {{-- Top Diagnoses --}}
        <x-filament::card>
            <h2 class="text-xl font-bold mb-4">Top 10 Diagnosis</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">No</th>
                            <th class="px-4 py-2 text-left">Kode ICD-10</th>
                            <th class="px-4 py-2 text-left">Nama Penyakit</th>
                            <th class="px-4 py-2 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData['top_diagnoses'] as $index => $diagnosis)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 font-mono">{{ $diagnosis->kd_penyakit }}</td>
                            <td class="px-4 py-2">{{ $diagnosis->penyakit->nm_penyakit ?? 'Unknown' }}</td>
                            <td class="px-4 py-2 text-right font-bold">{{ number_format($diagnosis->total) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::card>

        {{-- Top Medications --}}
        <x-filament::card>
            <h2 class="text-xl font-bold mb-4">Top 10 Obat Terlaris</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">No</th>
                            <th class="px-4 py-2 text-left">Kode</th>
                            <th class="px-4 py-2 text-left">Nama Obat</th>
                            <th class="px-4 py-2 text-right">Total Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData['top_medications'] as $index => $medication)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-2">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 font-mono">{{ $medication->kode_brng }}</td>
                            <td class="px-4 py-2">{{ $medication->databarang->nama_brng ?? 'Unknown' }}</td>
                            <td class="px-4 py-2 text-right font-bold">{{ number_format($medication->total_qty) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page>
