<?php

return [
    'temporary_file_upload' => [
        'disk' => 'local', // Simpan file sementara di disk 'local'
        'rules' => ['image', 'max:2048'], // Maksimal 2MB
        'directory' => 'livewire-tmp',
        'middleware' => null, // Ubah dari 'auth' ke 'null' jika tidak ingin validasi middleware
    ],
];
