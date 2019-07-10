@extends('layouts.base')

@section('content')
    @include('partials.validation')

    <div class="section">
        <a href="{{ route('index', $run) }}" class="waves-effect waves-light btn-large{{ !request()->get('coffee_type') ? ' disabled' : '' }}">All</a>

        @foreach($coffeeTypes as $type)
            <a href="{{ route('index', ['uuid' => $run, 'coffee_type' => $type->slug]) }}" class="waves-effect waves-light btn-small{{ request()->get('coffee_type') === $type->slug ? ' disabled' : '' }}"><span>{{ $type->name }} (<b>{{ $type->total }}</b>)</span></a>
        @endforeach
    </div>

    <div class="section">
        @forelse($userCoffees as $userCoffee)
            <div class="col s12 m6 l4">
                <div class="card">
                    <div class="card-image">
                        <a href="{{ $userCoffee->user->cup ? $userCoffee->user->cup->image_path : '/dist/img/no-cup.png' }}" target="_blank">
                            <img class="" src="{{ $userCoffee->user->cup ? $userCoffee->user->cup->thumbnail_path : '/dist/img/no-cup-thumb.jpg' }}">
                        </a>
                    </div>
                    <div class="card-content">
                        <span class="card-title grey-text text-darken-4 tooltipped" data-position="top" data-tooltip="{{ $userCoffee->user->full_name }}">{{ str_limit($userCoffee->user->full_name, 9) }}</span>
                        <p><strong>Coffee</strong>: {{ $userCoffee->coffee->name }}</p>
                        <p><strong>Sugars</strong>: {{ $userCoffee->sugar }}</p>
                    </div>
                    <div class="card-action">
                        <a href="{{ $userCoffee->getAdhocUrl($run) }}">Change</a>

                        <a href="{{ route('dashboard.runs.confirm-remove', compact('run', 'userCoffee')) }}">Remove</a>
                    </div>
                </div>
            </div>
        @empty
            <p>No coffee available</p>
        @endforelse
    </div>
@endsection
