<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserServices
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function formRule($userLevelId = false, $id = false)
    {
        $data = [
            'firstname' => ['required', 'string', 'min:2', 'max:900'],
            'lastname' => ['required', 'string', 'min:2', 'max:900'],
            'email' => ['required', 'string', 'email', 'min:2', 'max:900', Rule::unique('users')->ignore($id ? $id : '')],
            'access_level' => ['required'],
            'status' => ['required'],
            'username' => ['required', 'string', 'min:2', 'max:900', Rule::unique('users')->ignore($id ? $id : '')],
        ];

        if (! $id) {
            $data['password'] = ['required', 'confirmed', Password::min(8)->mixedCase()];
        }

        return $data;
    }

    public function upsert(Request $request, $id = false)
    {

        $firstname = $request->firstname;
        $lastname = $request->lastname;
        $email = $request->email;
        $userLevelId = $request->access_level;
        $isActive = $request->status;
        $username = $request->username;
        $password = $request->password ? Hash::make($request->password) : false;

        $request->validate($this->formRule($userLevelId, $id));

        $user = ! $id ? new User : User::findOrFail($id);

        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->email = $email;
        $user->user_level_id = $userLevelId;
        $user->is_active = $isActive;
        $user->username = $username;

        if (! $id || $password) {
            $user->password = $password;
        }

        $user->{! $id ? 'created_by' : 'updated_by'} = Auth::id();

        $result = $user->save();

        return ['success' => $result];
    }
}
