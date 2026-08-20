@extends('layouts.pegawai-tu')

@section('title', 'Tambah Jurusan')

@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Master / Jurusan /</span> Tambah</h4>

    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Jurusan</h5>
                    <small class="text-muted float-end">Tambah data</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('pegawai-tu.master.jurusan.store') }}" method="POST">
                        @csrf

                        @include('master.jurusan.partials.form')

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('pegawai-tu.master.jurusan.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
