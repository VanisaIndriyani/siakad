<?php

namespace App\Support;

use App\Models\Khs;
use App\Models\KhsItem;
use App\Models\Krs;
use App\Models\Mahasiswa;

class QuestionnaireService
{
    public const SCORE_LABELS = [
        1 => 'Kurang',
        2 => 'Cukup',
        3 => 'Baik',
        4 => 'Sangat Baik',
    ];

    public static function ensureKhsItemsFromApprovedKrs(Mahasiswa $mahasiswa): void
    {
        $krsList = Krs::query()
            ->with('items')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status_approval', 'approved')
            ->get();

        foreach ($krsList as $krs) {
            $khs = Khs::query()->firstOrCreate(
                [
                    'mahasiswa_id' => $mahasiswa->id,
                    'semester' => $krs->semester,
                ],
                [
                    'tahun_ajaran' => $krs->tahun_ajaran ?: null,
                ]
            );

            if (! $khs->tahun_ajaran && $krs->tahun_ajaran) {
                $khs->update(['tahun_ajaran' => $krs->tahun_ajaran]);
            }

            foreach ($krs->items as $item) {
                KhsItem::query()->firstOrCreate([
                    'khs_id' => $khs->id,
                    'mata_kuliah_id' => (int) $item->mata_kuliah_id,
                ]);
            }
        }
    }

    public static function pendingItemsQuery(Mahasiswa $mahasiswa)
    {
        return KhsItem::query()
            ->select('khs_items.*')
            ->join('khs', 'khs.id', '=', 'khs_items.khs_id')
            ->where('khs.mahasiswa_id', $mahasiswa->id)
            ->whereExists(function ($query) use ($mahasiswa) {
                $query->selectRaw('1')
                    ->from('krs_items')
                    ->join('krs', 'krs.id', '=', 'krs_items.krs_id')
                    ->where('krs.mahasiswa_id', $mahasiswa->id)
                    ->where('krs.status_approval', 'approved')
                    ->whereColumn('krs.semester', 'khs.semester')
                    ->whereColumn('krs_items.mata_kuliah_id', 'khs_items.mata_kuliah_id');
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
        static::ensureKhsItemsFromApprovedKrs($mahasiswa);

        return static::pendingItemsQuery($mahasiswa)->get();
    }

    public static function pendingCount(Mahasiswa $mahasiswa): int
    {
        static::ensureKhsItemsFromApprovedKrs($mahasiswa);

        return static::pendingItemsQuery($mahasiswa)->count();
    }

    public static function hasPendingForMahasiswa(Mahasiswa $mahasiswa): bool
    {
        static::ensureKhsItemsFromApprovedKrs($mahasiswa);

        return static::pendingItemsQuery($mahasiswa)->exists();
    }

    public static function hasPendingForKhs(Khs $khs, int $mahasiswaId): bool
    {
        return KhsItem::query()
            ->where('khs_id', $khs->id)
            ->whereExists(function ($query) use ($mahasiswaId) {
                $query->selectRaw('1')
                    ->from('krs_items')
                    ->join('krs', 'krs.id', '=', 'krs_items.krs_id')
                    ->where('krs.mahasiswa_id', $mahasiswaId)
                    ->where('krs.status_approval', 'approved')
                    ->whereColumn('krs.semester', 'khs.semester')
                    ->whereColumn('krs_items.mata_kuliah_id', 'khs_items.mata_kuliah_id');
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
