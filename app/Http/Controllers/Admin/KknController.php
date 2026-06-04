<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\KknPengajuan;
use App\Models\KknPosko;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KknController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));

        $query = KknPengajuan::query()->with(['mahasiswa', 'posko']);

        if ($q !== '') {
            $query->whereHas('mahasiswa', function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $items = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admin.kkn.index', [
            'items' => $items,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function updateStatus(Request $request, KknPengajuan $kkn): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'catatan_admin' => ['nullable', 'string'],
        ]);

        $kkn->update([
            'status' => $validated['status'],
            'catatan_admin' => $validated['catatan_admin'] ?: null,
        ]);

        return back()->with('success', 'Status pendaftaran KKN diperbarui.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih data yang ingin dihapus.');
        }

        KknPengajuan::query()->whereIn('id', $ids)->delete();

        return back()->with('success', 'Data pendaftaran KKN berhasil dihapus secara massal.');
    }

    public function poskoIndex(): View
    {
        $poskos = KknPosko::query()->with(['pembimbingS', 'pengajuans'])->orderByDesc('id')->paginate(10);
        return view('admin.kkn.posko-index', [
            'poskos' => $poskos,
        ]);
    }

    public function poskoCreate(): View
    {
        $dosenList = Dosen::query()->orderBy('nama')->get();
        return view('admin.kkn.posko-create', [
            'dosenList' => $dosenList,
        ]);
    }

    public function poskoStore(Request $request): RedirectResponse
    {
        // Filter out empty values from dosen_ids before validation
        $dosenIds = array_filter($request->input('dosen_ids', []), fn($val) => !empty($val));
        $request->merge(['dosen_ids' => $dosenIds]);

        $validated = $request->validate([
            'nama_posko' => ['required', 'string', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'dosen_ids' => ['required', 'array', 'min:1', 'max:5'],
            'dosen_ids.*' => ['exists:dosen,id'],
            'nomor_sk' => ['nullable', 'string', 'max:255'],
            'sk_pembimbing_file' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
        ], [
            'dosen_ids.required' => 'Pilih minimal 1 dosen pembimbing.',
            'dosen_ids.min' => 'Pilih minimal 1 dosen pembimbing.',
        ]);

        $skPath = null;
        $skName = null;

        if ($request->hasFile('sk_pembimbing_file')) {
            $file = $request->file('sk_pembimbing_file');
            $skName = $file->getClientOriginalName();
            $skPath = $file->store('kkn/sk', 'public');
        }

        $posko = KknPosko::query()->create([
            'nama_posko' => $validated['nama_posko'],
            'lokasi' => $validated['lokasi'],
            'nomor_sk' => $validated['nomor_sk'],
            'sk_pembimbing_path' => $skPath,
            'sk_pembimbing_name' => $skName,
        ]);

        $posko->pembimbingS()->sync($validated['dosen_ids']);

        return redirect()->route('admin.kkn.posko.index')->with('success', 'Posko KKN berhasil dibuat.');
    }

    public function poskoShow(KknPosko $posko): View
    {
        $posko->load(['pembimbingS', 'pengajuans.mahasiswa', 'messages.sender', 'files.user']);
        $dosenList = Dosen::query()->orderBy('nama')->get();
        
        // Students who are approved but not yet in a posko
        $availableStudents = KknPengajuan::query()
            ->where('status', 'approved')
            ->whereNull('kkn_posko_id')
            ->with('mahasiswa')
            ->get();

        return view('admin.kkn.posko-show', [
            'posko' => $posko,
            'dosenList' => $dosenList,
            'availableStudents' => $availableStudents,
        ]);
    }

    public function poskoUpdate(Request $request, KknPosko $posko): RedirectResponse
    {
        // Filter out empty values from dosen_ids before validation
        $dosenIds = array_filter($request->input('dosen_ids', []), fn($val) => !empty($val));
        $request->merge(['dosen_ids' => $dosenIds]);

        $validated = $request->validate([
            'nama_posko' => ['required', 'string', 'max:255'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'dosen_ids' => ['required', 'array', 'min:1', 'max:5'],
            'dosen_ids.*' => ['exists:dosen,id'],
            'nomor_sk' => ['nullable', 'string', 'max:255'],
            'sk_pembimbing_file' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
        ], [
            'dosen_ids.required' => 'Pilih minimal 1 dosen pembimbing.',
            'dosen_ids.min' => 'Pilih minimal 1 dosen pembimbing.',
        ]);

        if ($request->hasFile('sk_pembimbing_file')) {
            if ($posko->sk_pembimbing_path) {
                Storage::disk('public')->delete($posko->sk_pembimbing_path);
            }
            $file = $request->file('sk_pembimbing_file');
            $posko->sk_pembimbing_name = $file->getClientOriginalName();
            $posko->sk_pembimbing_path = $file->store('kkn/sk', 'public');
        }

        $posko->update([
            'nama_posko' => $validated['nama_posko'],
            'lokasi' => $validated['lokasi'],
            'nomor_sk' => $validated['nomor_sk'],
        ]);

        $posko->pembimbingS()->sync($validated['dosen_ids']);

        return back()->with('success', 'Data posko berhasil diperbarui.');
    }

    public function assignStudent(Request $request, KknPosko $posko): RedirectResponse
    {
        $validated = $request->validate([
            'kkn_pengajuan_ids' => ['required', 'array'],
            'kkn_pengajuan_ids.*' => ['exists:kkn_pengajuans,id'],
        ]);

        KknPengajuan::query()
            ->whereIn('id', $validated['kkn_pengajuan_ids'])
            ->update(['kkn_posko_id' => $posko->id]);

        return back()->with('success', 'Mahasiswa berhasil ditambahkan ke posko.');
    }

    public function removeStudent(KknPengajuan $kkn): RedirectResponse
    {
        $kkn->update(['kkn_posko_id' => null]);
        return back()->with('success', 'Mahasiswa dikeluarkan dari posko.');
    }

    public function poskoDestroy(KknPosko $posko): RedirectResponse
    {
        if ($posko->sk_pembimbing_path) {
            Storage::disk('public')->delete($posko->sk_pembimbing_path);
        }
        
        // Members become unassigned
        $posko->pengajuans()->update(['kkn_posko_id' => null]);
        $posko->delete();

        return redirect()->route('admin.kkn.posko.index')->with('success', 'Posko berhasil dihapus.');
    }
}
