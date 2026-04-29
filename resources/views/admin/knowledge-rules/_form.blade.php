@php
    $rule = $rule ?? null;
    $symptomOptions = is_object($symptoms ?? null) && method_exists($symptoms, 'items') ? collect($symptoms->items()) : collect($symptoms ?? []);
    $disorderOptions = is_object($disorders ?? null) && method_exists($disorders, 'items') ? collect($disorders->items()) : collect($disorders ?? []);
    $key = data_get($rule, 'id');
    $isEdit = filled($key);
    $action = $isEdit
        ? (\Illuminate\Support\Facades\Route::has('admin.knowledge-rules.update') ? route('admin.knowledge-rules.update', $key) : '#')
        : (\Illuminate\Support\Facades\Route::has('admin.knowledge-rules.store') ? route('admin.knowledge-rules.store') : '#');
    $selectedSymptom = old('symptom_id', data_get($rule, 'symptom_id', data_get($rule, 'symptom.id')));
    $selectedDisorder = old('mental_disorder_id', old('disorder_id', data_get($rule, 'mental_disorder_id', data_get($rule, 'mentalDisorder.id'))));
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="symptom_id" class="block text-sm font-semibold text-slate-700">Gejala</label>
            <select id="symptom_id" name="symptom_id" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20">
                <option value="">Pilih gejala</option>
                @foreach ($symptomOptions as $symptom)
                    @php $value = data_get($symptom, 'id', data_get($symptom, 'code')); @endphp
                    <option value="{{ $value }}" @selected((string) $selectedSymptom === (string) $value)>
                        {{ data_get($symptom, 'code', $value) }} - {{ data_get($symptom, 'name', data_get($symptom, 'description')) }}
                    </option>
                @endforeach
            </select>
            @error('symptom_id') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="mental_disorder_id" class="block text-sm font-semibold text-slate-700">Gangguan</label>
            <select id="mental_disorder_id" name="mental_disorder_id" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20">
                <option value="">Pilih gangguan</option>
                @foreach ($disorderOptions as $disorder)
                    @php $value = data_get($disorder, 'id', data_get($disorder, 'code')); @endphp
                    <option value="{{ $value }}" @selected((string) $selectedDisorder === (string) $value)>
                        {{ data_get($disorder, 'code', $value) }} - {{ data_get($disorder, 'name') }}
                    </option>
                @endforeach
            </select>
            @error('mental_disorder_id') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="belief" class="block text-sm font-semibold text-slate-700">Belief</label>
            <input id="belief" name="belief" value="{{ old('belief', data_get($rule, 'belief')) }}" type="number" step="0.01" min="0" max="1" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="0.60">
            @error('belief') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="plausibility" class="block text-sm font-semibold text-slate-700">Plausibility</label>
            <input id="plausibility" name="plausibility" value="{{ old('plausibility', data_get($rule, 'plausibility')) }}" type="number" step="0.01" min="0" max="1" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Opsional">
            @error('plausibility') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="notes" class="block text-sm font-semibold text-slate-700">Catatan pakar</label>
        <textarea id="notes" name="notes" rows="4" class="mt-2 block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20" placeholder="Sumber atau alasan penetapan nilai evidence.">{{ old('notes', data_get($rule, 'notes')) }}</textarea>
        @error('notes') <p class="mt-2 text-sm text-rose-700">{{ $message }}</p> @enderror
    </div>

    <label class="flex items-center gap-3 rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', data_get($rule, 'is_active', true))) class="size-4 rounded border-slate-300 text-teal-700 focus:ring-teal-600">
        Aturan aktif pada perhitungan
    </label>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
        <a href="{{ \Illuminate\Support\Facades\Route::has('admin.knowledge-rules.index') ? route('admin.knowledge-rules.index') : '#' }}" class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
            <x-icon name="arrow-right" class="size-4 rotate-180" />
            Batal
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-teal-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-teal-800">
            <x-icon name="check-circle" class="size-4" />
            Simpan Aturan
        </button>
    </div>
</form>
