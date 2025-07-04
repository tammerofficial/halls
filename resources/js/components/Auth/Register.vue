<template>
  <div class="register-page">
    <div class="register-container">
      <div class="register-card">
        <div class="register-header">
          <div class="logo">
            <i class="fas fa-user-plus"></i>
            <h1>إنشاء حساب جديد</h1>
          </div>
          <p class="subtitle">انضم إلينا وابدأ في إدارة قاعاتك</p>
        </div>

        <div v-if="error" class="alert alert-danger">
            <ul v-if="typeof error === 'object'">
                <li v-for="(messages, field) in error" :key="field">
                    {{ messages.join(', ') }}
                </li>
            </ul>
            <p v-else>{{ error }}</p>
        </div>
        
        <form @submit.prevent="handleRegister" class="register-form">
          <div class="form-group">
            <label for="name">الاسم الكامل</label>
            <div class="input-wrapper">
              <i class="fas fa-user"></i>
              <input 
                id="name"
                v-model="form.name" 
                type="text" 
                required 
                placeholder="أدخل اسمك الكامل"
                class="form-input"
              >
            </div>
          </div>

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
                type="password" 
                required 
                placeholder="أدخل كلمة المرور"
                class="form-input"
              >
            </div>
          </div>

           <div class="form-group">
            <label for="password_confirmation">تأكيد كلمة المرور</label>
            <div class="input-wrapper">
              <i class="fas fa-lock"></i>
              <input 
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password" 
                required 
                placeholder="أعد إدخال كلمة المرور"
                class="form-input"
              >
            </div>
          </div>
          
          <button type="submit" class="register-btn" :disabled="loading">
            <i v-if="loading" class="fas fa-spinner fa-spin"></i>
            <span v-else>إنشاء الحساب</span>
          </button>
        </form>
        
        <div class="register-footer">
          <p>لديك حساب بالفعل؟ <a href="/login" class="login-link">تسجيل الدخول</a></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      },
      loading: false,
      error: null
    }
  },
  methods: {
    async handleRegister() {
        this.loading = true;
        this.error = null;

        try {
            const response = await axios.post('/api/auth/register', this.form);
            const { token, user } = response.data;

            localStorage.setItem('auth_token', token);
            localStorage.setItem('user', JSON.stringify(user));
            
            this.$router.push('/dashboard');

        } catch (error) {
            if (error.response && error.response.status === 422) {
                this.error = error.response.data.errors;
            } else {
                this.error = 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.';
                console.error('خطأ في التسجيل:', error);
            }
        } finally {
            this.loading = false;
        }
    }
  }
}
</script> 

<style scoped>
.register-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  direction: rtl;
}

.register-container {
  width: 100%;
  max-width: 420px;
}

.register-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  padding: 2.5rem;
}

.register-header {
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

.register-form {
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

.register-btn {
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

.register-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.register-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.register-footer {
  text-align: center;
  padding-top: 1.5rem;
  border-top: 1px solid #e1e5e9;
}

.register-footer p {
  margin: 0;
  color: #666;
  font-size: 0.9rem;
}

.login-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.login-link:hover {
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
.alert-danger ul {
    margin-bottom: 0;
    padding-right: 20px;
}
</style> 