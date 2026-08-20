@extends('layouts.pegawai-tu')

@section('title', 'Edit Pegawai TU')

@section('content')
    <div class="py-3 mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Master / <a href="{{ route('pegawai-tu.master.pegawai-tu.index') }}" class="text-muted">Pegawai TU</a> /</span>
            Edit Pegawai TU
        </h4>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 col-md-10">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Edit Pegawai Tata Usaha</h5>
                    <a href="{{ route('pegawai-tu.master.pegawai-tu.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i> Kembali
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('pegawai-tu.master.pegawai-tu.update', $pegawaiTu) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @include('master.pegawai-tu.partials.form', ['pegawaiTu' => $pegawaiTu])

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bx bx-save me-1"></i> Perbarui
                            </button>
                            <a href="{{ route('pegawai-tu.master.pegawai-tu.index') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
