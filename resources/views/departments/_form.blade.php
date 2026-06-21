<div class="space-y-4">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
        <input type="text" name="name" id="name" value="{{ old('name', $department->name ?? '') }}" required
               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="manager_id" class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.employee_manager') }}</label>
        <select name="manager_id" id="manager_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <option value="">--</option>
            @foreach($managers as $mgr)
            <option value="{{ $mgr->id }}" {{ old('manager_id', $department->manager_id ?? '') == $mgr->id ? 'selected' : '' }}>{{ $mgr->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color *</label>
        <div class="flex items-center gap-x-3">
            <input type="color" name="color" id="color" value="{{ old('color', $department->color ?? '#3b82f6') }}"
                   class="h-10 w-14 rounded border-gray-300 cursor-pointer">
            <input type="text" value="{{ old('color', $department->color ?? '#3b82f6') }}" readonly
                   class="block w-28 rounded-lg border-gray-300 bg-gray-50 text-sm text-gray-500"
                   id="color-text">
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('color').addEventListener('input', function(e) {
        document.getElementById('color-text').value = e.target.value;
    });
</script>
@endpush
