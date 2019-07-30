@if (isset($runs) && $runs->isNotEmpty())
    <table class="responsive-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>User</th>
                <th>Total Coffees
                <th>Volunteers</th>
                @if (request()->user()->isAdmin())
                    <th>Actions</th>
                @endif
            </tr>
        </thead>

        <tbody>
            @forelse($runs as $run)
                <tr>
                    {{-- <td>{{ $run->id}}</td> --}}
                    <td>{{ $run->created_at->format('h:i A') }}</td>
                    <td>{{ $run->user->full_name }}</td>
                    <td>{{ $run->userCoffees->count() }}</td>
                    <td>
                        {{-- If the user who was selected to do the run sees his own run,
                         then we give them the option to ask for volunteers to cover for them --}}
                        @if (request()->user()->is($run->user) && $run->notExpired())

                            {{-- The user has not asked for volunteers yet, allow them to do so --}}
                            @if (!$run->volunteerRequested())
                                <form action={{ route('dashboard.runs.busy', $run) }} method="POST">
                                    @csrf
                                    <button type="submit" class="waves-effect waves-light btn-small">I'm busy</button>
                                </form>
                            @endif

                            {{-- The user has asked for volunteers but no one has volunteered yet --}}
                            @if ($run->volunteerRequested() && !$run->hasVolunteer())
                                <span class="waves-effect waves-light btn-small disabled">I'm busy</span><br />
                                <small>Volunteer requested...</small>
                            @endif

                            {{-- If someone has volunteered, then show their name --}}
                            @if ($run->hasVolunteer())
                                <span class="badge"><small>{{ $run->volunteer->full_name }}</small></span>
                            @endif

                        @elseif (request()->user()->isNot($run->user) && $run->notExpired())
                            {{-- Allow users to volunteer if a coffee run requires a voolunteer --}}
                            @if ($run->needsVolunteer())
                                <form action={{ route('dashboard.runs.volunteer', $run) }} method="POST">
                                    @csrf
                                    <button type="submit" class="waves-effect waves-light btn-small">Volunteer</button>
                                </form>
                            @endif

                            {{-- If someone has volunteered, then show their name --}}
                            @if ($run->volunteerRequested() && $run->hasVolunteer())
                                <span class="badge"><small>{{ $run->volunteer->full_name }}</small></span>
                            @endif
                        @else
                            @if ($run->hasVolunteer())
                                <span class="badge"><small>{{ $run->volunteer->full_name }}</small></span>
                            @endif
                        @endif
                    </td>
                    @can('pick', $run)
                        <td>
                            <form action={{ route('dashboard.runs.update', $run) }} method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="waves-effect waves-light btn-small tooltipped" data-position="top" data-tooltip="Randomly select a new user to do this run" name="action" value="pick"><i class="tiny material-icons">refresh</i></button>
                            </form>
                        </td>
                    @endcan
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row center-align">
        {{ $runs->links() }}
    </div>
@else
    <p>There are no coffee runs coming up!</p>
@endif
