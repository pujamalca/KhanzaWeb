<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class MultipleLoginAlert extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database']; // Simpan notifikasi ke database
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Peringatan Login!',
            'body' => 'Akun Anda telah login di perangkat lain. Jika ini bukan Anda, segera ubah password.',
        ];
    }
}
