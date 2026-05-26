{{-- resources/views/tasks/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 600px;">
    <h5 class="mb-4">✏️ Edit Tugas</h5>

    <form action="{{ route('tasks.update', $task) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Nama Tugas --}}
        <div class="mb-3">
            <label for="nama_tugas" class="form-label fw-semibold">Nama Tugas <span class="text-danger">*</span></label>
            <input type="text" name="nama_tugas" id="nama_tugas"
                   class="form-control @error('nama_tugas') is-invalid @enderror"
                   value="{{ old('nama_tugas', $task->nama_tugas) }}">
            @error('nama_tugas')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Deskripsi --}}
        <div class="mb-3">
            <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="3"
                      class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $task->deskripsi) }}</textarea>
            @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Kategori --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
            <div class="d-flex gap-2">
                <input type="radio" class="btn-check" name="kategori" id="kat_school"
                       value="School" {{ old('kategori', $task->kategori) === 'School' ? 'checked' : '' }}>
                <label class="btn btn-outline-primary" for="kat_school">🏫 School</label>

                <input type="radio" class="btn-check" name="kategori" id="kat_work"
                       value="Work" {{ old('kategori', $task->kategori) === 'Work' ? 'checked' : '' }}>
                <label class="btn btn-outline-success" for="kat_work">💼 Work</label>
            </div>
            @error('kategori')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Tipe Task (BARU) --}}
        @include('tasks._tipe_task_field')

        {{-- Deadline --}}
        <div class="mb-4">
            <label for="deadline" class="form-label fw-semibold">Deadline <span class="text-danger">*</span></label>
            <input type="date" name="deadline" id="deadline"
                   class="form-control @error('deadline') is-invalid @enderror"
                   value="{{ old('deadline', $task->deadline->format('Y-m-d')) }}">
            @error('deadline')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Perbarui
            </button>
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection