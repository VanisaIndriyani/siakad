<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\AbsensiItem;
use App\Models\Dosen;
use App\Models\Krs;
use App\Models\KrsItem;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dapatkan atau buat Admin untuk created_by_user_id
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin System',
                'email' => 'admin_test@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }

        // 2. Dapatkan atau buat Dosen
        $dosen = Dosen::first();
        if (!$dosen) {
            $userDosen = User::create([
                'name' => 'Dosen Seeder',
                'email' => 'dosenseeder@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'dosen',
            ]);
            $dosen = Dosen::create([
                'user_id' => $userDosen->id,
                'nama' => 'Dosen Seeder',
                'nidn' => '0000000001',
                'nik' => '1234567890',
            ]);
        }

        // 3. Dapatkan atau buat Mata Kuliah PAI102
        $mk = MataKuliah::where('kode', 'PAI102')->first();
        if (!$mk) {
            $mk = MataKuliah::create([
                'kode' => 'PAI102',
                'nama' => 'Pendidikan Agama Islam Dasar',
                'jurusan' => 'Pendidikan Agama Islam',
                'sks' => 3,
                'semester' => 1,
                'dosen_id' => $dosen->id,
            ]);
        }

        // 4. Hapus data absensi lama untuk MK ini agar tidak duplikat (Unique Constraint)
        $existingAbsensiIds = Absensi::where('mata_kuliah_id', $mk->id)->pluck('id');
        AbsensiItem::whereIn('absensi_id', $existingAbsensiIds)->delete();
        Absensi::where('mata_kuliah_id', $mk->id)->delete();

        // 5. Pastikan ada minimal 10 Mahasiswa
        $mahasiswas = Mahasiswa::take(10)->get();
        if ($mahasiswas->count() < 10) {
            for ($i = $mahasiswas->count() + 1; $i <= 10; $i++) {
                $userMhs = User::create([
                    'name' => 'Mahasiswa ' . $i,
                    'email' => 'mhs' . $i . '@test.com',
                    'password' => Hash::make('password'),
                    'role' => 'mahasiswa',
                ]);
                Mahasiswa::create([
                    'user_id' => $userMhs->id,
                    'npm' => '2026' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'nama_lengkap' => 'Mahasiswa ' . $i,
                    'angkatan' => 2026,
                    'program_studi' => 'Pendidikan Agama Islam',
                ]);
            }
            $mahasiswas = Mahasiswa::take(10)->get();
        }

        // 6. Pastikan Mahasiswa memiliki KRS Approved untuk MK ini
        foreach ($mahasiswas as $mhs) {
            $krs = Krs::firstOrCreate(
                [
                    'mahasiswa_id' => $mhs->id,
                    'semester' => $mk->semester,
                ],
                [
                    'tahun_ajaran' => '2026/2027',
                    'status_approval' => 'approved',
                    'approved_by_dosen_id' => $dosen->id,
                ]
            );

            KrsItem::firstOrCreate([
                'krs_id' => $krs->id,
                'mata_kuliah_id' => $mk->id,
            ]);
        }

        // 7. Buat 16 Pertemuan Absensi
        $statuses = ['hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpha']; // Lebih banyak hadir agar rekap terlihat bagus
        
        for ($pertemuan = 1; $pertemuan <= 16; $pertemuan++) {
            $absensi = Absensi::create([
                'jurusan' => $mk->jurusan,
                'semester' => $mk->semester,
                'mata_kuliah_id' => $mk->id,
                'pertemuan' => $pertemuan,
                'tanggal' => now()->subDays(16 - $pertemuan),
                'materi' => 'Materi Perkuliahan Pertemuan Ke-' . $pertemuan,
                'created_by_user_id' => $admin->id,
            ]);

            // 7. Buat AbsensiItem untuk setiap Mahasiswa
            foreach ($mahasiswas as $mhs) {
                AbsensiItem::create([
                    'absensi_id' => $absensi->id,
                    'mahasiswa_id' => $mhs->id,
                    'status' => $statuses[array_rand($statuses)],
                    'keterangan' => $pertemuan % 5 == 0 ? 'Catatan pertemuan ' . $pertemuan : null,
                ]);
            }
        }

        $this->command->info('Data Absensi PAI102 (16 Pertemuan, 10 Mahasiswa) berhasil dibuat.');
    }
}
