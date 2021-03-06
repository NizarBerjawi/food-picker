@if ($schedules->isNotEmpty())
<table class="responsive-table">
    <thead>
        <tr>
            <th>Time</th>
            <th>Days</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        @foreach($schedules as $schedule)
            <tr>
                <td><a href="{{ route('admin.schedules.show', $schedule) }}">{{ $schedule->time }}</a></td>
                <td>{{ $schedule->getFormattedDays() }}</td>
                <td>
                    <a href="{{ route('admin.schedules.edit', $schedule) }}" class="btn-floating btn-small grey lighten-4"><i class="tiny material-icons teal-text">edit</i></a>
                    <a href="{{ route('admin.schedules.confirm-destroy', $schedule) }}" class="btn-floating btn-small grey lighten-4"><i class="tiny material-icons teal-text">delete</i></a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="row center-align">
    {{ $schedules->links() }}
</div>

@else
    <p>You don't have any schedules!</p>
@endif
