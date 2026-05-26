{{-- resources/views/tasks/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            📋 Daftar Tugas
            @if($totalPending > 0)
                <span class="badge bg-danger ms-1">{{ $totalPending }} pending</span>
            @endif
        </h4>
        <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Tambah Tugas
        </a>
    </div>

    {{-- Alert flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter: Kategori --}}
    <div class="mb-2 d-flex gap-2 flex-wrap align-items-center">
        <span class="text-muted small">Kategori:</span>
        @foreach([''=>'Semua','School'=>'School','Work'=>'Work'] as $val => $label)
            <a href="{{ request()->fullUrlWithQuery(['kategori' => $val]) }}"
               class="btn btn-sm {{ request('kategori', '') === $val ? 'btn-dark' : 'btn-outline-dark' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Filter: Tipe Task (TAB BARU) --}}
    <ul class="nav nav-tabs mb-3">
        @php
            $tipeList = [
                ''        => ['label' => 'Semua',   'icon' => 'bi-list-task',      'badge_class' => 'bg-danger'],
                'daily'   => ['label' => 'Daily',   'icon' => 'bi-calendar-day',   'badge_class' => 'bg-secondary'],
                'weekly'  => ['label' => 'Weekly',  'icon' => 'bi-calendar-week',  'badge_class' => 'bg-info text-dark'],
                'monthly' => ['label' => 'Monthly', 'icon' => 'bi-calendar-month', 'badge_class' => 'bg-warning text-dark'],
            ];
            $currentTipe = request('tipe_task', '');
        @endphp

        @foreach($tipeList as $val => $info)
            @php
                $pendingCount = $val === ''
                    ? $totalPending
                    : ($pendingPerTipe[$val] ?? 0);
            @endphp
            <li class="nav-item">
                <a class="nav-link {{ $currentTipe === $val ? 'active' : '' }}"
                   href="{{ request()->fullUrlWithQuery(['tipe_task' => $val]) }}">
                    <i class="bi {{ $info['icon'] }} me-1"></i>
                    {{ $info['label'] }}
                    @if($pendingCount > 0)
                        <span class="badge {{ $info['badge_class'] }} ms-1">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>

    {{-- Tabel Tugas --}}
    @if($tasks->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1"></i>
            <p class="mt-2">Belum ada tugas. <a href="{{ route('tasks.create') }}">Tambah sekarang</a>.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Nama Tugas</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $i => $task)
                    <tr class="{{ $task->selesai ? 'table-success opacity-75' : ($task->mendekati_deadline ? 'table-warning' : '') }}">
                        <td class="text-muted small">{{ $i + 1 }}</td>

                        {{-- Nama --}}
                        <td>
                            <span class="{{ $task->selesai ? 'text-decoration-line-through text-muted' : 'fw-semibold' }}">
                                {{ $task->nama_tugas }}
                            </span>
                            @if($task->mendekati_deadline && !$task->selesai)
                                <span class="badge bg-danger ms-1 small">Segera!</span>
                            @endif
                            @if($task->deskripsi)
                                <div class="text-muted small text-truncate" style="max-width:200px">
                                    {{ $task->deskripsi }}
                                </div>
                            @endif
                        </td>

                        {{-- Kategori --}}
                        <td>
                            <span class="badge bg-{{ $task->kategori === 'School' ? 'primary' : 'success' }}">
                                {{ $task->kategori }}
                            </span>
                        </td>

                        {{-- Tipe Task (BARU) --}}
                        <td>
                            <span class="badge bg-{{ $task->tipe_task_badge }} text-{{ in_array($task->tipe_task, ['weekly','monthly']) ? 'dark' : 'white' }}">
                                <i class="bi bi-calendar{{ $task->tipe_task === 'daily' ? '-day' : ($task->tipe_task === 'weekly' ? '-week' : '-month') }} me-1"></i>
                                {{ $task->tipe_task_label }}
                            </span>
                        </td>

                        {{-- Deadline --}}
                        <td class="small">
                            {{ $task->deadline->translatedFormat('d M Y') }}
                        </td>

                        {{-- Status toggle --}}
                        <td>
                            <div class="form-check form-switch mb-0" title="{{ $task->selesai ? 'Tandai belum selesai' : 'Tandai selesai' }}">
                                <input class="form-check-input toggle-selesai" type="checkbox"
                                       data-id="{{ $task->id }}"
                                       {{ $task->selesai ? 'checked' : '' }}>
                            </div>
                        </td>

                        {{-- Aksi --}}
                        <td class="text-end">
                            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-sm btn-outline-secondary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus tugas ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.toggle-selesai').forEach(el => {
    el.addEventListener('change', function () {
        const id = this.dataset.id;
        fetch(`/tasks/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
        });
    });
});
</script>
@endpush