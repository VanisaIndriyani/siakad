<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\KknPengajuan;
use App\Models\KknPosko;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KknController extends Controller
{
    private const PRODI_APPROVER_STATUS = ['Ketua Prodi', 'Sekretaris Prodi'];

    private function resolveKaprodi(?string $programStudi): ?Dosen
    {
        $programStudi = trim((string) $programStudi);
        if ($programStudi === '') {
            return null;
        }

        return Dosen::query()
            ->where('program_studi', $programStudi)
            ->where('status_akademik', 'Ketua Prodi')
            ->orderByDesc('id')
            ->first();
    }

    private function resolveContext(Request $request): array
    {
        $user = $request->user();
        if ($user?->isAdmin()) {
            return ['routePrefix' => 'admin', 'canAssign' => true, 'programStudi' => null];
        }

        if ($user?->isDosen()) {
            $dosen = $user->dosen;
            $programStudi = trim((string) ($dosen?->program_studi ?? ''));
            $statusAkademik = (string) ($dosen?->status_akademik ?? '');

            $canAssign = in_array($statusAkademik, self::PRODI_APPROVER_STATUS, true);

            return ['routePrefix' => 'dosen', 'canAssign' => $canAssign, 'programStudi' => $programStudi ?: '---'];
        }

        abort(403);
    }

    public function index(Request $request): View
    {
        $context = $this->resolveContext($request);
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));

        $query = KknPengajuan::query()->with(['mahasiswa', 'posko']);

        if ($context['programStudi']) {
            $programStudi = $context['programStudi'];
            $query->whereHas('mahasiswa', function ($sub) use ($programStudi) {
                $sub->where('program_studi', $programStudi);
            });
        }

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
            'routePrefix' => $context['routePrefix'],
            'canAssign' => $context['canAssign'],
            'canManage' => $context['canAssign'],
        ]);
    }

    public function exportPdf(Request $request)
    {
        $context = $this->resolveContext($request);
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));

        $query = KknPengajuan::query()->with(['mahasiswa', 'posko']);

        if ($context['programStudi']) {
            $programStudi = $context['programStudi'];
            $query->whereHas('mahasiswa', function ($sub) use ($programStudi) {
                $sub->where('program_studi', $programStudi);
            });
        }

        if ($q !== '') {
            $query->whereHas('mahasiswa', function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $items = $query->orderByDesc('id')->get();
        $kaprodi = $this->resolveKaprodi($context['programStudi']);

        $html = view('kkn.index-pdf', [
            'items' => $items,
            'q' => $q,
            'status' => $status,
            'programStudi' => $context['programStudi'],
            'printedBy' => $request->user()?->name,
            'kaprodi' => $kaprodi,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'daftar-pendaftaran-kkn-'.now()->format('YmdHis').'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function show(Request $request, KknPengajuan $kkn): View
    {
        $context = $this->resolveContext($request);

        if ($context['programStudi']) {
            $kkn->loadMissing('mahasiswa');
            abort_unless((string) ($kkn->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        $kkn->load(['mahasiswa', 'posko.pembimbingS']);

        return view('admin.kkn.show', [
            'kkn' => $kkn,
            'routePrefix' => $context['routePrefix'],
            'canAssign' => $context['canAssign'],
        ]);
    }

    public function updateStatus(Request $request, KknPengajuan $kkn): RedirectResponse
    {
        $context = $this->resolveContext($request);
        abort_unless($context['canAssign'], 403);

        if ($context['programStudi']) {
            $kkn->loadMissing('mahasiswa');
            abort_unless((string) ($kkn->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

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

    public function destroy(Request $request, KknPengajuan $kkn): RedirectResponse
    {
        $context = $this->resolveContext($request);
        abort_unless($context['canAssign'], 403);

        if ($context['programStudi']) {
            $kkn->loadMissing('mahasiswa');
            abort_unless((string) ($kkn->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        $kkn->delete();

        $routeBack = ($context['routePrefix'] ?? 'admin') === 'admin' ? 'admin.kkn.index' : 'dosen.kkn-pengajuan.index';

        return redirect()->route($routeBack)->with('success', 'Data pendaftaran KKN berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $context = $this->resolveContext($request);
        abort_unless($context['canAssign'], 403);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));

        $query = KknPengajuan::query()->whereIn('id', $ids);
        if ($context['programStudi']) {
            $programStudi = $context['programStudi'];
            $query->whereHas('mahasiswa', function ($sub) use ($programStudi) {
                $sub->where('program_studi', $programStudi);
            });
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Tidak ada data KKN yang ditemukan untuk dihapus.');
        }

        foreach ($items as $it) {
            $it->delete();
        }

        return back()->with('success', 'Data pendaftaran KKN terpilih berhasil dihapus.');
    }

    public function poskoIndex(Request $request): View
    {
        $context = $this->resolveContext($request);
        abort_unless($context['routePrefix'] === 'admin', 403);

        $poskos = KknPosko::query()->with(['pembimbingS', 'pengajuans'])->orderByDesc('id')->paginate(10);
        return view('admin.kkn.posko-index', [
            'poskos' => $poskos,
        ]);
    }

    public function poskoCreate(Request $request): View
    {
        $context = $this->resolveContext($request);
        abort_unless($context['routePrefix'] === 'admin', 403);

        $dosenList = Dosen::query()->orderBy('nama')->get();
        return view('admin.kkn.posko-create', [
            'dosenList' => $dosenList,
        ]);
    }

    public function poskoStore(Request $request): RedirectResponse
    {
        $context = $this->resolveContext($request);
        abort_unless($context['routePrefix'] === 'admin', 403);

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

    public function poskoShow(Request $request, KknPosko $posko): View
    {
        $context = $this->resolveContext($request);
        abort_unless($context['routePrefix'] === 'admin', 403);

        $posko->load(['pembimbingS', 'pengajuans.mahasiswa', 'messages.sender', 'files.user']);
        $dosenList = Dosen::query()->orderBy('nama')->get();

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
        $context = $this->resolveContext($request);
        abort_unless($context['routePrefix'] === 'admin', 403);

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
        $context = $this->resolveContext($request);
        abort_unless($context['routePrefix'] === 'admin', 403);

        $validated = $request->validate([
            'kkn_pengajuan_ids' => ['required', 'array'],
            'kkn_pengajuan_ids.*' => ['exists:kkn_pengajuans,id'],
        ]);

        KknPengajuan::query()
            ->whereIn('id', $validated['kkn_pengajuan_ids'])
            ->update(['kkn_posko_id' => $posko->id]);

        return back()->with('success', 'Mahasiswa berhasil ditambahkan ke posko.');
    }

    public function removeStudent(Request $request, KknPengajuan $kkn): RedirectResponse
    {
        $context = $this->resolveContext($request);
        abort_unless($context['routePrefix'] === 'admin', 403);

        $kkn->update(['kkn_posko_id' => null]);
        return back()->with('success', 'Mahasiswa dikeluarkan dari posko.');
    }

    public function poskoDestroy(Request $request, KknPosko $posko): RedirectResponse
    {
        $context = $this->resolveContext($request);
        abort_unless($context['routePrefix'] === 'admin', 403);

        if ($posko->sk_pembimbing_path) {
            Storage::disk('public')->delete($posko->sk_pembimbing_path);
        }

        $posko->pengajuans()->update(['kkn_posko_id' => null]);
        $posko->delete();

        return redirect()->route('admin.kkn.posko.index')->with('success', 'Posko berhasil dihapus.');
    }
}
