@extends('layouts.base')

@section('content')
    <h3>Orders</h3>

    @if(session()->has('success'))
        @include('partials.success', [
            'message' => session('success')->first()
        ])
    @endif

    <div class="row">
        @foreach($users as $user)
          <div>Name: {{ $user->name }}</div>

          <img class="materialboxed" width="250" src="{{ $user->cup->generateUrl() }}">
        @endforeach
    </div>

    <div class="row center-align">
        {{ $users->links() }}
    </div>
@endsection
