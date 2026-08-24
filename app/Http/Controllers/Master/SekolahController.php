<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SekolahController extends Controller
{
    public function edit(): View
    {
        $sekolah = Sekolah::getSetting();

        $gurus = Guru::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $isPegawaiTu = auth()->user()?->isPegawaiTu();
        $layout = $isPegawaiTu ? 'layouts.pegawai-tu' : 'layouts.kepala-sekolah';

        return view('master.sekolah.edit', compact('sekolah', 'gurus', 'layout', 'isPegawaiTu'));
    }

    public function update(Request $request): RedirectResponse
    {
        $sekolah = Sekolah::getSetting();

        $validated = $request->validate([
            'nama_instansi' => ['required', 'string', 'max:255'],
            'nama_dinas' => ['required', 'string', 'max:255'],
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:20'],
            'akreditasi' => ['nullable', 'string', 'max:10'],
            'alamat' => ['nullable', 'string'],
            'kelurahan' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kabupaten_kota' => ['nullable', 'string', 'max:100'],
            'provinsi' => ['nullable', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:100'],
            'website' => ['nullable', 'string', 'max:100'],
            'kepala_sekolah_nama' => ['required', 'string', 'max:255'],
            'kepala_sekolah_nip' => ['nullable', 'string', 'max:30'],
            'kepala_sekolah_ttd_lokasi' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($sekolah->logo && Storage::disk('public')->exists($sekolah->logo)) {
                Storage::disk('public')->delete($sekolah->logo);
            }
            $validated['logo'] = $request->file('logo')->store('sekolah', 'public');
        }

        $sekolah->update($validated);

        return back()->with('success', 'Pengaturan identitas sekolah dan kop laporan berhasil disimpan.');
    }
}
