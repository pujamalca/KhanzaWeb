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

 public static function applyEloquentQuery(Builder $query, string $table): Builder
{
    $user = auth()->user();

    return $query->when(auth()->check(), function ($query) use ($user, $table) {
        if (!$user->can("view_{$table}")) {
            if ($table === 'reg_periksa') {
                return $query->where('kd_dokter', $user->username);
            } elseif ($table === 'berkas_pegawai') {
                return $query->where('nik', $user->username);
            }
        }
        return $query;
    });
}
protected function applyFiltersToQuery(Builder $query): Builder
{
    // Terapkan filter berdasarkan peran user
    $query = \App\Traits\AppliesUserFilter::applyEloquentQuery($query, (new static::$model())->getTable());

    // Terapkan filter tanggal dari DateRangeFilter
    $startDate = request()->input('tableFilters.Tanggal Mulai', now()->toDateString());
    $endDate = request()->input('tableFilters.Tanggal Akhir', now()->toDateString());

    $query->whereBetween('tgl_registrasi', [$startDate, $endDate]);

    return $query;
}


}
