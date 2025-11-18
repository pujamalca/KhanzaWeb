<?php

return [

    /*
    |--------------------------------------------------------------------------
    | BPJS API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for BPJS VClaim and PCare API integration
    | Based on SIMRS Khanza desktop implementation
    |
    */

    'enabled' => env('BPJS_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | VClaim Configuration (untuk SEP)
    |--------------------------------------------------------------------------
    */
    'vclaim' => [
        'base_url' => env('BPJS_VCLAIM_URL', 'https://apijkn-dev.bpjs-kesehatan.go.id/vclaim-rest-dev'),
        'cons_id' => env('BPJS_CONS_ID', ''),
        'secret_key' => env('BPJS_SECRET_KEY', ''),
        'user_key' => env('BPJS_USER_KEY', ''),

        'endpoints' => [
            'sep' => [
                'insert' => '/SEP/2.0/insert',
                'update' => '/SEP/2.0/update',
                'delete' => '/SEP/2.0/delete',
                'detail' => '/SEP/2.0/',
                'pengajuan' => '/SEP/2.0/pengajuan/',
                'approval' => '/SEP/2.0/approval/',
                'internal' => '/SEP/2.0/internal/',
            ],
            'peserta' => [
                'by_no_kartu' => '/Peserta/',
                'by_nik' => '/Peserta/nik/',
            ],
            'rujukan' => [
                'rs' => '/Rujukan/RS/',
                'faskes' => '/Rujukan/',
                'multi_sep' => '/Rujukan/MultiSep/',
            ],
            'monitoring' => [
                'klaim' => '/Monitoring/Klaim/Tanggal/',
                'history' => '/Monitoring/HistoriPelayanan/Peserta/',
            ],
            'referensi' => [
                'diagnosa' => '/referensi/diagnosa/',
                'poli' => '/referensi/poli/',
                'faskes' => '/referensi/faskes/',
                'propinsi' => '/referensi/propinsi',
                'kabupaten' => '/referensi/kabupaten/propinsi/',
                'kecamatan' => '/referensi/kecamatan/kabupaten/',
                'dokter' => '/referensi/dokter/',
                'procedure' => '/referensi/procedure/',
                'kelas_rawat' => '/referensi/kelasrawat',
                'dpjp' => '/referensi/dokter/pelayanan/',
                'spesialistik' => '/referensi/spesialistik',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | PCare Configuration (untuk FKTP)
    |--------------------------------------------------------------------------
    */
    'pcare' => [
        'base_url' => env('BPJS_PCARE_URL', 'https://apijkn-dev.bpjs-kesehatan.go.id/pcare-rest-dev'),
        'username' => env('BPJS_PCARE_USERNAME', ''),
        'password' => env('BPJS_PCARE_PASSWORD', ''),
        'app_code' => env('BPJS_PCARE_APP_CODE', '095'),
        'user_key' => env('BPJS_PCARE_USER_KEY', ''),

        'endpoints' => [
            'peserta' => '/peserta/',
            'pendaftaran' => [
                'list' => '/pendaftaran',
                'detail' => '/pendaftaran/',
                'insert' => '/pendaftaran',
                'delete' => '/pendaftaran/',
            ],
            'kunjungan' => [
                'list' => '/kunjungan',
                'detail' => '/kunjungan/',
                'insert' => '/kunjungan',
                'update' => '/kunjungan',
            ],
            'tindakan' => [
                'list' => '/tindakan',
                'insert' => '/tindakan',
            ],
            'obat' => [
                'list' => '/obat',
            ],
            'diagnosa' => [
                'list' => '/diagnosa',
            ],
            'rujukan' => [
                'list' => '/rujukan',
                'insert' => '/rujukan',
            ],
            'provider' => '/provider/',
            'status_pulang' => '/statuspulang',
            'spesialis' => '/spesialis',
            'subspesialis' => '/spesialis/{kode}/subspesialis',
            'sarana' => '/sarana',
            'khusus' => '/khusus',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | BPJS Facility Configuration
    |--------------------------------------------------------------------------
    */
    'facility' => [
        'kode_ppk' => env('BPJS_KODE_PPK', ''),
        'nama_ppk' => env('BPJS_NAMA_PPK', 'KLINIK PRATAMA'),
        'jenis_faskes' => env('BPJS_JENIS_FASKES', '1'), // 1=Faskes Primer, 2=RS
    ],

    /*
    |--------------------------------------------------------------------------
    | SEP Configuration
    |--------------------------------------------------------------------------
    */
    'sep' => [
        'user_sep' => env('BPJS_USER_SEP', 'admin'),
        'request_sep' => env('BPJS_REQUEST_SEP', '0'), // 0=tidak wajib, 1=wajib
        'auto_create_sep' => env('BPJS_AUTO_CREATE_SEP', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Other Settings
    |--------------------------------------------------------------------------
    */
    'settings' => [
        'timeout' => env('BPJS_TIMEOUT', 30), // seconds
        'log_enabled' => env('BPJS_LOG_ENABLED', true),
        'log_path' => storage_path('logs/bpjs.log'),
    ],

];
