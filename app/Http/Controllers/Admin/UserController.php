<?php

namespace App\Http\Controllers\Admin;

use Picker\{Cup, User, Coffee};
use Picker\User\Notifications\UserInvited;
use Picker\User\Requests\{CreateUser, UpdateUser, InviteUser};
use App\Http\Controllers\Controller;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\{Notification, URL};
use Illuminate\Http\{Response, Request, RedirectResponse};

class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @return Response
     */
    public function index() : Response
    {
        $users = User::paginate(3);

        return response()->view('admin.users.index', compact('users'));
    }

    /**
     * Display the form to invite users to the application.
     *
     * @return Response
     */
    public function invitation() : Response
    {
        return response()->view('admin.users.invite');
    }

    /**
     * Send an invitation email to the specified email
     *
     * @param InviteUser  $request
     * @return Response
     */
    public function invite(InviteUser $request)
    {
        $email = $request->input('email');
        $expires = now()->addDays(2);

        $url = URL::temporarySignedRoute('register', $expires, [
            'email' => $email
        ]);

        Notification::route('mail', $email)->notify(new UserInvited($url));

        $this->messages->add('invited', trans('messages.user.updated'));

        return back()->withSuccess($this->messages);
    }

    /**
     * Display the form for editing a user resource.
     *
     * @param User $user
     * @return Response
     */
    public function edit(User $user) : Response
    {
        return response()->view('admin.users.edit', compact('user'));
    }

    /**
     * Update a user account.
     *
     * @param UpdateUser $request
     * @param User $user
     * @return RedirectResponse
     */
    public function update(UpdateUser $request, User $user) : RedirectResponse
    {
        $user->update($request->only(['first_name','last_name']));

        $this->messages->add('updated', trans('messages.user.updated'));

        return redirect()
                ->route('users.index')
                ->withSuccess($this->messages);
    }

    /**
     * Delete a specfied user resource
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user) : RedirectResponse
    {
        $user->delete();

        $this->messages->add('deleted', trans('messages.user.deleted'));

        return redirect()
                ->route('users.index')
                ->withSuccess($this->messages);
    }
}