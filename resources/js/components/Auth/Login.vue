<template>
  <div class="login-page">
    <div class="login-container">
      <div class="login-card">
        <div class="login-header">
          <div class="logo">
            <i class="fas fa-building"></i>
            <h1>نظام القاعات</h1>
          </div>
          <p class="subtitle">تسجيل الدخول إلى لوحة التحكم</p>
        </div>
        
        <div v-if="error" class="alert alert-danger">
          {{ error }}
        </div>
        
        <form @submit.prevent="handleLogin" class="login-form">
          <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <div class="input-wrapper">
              <i class="fas fa-envelope"></i>
              <input 
                id="email"
                v-model="form.email" 
                type="email" 
                required 
                placeholder="أدخل بريدك الإلكتروني"
                class="form-input"
              >
            </div>
          </div>
          
          <div class="form-group">
            <label for="password">كلمة المرور</label>
            <div class="input-wrapper">
              <i class="fas fa-lock"></i>
              <input 
                id="password"
                v-model="form.password" 
                :type="showPassword ? 'text' : 'password'" 
                required 
                placeholder="أدخل كلمة المرور"
                class="form-input"
              >
              <button 
                type="button" 
                @click="togglePassword" 
                class="password-toggle"
              >
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
          </div>
          
          <div class="form-options">
            <label class="checkbox-wrapper">
              <input type="checkbox" v-model="form.remember">
              <span class="checkmark"></span>
              تذكرني
            </label>
            <a href="#" class="forgot-password">نسيت كلمة المرور؟</a>
          </div>
          
          <button type="submit" class="login-btn" :disabled="loading">
            <i v-if="loading" class="fas fa-spinner fa-spin"></i>
            <span v-else>تسجيل الدخول</span>
          </button>
        </form>
        
        <div class="login-footer">
          <p>ليس لديك حساب؟ <a href="/register" class="register-link">إنشاء حساب جديد</a></p>
          <p class="mt-2">أو <a href="/register-tenant" class="register-link">تسجيل مستأجر جديد</a></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Login',
  data() {
    return {
      form: {
        email: '',
        password: '',
        remember: false
      },
      showPassword: false,
      loading: false,
      error: null,
    }
  },
  methods: {
    togglePassword() {
      this.showPassword = !this.showPassword;
    },
    async handleLogin() {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.post('/api/auth/login', this.form);
        
        const { token, user } = response.data;
        
        console.log('Login successful, token:', token);
        console.log('User:', user);
        
        localStorage.setItem('auth_token', token);
        localStorage.setItem('user', JSON.stringify(user));
        
        // إعادة تعيين الـ token في axios headers
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        
        console.log('Token saved to localStorage and axios headers');
        
        this.$router.push('/dashboard');
      } catch (error) {
        if (error.response && error.response.data && error.response.data.message) {
            this.error = error.response.data.message;
        } else {
            this.error = 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.';
        }
        console.error('خطأ في تسجيل الدخول:', error);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  direction: rtl;
}

.login-container {
  width: 100%;
  max-width: 400px;
}

.login-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  padding: 2.5rem;
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.logo {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
}

.logo i {
  font-size: 2rem;
  color: #667eea;
  margin-left: 0.5rem;
}

.logo h1 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: bold;
  color: #333;
}

.subtitle {
  color: #666;
  margin: 0;
  font-size: 0.9rem;
}

.login-form {
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #333;
  font-size: 0.9rem;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-wrapper i {
  position: absolute;
  right: 1rem;
  color: #999;
  z-index: 1;
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  padding-right: 2.5rem;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: #f8f9fa;
}

.form-input:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.password-toggle {
  position: absolute;
  left: 1rem;
  background: none;
  border: none;
  color: #999;
  cursor: pointer;
  padding: 0.25rem;
}

.password-toggle:hover {
  color: #667eea;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.checkbox-wrapper {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-size: 0.9rem;
  color: #666;
}

.checkbox-wrapper input[type="checkbox"] {
  margin-left: 0.5rem;
}

.forgot-password {
  color: #667eea;
  text-decoration: none;
  font-size: 0.9rem;
}

.forgot-password:hover {
  text-decoration: underline;
}

.login-btn {
  width: 100%;
  padding: 0.875rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.login-footer {
  text-align: center;
  padding-top: 1.5rem;
  border-top: 1px solid #e1e5e9;
}

.login-footer p {
  margin: 0;
  color: #666;
  font-size: 0.9rem;
}

.register-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.register-link:hover {
  text-decoration: underline;
}

.alert {
  padding: 0.75rem 1.25rem;
  margin-bottom: 1rem;
  border: 1px solid transparent;
  border-radius: 0.25rem;
}

.alert-danger {
  color: #721c24;
  background-color: #f8d7da;
  border-color: #f5c6cb;
}

@media (max-width: 480px) {
  .login-card {
    padding: 2rem 1.5rem;
  }
  
  .form-options {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
}
</style> 