@extends('layouts.base')

@section('content')
    <h3>View Coffee</h3>

    <div class="row">
        @include('coffees.form', [
            'disabled' => true,
        ])
    </div>
@endsection
