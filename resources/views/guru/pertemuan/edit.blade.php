@extends('layouts.guru')

@section('title', 'Edit Pertemuan')

@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pembelajaran / Pertemuan /</span> Edit</h4>

    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Pertemuan</h5>
                    <small class="text-muted float-end">Edit data</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.pertemuan.update', $pertemuan) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @include('guru.pertemuan.partials.form', ['pertemuan' => $pertemuan])

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('guru.pertemuan.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
