<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaffUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        Log::info('Login attempt', ['email' => $request->email]);
        
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = StaffUser::where('email', $request->email)->first();

        if (!$user) {
            Log::warning('User not found', ['email' => $request->email]);
            throw ValidationException::withMessages([
                'email' => ['المستخدم غير موجود.'],
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            Log::warning('Invalid password', ['email' => $request->email]);
            throw ValidationException::withMessages([
                'password' => ['كلمة المرور غير صحيحة.'],
            ]);
        }

        // التحقق من is_active إذا كان العمود موجود
        if (isset($user->is_active) && !$user->is_active) {
            Log::warning('Inactive user login attempt', ['email' => $request->email]);
            throw ValidationException::withMessages([
                'email' => ['الحساب غير مفعل. تواصل مع الإدارة.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;
        
        Log::info('Login successful', ['user_id' => $user->id]);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'تم تسجيل الدخول بنجاح'
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:staff_users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = StaffUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // إضافة المستخدم إلى جدول العملاء إذا لم يكن موجوداً
        if (!DB::table('customers')->where('email', $request->email)->exists()) {
            DB::table('customers')->insert([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone ?? '',
                'address' => $request->address ?? '',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
