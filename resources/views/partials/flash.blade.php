@if(session('success') || $errors->any())
    <div class="flash">
        @if(session('success'))
            <p>{{ session('success') }}</p>
        @endif

        @if($errors->any())
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
