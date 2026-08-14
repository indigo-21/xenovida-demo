<?php

namespace App\Services;

use App\Models\UserLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserLevelServices
{
    public function __construct() {}

    /**
     * Build validation rules for create / update.
     */
    public function formRules(int|false $id = false): array
    {
        return [
            'description' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('user_levels', 'description')
                    ->whereNull('deleted_at')
                    ->ignore($id ?: null),
            ],
        ];
    }

    /**
     * Create or update a UserLevel record.
     *
     * @return array{success: bool, userLevel: UserLevel}
     */
    public function upsert(Request $request, int|false $id = false): array
    {
        $request->validate($this->formRules($id));

        $userLevel = $id ? UserLevel::findOrFail($id) : new UserLevel;

        $userLevel->description = $request->description;
        $userLevel->{$id ? 'updated_by' : 'created_by'} = Auth::id();

        $result = $userLevel->save();

        return ['success' => $result, 'userLevel' => $userLevel];
    }
}
