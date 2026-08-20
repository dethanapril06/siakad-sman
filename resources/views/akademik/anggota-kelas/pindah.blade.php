@extends('layouts.pegawai-tu')

@section('title', 'Pindah Kelas')

@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Akademik / Anggota Kelas /</span> Pindah</h4>

    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Pindah Kelas</h5>
                    <small class="text-muted float-end">{{ $anggotaKelas->kelasAkademik?->tahunAkademik?->nama ?? '-' }}</small>
                </div>
                <div class="card-body">
                    <div class="alert alert-info" role="alert">
                        {{ $anggotaKelas->siswa?->nama ?? '-' }} saat ini berada di kelas {{ $anggotaKelas->kelasAkademik?->nama_lengkap ?? '-' }}.
                    </div>

                    <form action="{{ route('pegawai-tu.akademik.anggota-kelas.pindah', $anggotaKelas) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label" for="kelas_akademik_id">Kelas Tujuan</label>
                            <select class="form-select @error('kelas_akademik_id') is-invalid @enderror" id="kelas_akademik_id" name="kelas_akademik_id" autofocus>
                                <option value="">Pilih kelas tujuan</option>
                                @foreach ($kelasAkademiks as $kelasAkademik)
                                    <option value="{{ $kelasAkademik->id }}" @selected((string) old('kelas_akademik_id') === (string) $kelasAkademik->id)>
                                        {{ $kelasAkademik->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kelas_akademik_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" @disabled($kelasAkademiks->isEmpty())>Pindahkan</button>
                            <a href="{{ route('pegawai-tu.akademik.anggota-kelas.index', $anggotaKelas->kelasAkademik) }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
