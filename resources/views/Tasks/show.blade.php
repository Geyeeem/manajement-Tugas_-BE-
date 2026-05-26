@extends('layouts.app')

@section('title', 'Detail Tugas')

@section('content')

<div class="card mx-auto" style="max-width:600px;">
    <div class="card-body p-4">

        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="{{ route('tasks.index') }}" class="btn btn-light btn-sm rounded-circle">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h5 class="mb-0 fw-bold">Detail Tugas</h5>
        </div>

        {{-- Kategori --}}
        <span class="badge badge-{{ strtolower($task->kategori) }} mb-3 px-3 py-2">
            @if($task->kategori === 'School')
                <i class="bi bi-mortarboard-fill"></i>
            @else
                <i class="bi bi-briefcase-fill"></i>
            @endif
            {{ $task->kategori }}
        </span>

        {{-- Status --}}
        <span class="badge {{ $task->selesai ? 'bg-success' : 'bg-secondary' }} mb-3 ms-2 px-3 py-2">
            {{ $task->selesai ? 'Selesai' : 'Belum Selesai' }}
        </span>

        <h4 class="fw-bold {{ $task->selesai ? 'text-decoration-line-through text-muted' : '' }}">
            {{ $task->nama_tugas }}
        </h4>

        @if($task->deskripsi)
            <p class="text-muted">{{ $task->deskripsi }}</p>
        @else
            <p class="text-muted fst-italic">Tidak ada deskripsi.</p>
        @endif

        <hr>

        <div class="d-flex align-items-center gap-2 text-muted">
            <i class="bi bi-calendar3"></i>
            <span>Deadline: <strong>{{ $task->deadline->format('d M Y') }}</strong></span>

            @if(!$task->selesai && $task->deadline->isPast())
                <span class="badge bg-danger ms-2">Terlambat</span>
            @elseif(!$task->selesai && $task->deadline->isToday())
                <span class="badge bg-warning ms-2">Hari ini</span>
            @elseif(!$task->selesai)
                <span class="badge bg-info ms-2">
                    {{ $task->deadline->diffInDays(now()) }} hari lagi
                </span>
            @endif
        </div>

        <hr>

        {{-- Aksi --}}
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-primary rounded-pill flex-fill">
                <i class="bi bi-pencil"></i> Edit
            </a>

            <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="flex-fill">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-outline-success rounded-pill w-100">
                    <i class="bi bi-check2-circle"></i>
                    {{ $task->selesai ? 'Tandai Belum Selesai' : 'Tandai Selesai' }}
                </button>
            </form>

            <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                  onsubmit="return confirm('Hapus tugas ini?')" class="flex-fill">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger rounded-pill w-100">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </form>
        </div>

    </div>
</div>

@endsection
