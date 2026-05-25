@extends('layouts.app', ['title' => 'CSV Bulk Import — Eventra'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-surface-100 pb-4">
        <div>
            <h1 class="text-h2 font-extrabold text-neutral-dark">CSV Bulk Import</h1>
            <p class="text-body text-surface-500 mt-1">Import multiple guests instantly by uploading a standardized CSV file.</p>
        </div>
        <x-btn href="{{ route('guests.index', $event) }}" variant="ghost" icon="arrow-left" size="sm">
            Back
        </x-btn>
    </div>

    {{-- Grid Layout --}}
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Left Form Panel --}}
        <div class="lg:col-span-2">
            <x-card class="space-y-6" data-animate="fade-up">
                <h2 class="text-h3 font-bold text-neutral-dark">Upload Guest Sheet</h2>
                <p class="text-body text-surface-500">
                    Make sure your CSV file matches the template structure shown on the right. Columns must be: <strong>name, email, phone, dietary, plus_one_count</strong>.
                </p>

                <form method="POST" enctype="multipart/form-data" action="{{ route('guests.bulk', $event) }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <label for="csv" class="block text-body font-medium text-surface-700">Select CSV File <span class="text-danger">*</span></label>
                        <div class="flex items-center gap-4 p-6 rounded-md border-2 border-dashed border-surface-300 bg-surface-50 hover:bg-surface-100 hover:border-primary-400 transition-all cursor-pointer relative group">
                            <input 
                                type="file" 
                                name="csv" 
                                id="csv" 
                                accept=".csv,text/csv,text/plain" 
                                required 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            >
                            <div class="w-full text-center space-y-2">
                                <div class="text-4xl text-surface-400 group-hover:scale-110 transition-transform">📂</div>
                                <p class="text-body font-semibold text-neutral-dark">Click to browse or drag & drop</p>
                                <p class="text-caption text-surface-400 font-medium">Supported formats: .csv, .txt (Max 2MB)</p>
                            </div>
                        </div>
                        @if($errors->first('csv'))
                            <p class="text-caption text-danger animate-shake">{{ $errors->first('csv') }}</p>
                        @endif
                    </div>

                    <div class="flex justify-end gap-3 border-t border-surface-100 pt-6">
                        <x-btn href="{{ route('guests.index', $event) }}" variant="ghost">
                            Cancel
                        </x-btn>
                        <button type="submit" class="btn-primary py-2.5 px-6">
                            Validate and Import
                        </button>
                    </div>
                </form>
            </x-card>
        </div>

        {{-- Right Template Panel --}}
        <div>
            <x-card class="bg-surface-50 border-transparent space-y-4" data-animate="fade-up">
                <h3 class="text-body-lg font-bold text-neutral-dark">📋 CSV Template Guidelines</h3>
                <p class="text-body text-surface-500">
                    Ensure your spreadsheet has the exact headers shown below. Spaces or misspelling in headers will cause import errors.
                </p>

                <div class="space-y-2">
                    <span class="text-caption font-bold text-surface-500 uppercase tracking-wider">Example Content</span>
                    <pre class="overflow-auto rounded-md bg-neutral-dark p-4 text-xs font-mono text-white/90 leading-relaxed shadow-inner">name,email,phone,dietary,plus_one_count
Riya Mehta,riya@example.com,+919876543210,veg,1
Kabir Rao,kabir@example.com,+919812345678,vegan,0</pre>
                </div>

                <div class="space-y-1 text-caption text-surface-400 font-medium pt-2">
                    <p>• <strong>dietary</strong> options: veg, non-veg, vegan, gluten-free, jain, other</p>
                    <p>• <strong>plus_one_count</strong>: number between 0 and 5</p>
                    <p>• <strong>phone</strong>: optional, include country code (e.g. +91)</p>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
