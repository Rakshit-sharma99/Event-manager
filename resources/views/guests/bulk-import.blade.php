@extends('layouts.app', ['title' => 'Bulk Import - Eventra'])
@section('page-title','CSV Import')
@section('content')
<section class="grid gap-6 lg:grid-cols-[.8fr_1.2fr]">
    <form method="POST" enctype="multipart/form-data" action="{{ route('guests.bulk',$event) }}" class="glass-strong rounded-[2rem] p-6">@csrf<h2 class="font-display mb-4 text-3xl font-bold">Import guests</h2><p class="mb-5 text-white/55">Upload CSV columns: name,email,phone,dietary,plus_one_count.</p><input class="mb-5 w-full" type="file" name="csv" required><button class="btn-primary w-full">Validate and import</button></form>
    <div class="glass rounded-[2rem] p-6"><h3 class="font-display text-2xl font-bold">CSV template</h3><pre class="mt-4 overflow-auto rounded-2xl bg-black/40 p-4 text-sm text-white/70">name,email,phone,dietary,plus_one_count
Riya Mehta,riya@example.com,+919876543210,veg,1
Kabir Rao,kabir@example.com,+919812345678,vegan,0</pre></div>
</section>
@endsection
