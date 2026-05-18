@extends('layouts.app', ['title' => 'Gallery - Eventra'])
@section('page-title','Gallery')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4"><div><p class="chip">{{ $event->event_name }}</p><h2 class="mt-3 font-display text-4xl font-bold">Visual gallery</h2></div><a class="btn-ghost" href="{{ route('events.show',$event) }}">Back</a></div>
<form method="POST" enctype="multipart/form-data" action="{{ route('gallery.store',$event) }}" class="glass mb-6 grid gap-3 rounded-[2rem] p-4 md:grid-cols-[1fr_1fr_auto]">@csrf<input type="file" name="images[]" multiple required><input name="caption" placeholder="Caption"><button class="btn-primary !py-2">Upload</button></form>
<div class="columns-1 gap-5 sm:columns-2 xl:columns-3">
@forelse($images as $image)
    @php($src = str_starts_with($image->image_path, 'http') ? $image->image_path : asset('storage/'.$image->image_path))
    <figure class="glass mb-5 break-inside-avoid overflow-hidden rounded-[2rem]"><img class="w-full transition duration-500 hover:scale-105" src="{{ $src }}"><figcaption class="flex items-center justify-between p-4 text-sm text-white/55"><span>{{ $image->caption }}</span><form method="POST" action="{{ route('gallery.destroy',[$event,$image]) }}">@csrf @method('DELETE')<button class="text-rose-300">Delete</button></form></figcaption></figure>
@empty
    <div class="glass-strong rounded-[2rem] p-8"><h3 class="font-display text-2xl font-bold">No images yet</h3><p class="mt-2 text-white/55">Upload moodboards, venue frames, and event moments.</p></div>
@endforelse
</div>
@endsection
