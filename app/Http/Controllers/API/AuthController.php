<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|unique:users,username',
                'password' => 'required|min:6',
                'role' => 'required',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:parents,email',
                'contactNo' => 'required|string|max:20'
            ]);

            // Create user
            $user = User::create([
                'username' => $request->username,
                'password_hash' => Hash::make($request->password),
                'role' => $request->role
            ]);

            // If registering as parent, create parent record
            $profile = null;
            if ($request->role === 'parent') {
                $parentId = DB::table('parents')->insertGetId([
                    'user_id' => $user->user_id,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'contactNo' => $request->contactNo
                ]);

                $profile = DB::table('parents')->where('parent_id', $parentId)->first();
            }

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'profile' => $profile,
                'success' => true
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'success' => false
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed: ' . $e->getMessage(),
                'success' => false
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Get parent profile if user is a parent
        $userInfo = null;
        if ($user->role === 'parent') {
            $userInfo = DB::table('parents')
                ->where('user_id', $user->user_id)
                ->first();
        } elseif ($user->role === 'teacher') {
            $userInfo = DB::table('teachers')
                ->where('user_id', $user->user_id)
                ->first();
        }

        // Create token for mobile app
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'profile' => $userInfo,
            'token' => $token,
            'success' => true
        ]);
    }
    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logged out successfully',
            'success' => true
        ]);
    }
}