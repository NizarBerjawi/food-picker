@extends('layouts.base')

@section('content')
    <div class="col s12 m8 offset-m2">
        <div class="card-panel">
            <div class="card-content">
                <h5 class="card-title">{{ __('Edit this Coffee') }}</h5>

                @include('dashboard.coffee.form', [
                    'action'  => route('dashboard.coffee.update', $userCoffee),
                    'method'  => 'PUT',
                    'enabled' => true,
                ])
            </div>
        </div>
    </div>
@endsection
