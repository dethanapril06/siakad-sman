@extends('layouts.pegawai-tu')

@section('title', 'Tambah Tahun Akademik')

@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / Tahun Akademik /</span> Tambah</h4>

    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Tahun Akademik</h5>
                    <small class="text-muted float-end">Tambah data</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('pegawai-tu.master.tahun-akademik.store') }}" method="POST">
                        @csrf

                        @include('master.tahun-akademik.partials.form')

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('pegawai-tu.master.tahun-akademik.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
