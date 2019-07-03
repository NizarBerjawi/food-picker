@extends('layouts.base')

@section('content')
    <div class="section">
        @success
            @alert('success')
        @endsuccess

        @errors
            @alert('errors')
        @enderrors

        @include('dashboard.cups.form', [
            'action' => route('dashboard.cups.store'),
            'method' => 'POST',
        ])
    </div>
@endsection
