{{-- resources/views/tasks/_tipe_task_field.blade.php --}}
{{-- Gunakan @include('tasks._tipe_task_field') di form create & edit --}}

<div class="mb-3">
    <label class="form-label fw-semibold">Tipe Task <span class="text-danger">*</span></label>
    <div class="d-flex gap-2 flex-wrap">

        {{-- Daily --}}
        <input type="radio" class="btn-check" name="tipe_task" id="tipe_daily"
               value="daily" autocomplete="off"
               {{ old('tipe_task', $task->tipe_task ?? 'daily') === 'daily' ? 'checked' : '' }}>
        <label class="btn btn-outline-secondary" for="tipe_daily">
            <i class="bi bi-calendar-day me-1"></i> Daily
        </label>

        {{-- Weekly --}}
        <input type="radio" class="btn-check" name="tipe_task" id="tipe_weekly"
               value="weekly" autocomplete="off"
               {{ old('tipe_task', $task->tipe_task ?? '') === 'weekly' ? 'checked' : '' }}>
        <label class="btn btn-outline-info" for="tipe_weekly">
            <i class="bi bi-calendar-week me-1"></i> Weekly
        </label>

        {{-- Monthly --}}
        <input type="radio" class="btn-check" name="tipe_task" id="tipe_monthly"
               value="monthly" autocomplete="off"
               {{ old('tipe_task', $task->tipe_task ?? '') === 'monthly' ? 'checked' : '' }}>
        <label class="btn btn-outline-warning" for="tipe_monthly">
            <i class="bi bi-calendar-month me-1"></i> Monthly
        </label>

    </div>
    @error('tipe_task')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>