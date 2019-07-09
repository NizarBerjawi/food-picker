@extends('layouts.base')

@section('content')
    <div class="section">
        <h3>View Coffee Run</h3>
        @include('partials.validation')

        <div class="row">
            @include('dashboard.runs.form')
        </div>
    </div>
@endsection
