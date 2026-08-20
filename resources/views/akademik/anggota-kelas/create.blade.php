@extends('layouts.pegawai-tu')

@section('title', 'Tambah Anggota Kelas')

@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Akademik / Anggota Kelas /</span> Tambah</h4>

    <div class="row">
        <div class="col-xl-9">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Tambah Anggota Kelas</h5>
                        <small class="text-muted">{{ $kelasAkademik->nama_lengkap }} - {{ $kelasAkademik->tahunAkademik?->nama ?? '-' }}</small>
                    </div>
                </div>
                <div class="card-body">
                    @error('siswa_ids')
                        <div class="alert alert-danger" role="alert">{{ $message }}</div>
                    @enderror

                    <form action="{{ route('pegawai-tu.akademik.anggota-kelas.store', $kelasAkademik) }}" method="POST">
                        @csrf

                        <div class="table-responsive text-nowrap mb-4">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 64px;">Pilih</th>
                                        <th>NIS</th>
                                        <th>NISN</th>
                                        <th>Nama Siswa</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @forelse ($siswas as $siswa)
                                        <tr>
                                            <td>
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="siswa_ids[]"
                                                    value="{{ $siswa->id }}"
                                                    id="siswa-{{ $siswa->id }}"
                                                    @checked(in_array($siswa->id, old('siswa_ids', [])))
                                                />
                                            </td>
                                            <td><strong>{{ $siswa->nis }}</strong></td>
                                            <td>{{ $siswa->nisn ?? '-' }}</td>
                                            <td>
                                                <label class="mb-0" for="siswa-{{ $siswa->id }}">{{ $siswa->nama }}</label>
                                            </td>
                                            <td><span class="badge bg-label-success">Aktif</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                Tidak ada siswa aktif yang tersedia untuk tahun akademik ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" @disabled($siswas->isEmpty())>Simpan</button>
                            <a href="{{ route('pegawai-tu.akademik.anggota-kelas.index', $kelasAkademik) }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
