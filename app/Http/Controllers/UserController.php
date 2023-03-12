<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => $validator->messages()->all(),
            ], 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);


        $token = JWTAuth::fromUser($user);


        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email'    => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error'   => $validator->messages()->all(),
            ], 400);
        }


        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'invalid_credentials'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'could_not_create_token',
                'error'   => $e->getMessage(),
            ], 500);
        }

        $user = User::select('id', 'name', 'email')->where('email', $credentials['email'])->first();

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => $user,
        ], 200);
    }


    public function expireToken(Request $request)
    {
        $token = JWTAuth::getToken();
        try {
            JWTAuth::invalidate($token);
            return response()->json([
                'success' => true,
                'message' => 'Logout success',
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    public function updateUser(Request $request)
    {
        try {
            $values = $request->all();

            $validator = Validator::make($values, [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|string|max:255|unique:users,email,' . $values['id'],
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error'   => $validator->messages()->all(),
                ], 400);
            }

            $user = User::where('id', $values['id'])->first();
            if (empty($user->id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not found',
                ], 400);
            }

            $user->name = $values['name'];
            $user->email = $values['email'];
            $user->password =  Hash::make($values['password']);
            $user->save();


            return response()->json([
                'success' => true,
                'user'    => [
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
            ], 201);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'success'  => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::where('id', $id)->first();

            if (empty($user->id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not found',
                ], 400);
            }

            User::where('id', $user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ], 200);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'success'  => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUser($id)
    {
        try {
            $user = User::where('id', $id)->first();

            if (empty($user->id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not found',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'user' => $user,
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'success'  => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAllUsers(Request $request)
    {
        try {
            $users = User::all();

            return response()->json([
                'success' => true,
                'users' => $users,
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                'success'  => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
