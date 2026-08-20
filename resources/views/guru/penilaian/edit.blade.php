@extends('layouts.guru')

@section('title', 'Edit Penilaian')

@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Pembelajaran / Penilaian /</span> Edit</h4>

    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Penilaian</h5>
                    <small class="text-muted float-end">Edit data</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('guru.penilaian.update', $penilaian) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @include('guru.penilaian.partials.form', ['penilaian' => $penilaian])

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('guru.penilaian.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
