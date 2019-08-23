@extends('layouts.base')

@section('content')
    <div class="col s12 m8 offset-m2">
        <div class="card-panel">
            <div class="card-content">
                <h5 class="card-title">{{ __('Delete this schedule') }}</h5>

                <form action="{{ route('schedules.delete', $schedule) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <p>Are you sure you want to delete this schedule?</p>

                    <a href="{{ route('schedules.index') }}" class="btn blue-grey lighten-5 waves-effect waves-light black-text">No</a>
                    <button class="btn waves-effect waves-light">Yes</button>
                </form>
            </div>
        </div>
    </div>
@endsection
