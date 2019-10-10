@if ($users->isNotEmpty())
<table class="responsive-table">
    <thead>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->first_name }}</td>
                <td>{{ $user->last_name }}</td>
                <td>{{ $user->email }}</td>

                <td>
                    <a class="btn-floating btn-small grey lighten-4" href="{{ route('users.coffees.index', $user) }}"><i class="tiny material-icons teal-text">local_cafe</i></a>
                    <a class="btn-floating btn-small grey lighten-4" href="{{ route('users.edit', $user) }}"><i class="tiny material-icons teal-text">edit</i></a>
                    <a class="btn-floating btn-small grey lighten-4" href="{{ route('users.confirm-destroy', $user) }}"><i class="tiny material-icons teal-text">delete</i></a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="row center-align">
    {{ $users->links() }}
</div>
@else
    <p>No one wants coffee!</p>
@endif
