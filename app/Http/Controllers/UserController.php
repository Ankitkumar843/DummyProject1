<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function read()
    {
        $users = User::all();
        return response()->json([
            'message' => 'All user list',
            'status' => true,
            'users' => $users,
            'status' => '1'
        ], 200);
    }

    public function hello(){
        echo "Hello";
    }
    public function create(Request $req)
    {
        // dd($req->toArray());
        $validate = Validator::make($req->all(), ([
            'first_name' => 'required|string|max:200',
            'last_name' => 'required|string|max:200',
            'email' => 'required|email',
            'password' => 'required|max:10|min:8',
            'profile_pic' => 'required|image|mimes:jpg,jpeg,png'
        ]));
        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'status' => false
            ], 401);
        }
        if ($req->hasFile('profile_pic')) {
            $imageName = time() . '.' . $req->profile_pic->getClientOriginalName();
            $profilePicPath = $req->profile_pic->storeAs('profile_pics', $imageName);
        }
        $user = User::create([
            'first_name' => $req->first_name,
            'last_name' => $req->last_name,
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'profile_pic' => $profilePicPath
        ]);
        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user
        ], 200);
    }
    public function update(Request $req, $id)
    {
        // dd($req->toArray());
        $user = User::find($id);
        if (empty($user)) {
            return response()->json([
                'message' => 'User not found',
                'status' => false
            ]);
        }
        $validate = Validator::make($req->all(), ([
            'first_name' => 'required|string|max:200',
            'last_name' => 'required|string|max:200',
            'email' => 'required|email',
            'password' => 'required|max:10|min:8',
            'profile_pic' => 'required|mimes:jpg,jpeg,png|image'
        ]));
        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'status' => false
            ]);
            
        }
        if ($req->hasFile('profile_pic')) {
            $imageName = time() . '.' . $req->profile_pic->getClientOriginalName();
            $profilePicPath = $req->profile_pic->storeAs('profile_pics', $imageName);
        }
        $user->update([
            'first_name' => $req->first_name,
            'last_name' => $req->last_name,
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'profile_pic' => $profilePicPath
        ]);
        return response()->json([
            'message' => 'User updated successfully',
            'status' => true
        ]);
    }
    public function delete($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return response()->json([
                'message' => 'User not found',
                'status' => false
            ], 401);
        }
        $user->delete();
        if ($user) {
            return response()->json([
                'message' => 'User deleted successfully',
                'status' => true,
                'user' => $user
            ], 200);
        }
    }
}
