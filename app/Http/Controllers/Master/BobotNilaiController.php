<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\JenisNilai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BobotNilaiController extends Controller
{
    public function index(): View
    {
        $jenisNilais = JenisNilai::orderBy('urutan')->get();
        $totalBobot = $jenisNilais->where('is_active', true)->sum('bobot');

        return view('master.bobot-nilai.index', compact('jenisNilais', 'totalBobot'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bobot' => ['required', 'array'],
            'bobot.*' => ['required', 'integer', 'min:0', 'max:100'],
        ], [
            'bobot.required' => 'Data bobot nilai wajib diisi.',
            'bobot.*.required' => 'Setiap bobot nilai wajib diisi.',
            'bobot.*.integer' => 'Bobot nilai harus berupa angka bulat.',
            'bobot.*.min' => 'Bobot nilai minimal 0%.',
            'bobot.*.max' => 'Bobot nilai maksimal 100%.',
        ]);

        $activeIds = JenisNilai::where('is_active', true)->pluck('id')->all();
        $total = 0;

        foreach ($activeIds as $id) {
            $total += (int) ($validated['bobot'][$id] ?? 0);
        }

        if ($total !== 100) {
            throw ValidationException::withMessages([
                'bobot' => "Total persentase bobot nilai aktif harus tepat 100% (saat ini: {$total}%).",
            ]);
        }

        DB::transaction(function () use ($validated) {
            foreach ($validated['bobot'] as $id => $bobotVal) {
                JenisNilai::where('id', $id)->update([
                    'bobot' => (int) $bobotVal,
                ]);
            }
        });

        $prefix = auth()->user()?->isKepalaSekolah() ? 'kepala-sekolah' : 'pegawai-tu';

        return redirect()
            ->route("{$prefix}.master.bobot-nilai.index")
            ->with('success', 'Pengaturan bobot nilai berhasil diperbarui.');
    }
}
