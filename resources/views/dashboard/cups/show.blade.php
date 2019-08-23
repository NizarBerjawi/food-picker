@extends('layouts.base')

@section('content')
    <div class="col s12 m8 offset-m2">
        <div class="card-panel">
            <div class="card-content">
                <h5 class="card-title">{{ __('View your cup') }}</h5>

                @include('dashboard.cups.form', [
                    'enabled' => false,
                ])
            </div>
        </div>
    </div>
@endsection