<?php

namespace App\Support;

use App\Models\Khs;
use App\Models\KhsItem;
use App\Models\Mahasiswa;

class QuestionnaireService
{
    public const SCORE_LABELS = [
        1 => 'Kurang',
        2 => 'Cukup',
        3 => 'Baik',
        4 => 'Sangat Baik',
    ];

    public static function pendingItemsQuery(Mahasiswa $mahasiswa)
    {
        return KhsItem::query()
            ->select('khs_items.*')
            ->join('khs', 'khs.id', '=', 'khs_items.khs_id')
            ->where('khs.mahasiswa_id', $mahasiswa->id)
            ->where(function ($query) {
                $query->whereNotNull('khs_items.nilai_huruf')
                    ->orWhereNotNull('khs_items.nilai_angka');
            })
            ->whereNotExists(function ($query) use ($mahasiswa) {
                $query->selectRaw('1')
                    ->from('questionnaire_responses')
                    ->where('questionnaire_responses.mahasiswa_id', $mahasiswa->id)
                    ->whereColumn('questionnaire_responses.khs_id', 'khs_items.khs_id')
                    ->whereColumn('questionnaire_responses.mata_kuliah_id', 'khs_items.mata_kuliah_id');
            })
            ->with(['khs', 'mataKuliah.dosen', 'mataKuliah.dosen2'])
            ->orderBy('khs.semester')
            ->orderBy('khs_items.id');
    }

    public static function pendingItems(Mahasiswa $mahasiswa)
    {
        return static::pendingItemsQuery($mahasiswa)->get();
    }

    public static function pendingCount(Mahasiswa $mahasiswa): int
    {
        return static::pendingItemsQuery($mahasiswa)->count();
    }

    public static function hasPendingForMahasiswa(Mahasiswa $mahasiswa): bool
    {
        return static::pendingItemsQuery($mahasiswa)->exists();
    }

    public static function hasPendingForKhs(Khs $khs, int $mahasiswaId): bool
    {
        return KhsItem::query()
            ->where('khs_id', $khs->id)
            ->where(function ($query) {
                $query->whereNotNull('nilai_huruf')
                    ->orWhereNotNull('nilai_angka');
            })
            ->whereNotExists(function ($query) use ($mahasiswaId) {
                $query->selectRaw('1')
                    ->from('questionnaire_responses')
                    ->where('questionnaire_responses.mahasiswa_id', $mahasiswaId)
                    ->whereColumn('questionnaire_responses.khs_id', 'khs_items.khs_id')
                    ->whereColumn('questionnaire_responses.mata_kuliah_id', 'khs_items.mata_kuliah_id');
            })
            ->exists();
    }
}
