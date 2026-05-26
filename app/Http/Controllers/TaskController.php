<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    private const TIPE_TASK_VALID = ['daily', 'weekly', 'monthly'];

    // =========================================================
    //  WEB METHODS (Blade)
    // =========================================================

    public function index(Request $request)
    {
        $query = Task::where('user_id', Auth::id())
                     ->orderBy('deadline', 'asc');

        if ($request->filled('kategori') && in_array($request->kategori, ['School', 'Work'])) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('tipe_task') && in_array($request->tipe_task, self::TIPE_TASK_VALID)) {
            $query->where('tipe_task', $request->tipe_task);
        }

        $tasks        = $query->get();
        $totalPending = Task::where('user_id', Auth::id())->belumSelesai()->count();

        $pendingPerTipe = Task::where('user_id', Auth::id())
            ->belumSelesai()
            ->selectRaw('tipe_task, COUNT(*) as total')
            ->groupBy('tipe_task')
            ->pluck('total', 'tipe_task');

        return view('tasks.index', compact('tasks', 'totalPending', 'pendingPerTipe'));
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
            'kategori'   => 'required|in:School,Work',
            'tipe_task'  => 'required|in:daily,weekly,monthly',
            'deadline'   => 'required|date|after_or_equal:today',
        ], [
            'nama_tugas.required'     => 'Nama tugas wajib diisi.',
            'kategori.required'       => 'Pilih kategori tugas.',
            'tipe_task.required'      => 'Pilih tipe task.',
            'tipe_task.in'            => 'Tipe task tidak valid.',
            'deadline.required'       => 'Deadline wajib diisi.',
            'deadline.after_or_equal' => 'Deadline tidak boleh sebelum hari ini.',
        ]);

        Task::create([
            'user_id'    => Auth::id(),
            'nama_tugas' => $request->nama_tugas,
            'deskripsi'  => $request->deskripsi,
            'kategori'   => $request->kategori,
            'tipe_task'  => $request->tipe_task,
            'deadline'   => $request->deadline,
            'selesai'    => false,
        ]);

        return redirect()->route('tasks.index')
                         ->with('success', 'Tugas berhasil ditambahkan!');
    }

    public function show(Task $task)
    {
        $this->authorizeTask($task);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorizeTask($task);
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeTask($task);

        $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
            'kategori'   => 'required|in:School,Work',
            'tipe_task'  => 'required|in:daily,weekly,monthly',
            'deadline'   => 'required|date',
        ]);

        $task->update([
            'nama_tugas' => $request->nama_tugas,
            'deskripsi'  => $request->deskripsi,
            'kategori'   => $request->kategori,
            'tipe_task'  => $request->tipe_task,
            'deadline'   => $request->deadline,
        ]);

        return redirect()->route('tasks.index')
                         ->with('success', 'Tugas berhasil diperbarui!');
    }

    public function destroy(Task $task)
    {
        $this->authorizeTask($task);
        $task->delete();

        return redirect()->route('tasks.index')
                         ->with('success', 'Tugas berhasil dihapus!');
    }

    public function toggleSelesai(Task $task)
    {
        $this->authorizeTask($task);

        $task->update(['selesai' => !$task->selesai]);

        return response()->json([
            'success' => true,
            'selesai' => $task->selesai,
            'message' => $task->selesai ? 'Tugas ditandai selesai!' : 'Tugas ditandai belum selesai.',
        ]);
    }

    // =========================================================
    //  API METHODS (auth:sanctum)
    // =========================================================

    public function apiIndex(Request $request)
    {
        $query = Task::where('user_id', Auth::id())
                     ->orderBy('deadline', 'asc');

        if ($request->filled('kategori') && in_array($request->kategori, ['School', 'Work'])) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('tipe_task') && in_array($request->tipe_task, self::TIPE_TASK_VALID)) {
            $query->where('tipe_task', $request->tipe_task);
        }

        $tasks        = $query->get();
        $totalPending = Task::where('user_id', Auth::id())->belumSelesai()->count();

        $pendingPerTipe = Task::where('user_id', Auth::id())
            ->belumSelesai()
            ->selectRaw('tipe_task, COUNT(*) as total')
            ->groupBy('tipe_task')
            ->pluck('total', 'tipe_task');

        return response()->json([
            'success'          => true,
            'pending_count'    => $totalPending,
            'pending_per_tipe' => $pendingPerTipe,
            'tasks'            => $tasks,
        ]);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
            'kategori'   => 'required|in:School,Work',
            'tipe_task'  => 'required|in:daily,weekly,monthly',
            'deadline'   => 'required|date',
        ]);

        $task = Task::create([
            'user_id'    => Auth::id(),
            'nama_tugas' => $request->nama_tugas,
            'deskripsi'  => $request->deskripsi,
            'kategori'   => $request->kategori,
            'tipe_task'  => $request->tipe_task,
            'deadline'   => $request->deadline,
            'selesai'    => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil ditambahkan.',
            'task'    => $task,
        ], 201);
    }

    public function apiShow($id)
    {
        $task = Task::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        return response()->json([
            'success' => true,
            'task'    => $task,
        ]);
    }

    public function apiUpdate(Request $request, $id)
    {
        $task = Task::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'deskripsi'  => 'nullable|string',
            'kategori'   => 'required|in:School,Work',
            'tipe_task'  => 'required|in:daily,weekly,monthly',
            'deadline'   => 'required|date',
        ]);

        $task->update($request->only('nama_tugas', 'deskripsi', 'kategori', 'tipe_task', 'deadline'));

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil diperbarui.',
            'task'    => $task,
        ]);
    }

    public function apiDestroy($id)
    {
        $task = Task::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dihapus.',
        ]);
    }

    public function apiToggle($id)
    {
        $task = Task::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

        $task->update(['selesai' => !$task->selesai]);

        return response()->json([
            'success' => true,
            'selesai' => $task->selesai,
            'message' => $task->selesai ? 'Tugas ditandai selesai!' : 'Tugas ditandai belum selesai.',
        ]);
    }

    // =========================================================
    //  HELPER
    // =========================================================

    private function authorizeTask(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }
    }
}