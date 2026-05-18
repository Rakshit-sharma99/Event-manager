@extends('layouts.guest', ['title' => 'Verify Email - Eventra'])

@section('content')
<section class="mx-auto grid min-h-[72vh] max-w-3xl place-items-center px-5">
    <div class="glass-strong rounded-[2rem] p-8 text-center" data-reveal>
        <i data-lucide="mail-check" class="mx-auto mb-5 h-12 w-12 text-eventra-cyan"></i>
        <h1 class="font-display text-4xl font-bold">Verify your email</h1>
        <p class="mt-4 text-white/60">In production this page sends an email. In local/demo mode, Eventra shows the verification link in the toast after registration.</p>
        <a class="btn-primary mt-6" href="{{ route('dashboard') }}">Back to dashboard</a>
    </div>
</section>
@endsection
