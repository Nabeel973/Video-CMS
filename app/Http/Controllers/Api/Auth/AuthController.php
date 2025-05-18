<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Default role for new users
            'role' => 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Get user with role and permissions
        $userData = $this->getUserWithRoleAndPermissions($user);

        return response()->json([
            'user' => $userData,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        
        $token = $user->createToken('auth_token')->plainTextToken;
    //  dd($user);
        // Get user with role and permissions
        $userData = $this->getUserWithRoleAndPermissions($user);
   
        // dd($user,$userData);
        return response()->json([
            'user' => $userData,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        // Get user with role and permissions
        $userData = $this->getUserWithRoleAndPermissions($request->user());
        
        return response()->json($userData);
    }

    /**
     * Get user data with role and permissions
     * 
     * @param User $user
     * @return array
     */
    private function getUserWithRoleAndPermissions(User $user)
    {
        // Basic user data
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->roles->first()->name, // Default to 'user' if role is not set
        ];

        // If you're using Spatie Permission package, you can get permissions like this:
        $userData['permissions'] = $user->getAllPermissions()->pluck('name')->toArray();
        
        // If you have a custom permissions system, adjust accordingly
        // For example, if permissions are stored in a JSON column:
        // $userData['permissions'] = $user->permissions ?? [];
        
        // Or if permissions are derived from roles in your own system:
        // $userData['permissions'] = $this->getPermissionsForRole($user->role);

        return $userData;
    }
}
