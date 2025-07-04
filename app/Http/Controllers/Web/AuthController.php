<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\StaffUser;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // إعادة توجيه إلى الصفحة الرئيسية حيث Vue.js سيتعامل مع التوجيه
        return redirect('/');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        Log::info('Web login attempt', ['email' => $request->email]);

        // البحث عن المستخدم في جدول staff_users
        $user = StaffUser::where('email', $request->email)->first();

        if (!$user) {
            Log::warning('User not found', ['email' => $request->email]);
            return back()->withErrors([
                'email' => 'البريد الإلكتروني غير موجود'
            ])->withInput();
        }

        if (!$user->is_active) {
            Log::warning('User account is inactive', ['email' => $request->email]);
            return back()->withErrors([
                'email' => 'الحساب غير مفعل'
            ])->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {
            Log::warning('Invalid password', ['email' => $request->email]);
            return back()->withErrors([
                'password' => 'كلمة المرور غير صحيحة'
            ])->withInput();
        }

        // تسجيل الدخول
        Auth::login($user, $request->has('remember'));

        // تحديث آخر تسجيل دخول
        $user->update(['last_login_at' => now()]);

        Log::info('User logged in successfully', ['email' => $request->email]);

        return redirect()->intended('/dashboard')->with('success', 'تم تسجيل الدخول بنجاح');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح');
    }

    public function showRegisterForm()
    {
        // إعادة توجيه إلى الصفحة الرئيسية حيث Vue.js سيتعامل مع التوجيه
        return redirect('/');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff_users,email',
            'password' => 'required|confirmed|min:6',
            'role' => 'required|in:tenant,admin',
        ]);

        $user = StaffUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // إذا كان مستأجر، أنشئ قاعدة بيانات جديدة وأرسل مهمة للكرون جوب
        if ($request->role === 'tenant') {
            // إنشاء اسم قاعدة البيانات: halls_ + اسم المشروع (من البريد الإلكتروني)
            $projectName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->email));
            $dbName = 'halls_' . $projectName;
            
            // سجل طلب الإنشاء في جدول خاص للكرون جوب
            DB::table('tenant_requests')->insert([
                'user_id' => $user->id,
                'db_name' => $dbName,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // تسجيل الدخول تلقائياً
        Auth::login($user);
        return redirect('/dashboard')->with('success', 'تم التسجيل بنجاح!');
    }
}
