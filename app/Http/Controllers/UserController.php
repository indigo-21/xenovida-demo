<?php

namespace App\Http\Controllers;

use App\Models\Franchisee;
use App\Models\FranchiseeSource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserServices;
use App\Models\UserLevel;


class UserController extends Controller
{
    public function __construct(UserServices $userServices) {}

    public function getTableData()
    {
        $data = [
            "users" => User::all(),
        ];

        return $data;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->getTableData();

        return view("pages.user.index", $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data["userLevels"] = UserLevel::all();
        return view("pages.user.form", $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, UserServices $userServices)
    {
        $userServices->upsert($request);

        return redirect()
            ->route("user.index")
            ->with("success", "New User created successfully");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);

        $data["userLevels"] = UserLevel::all();
        $data["user"]   = $user;

        return view("pages.user.form", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, UserServices $userServices)
    {
        $fullname = $request->firstname . " " . $request->lastname;

        $userServices->upsert($request, $id);

        return redirect()
            ->route("user.index")
            ->with("success", "$fullname updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $fullname = $user->firstname . " " . $user->lastname;

        $user->delete();

        return [
            "error" => false,
            "message" => "$fullname deleted succesfully"
        ];
    }
}
