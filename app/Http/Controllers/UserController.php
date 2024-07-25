<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * set user role
     *
     */
    public function setRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:user,admin',
        ]);

        $updated = User::where('id', $request->user_id)->update([
            'role' => $request->role,
        ]);

        if (!$updated) {
            return response()->json(['message' => 'Failed to promote user'], 400);
        }
        
        // Revoke existing tokens
        $user = User::find($request->user_id);
        $user->tokens()->delete();

        return response()->json(['message' => 'User has been promoted to admin'], 200);
    }
}
