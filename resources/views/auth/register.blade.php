@extends('layouts.guest', ['title' => 'Register - Eventra'])

@section('content')
<section class="mx-auto grid min-h-[78vh] max-w-6xl items-center gap-8 px-5 py-10 lg:grid-cols-[.9fr_1.1fr]">
    <div data-reveal><p class="chip mb-5">Planner / Vendor / Guest</p><h1 class="font-display text-5xl font-bold">Create your Eventra identity.</h1><p class="mt-4 text-white/60">Role-aware registration routes every user into the right dashboard flow.</p></div>
    <form method="POST" action="{{ route('register.store') }}" class="glass-strong grid gap-4 rounded-[2rem] p-6 sm:grid-cols-2" data-reveal>
        @csrf
        <div><label class="field-label">Name</label><input class="w-full" name="name" value="{{ old('name') }}" required></div>
        <div><label class="field-label">Email</label><input class="w-full" name="email" type="email" value="{{ old('email') }}" required></div>
        <div><label class="field-label">Phone</label><input class="w-full" name="phone" value="{{ old('phone') }}"></div>
        <div><label class="field-label">Role</label><select class="w-full" name="role"><option value="planner">Planner</option><option value="vendor">Vendor</option><option value="guest">Guest</option></select></div>
        <div><label class="field-label">Password</label><input class="w-full" name="password" type="password" required></div>
        <div><label class="field-label">Confirm Password</label><input class="w-full" name="password_confirmation" type="password" required></div>
        <div class="sm:col-span-2"><button class="btn-primary w-full magnetic">Create account</button></div>
    </form>
</section>
@endsection
