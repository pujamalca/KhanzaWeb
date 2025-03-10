<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait AppliesUserFilter
{
    /**
     * Filter data hanya untuk user yang login, kecuali memiliki izin melihat semua.
     */
            /**
 * Filter data hanya untuk user yang login, kecuali memiliki izin melihat semua.
 */
    public static function applyEloquentQuery(Builder $query): Builder
        {
            return $query

                ->when(
                    auth()->check() && !auth()->user()->can('view_rawatjalan'),
                    fn ($query) => $query->where('kd_dokter', auth()->user()->username)
                )
                ->when(
                    auth()->check() && !auth()->user()->can('view_ugd'),
                    fn ($query) => $query->where('kd_dokter', auth()->user()->username)
                )
                ->when(
                    auth()->check() && !auth()->user()->can('view_master::berkas::pegawai'),
                    fn ($query) => $query->where('nik', auth()->user()->username)
                );
        }

}
