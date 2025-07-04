<template>
  <div class="register-tenant-container">
    <div class="register-tenant-box">
      <!-- Promotional Side (Left) -->
      <div class="promo-side">
        <h2 class="promo-title">أطلق العنان لإمكانيات قاعاتك</h2>
        <p class="promo-subtitle">انضم إلينا وحوّل طريقة إدارتك للحجوزات والمناسبات إلى تجربة رقمية متكاملة.</p>
        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-tasks"></i></div>
            <h3 class="feature-title">إدارة شاملة</h3>
            <p class="feature-desc">تحكم كامل في القاعات، الحجوزات، العملاء، والخدمات من لوحة تحكم واحدة.</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-calendar-check"></i></div>
            <h3 class="feature-title">حجوزات ذكية</h3>
            <p class="feature-desc">نظام حجوزات مرن وتقويم تفاعلي يمنع أي تعارض في المواعيد.</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
            <h3 class="feature-title">تقارير دقيقة</h3>
            <p class="feature-desc">احصل على رؤى فورية حول أداء مشروعك المالي ونمو أعمالك.</p>
          </div>
          <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <h3 class="feature-title">فوترة سهلة</h3>
            <p class="feature-desc">أنشئ وأرسل فواتير احترافية لعملائك وتتبع المدفوعات بسهولة.</p>
          </div>
        </div>
      </div>

      <!-- Form Side (Right) -->
      <div class="form-side">
        <div class="card-header">
          <h1 class="card-title">إنشاء حساب جديد</h1>
          <p class="card-subtitle">خطوتك الأولى نحو إدارة أفضل</p>
        </div>

        <form @submit.prevent="registerTenant" class="form">
          <div class="form-group">
            <i class="fas fa-user-tie form-icon"></i>
            <input v-model="form.name" type="text" placeholder="اسم المستأجر (الشركة)" class="form-input" required />
          </div>
          <div class="form-row">
            <div class="form-group">
              <i class="fas fa-envelope form-icon"></i>
              <input v-model="form.email" type="email" placeholder="البريد الإلكتروني" class="form-input" required />
            </div>
            <div class="form-group">
              <i class="fas fa-phone form-icon"></i>
              <input v-model="form.phone" type="text" placeholder="رقم الجوال" class="form-input" required />
            </div>
          </div>
          <div class="form-group">
            <i class="fas fa-database form-icon"></i>
            <input v-model="form.project" type="text" placeholder="اسم المشروع (حروف إنجليزية فقط)" class="form-input" required pattern="^[a-zA-Z0-9_]+$" />
            <small class="db-name-preview">سيتم إنشاء قاعدة بيانات باسم: <strong>hall_{{ form.project || 'project' }}</strong></small>
          </div>
          <div class="form-group">
            <i class="fas fa-box-open form-icon"></i>
            <select v-model="form.package_id" class="form-input" required>
              <option value="" disabled selected>اختر الباقة المناسبة</option>
              <option v-for="pkg in packages" :key="pkg.id" :value="pkg.id">
                {{ pkg.name }} - {{ pkg.price_monthly }} ريال / شهري
              </option>
            </select>
            <div class="select-arrow">
              <i class="fas fa-chevron-down"></i>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <i class="fas fa-lock form-icon"></i>
              <input v-model="form.password" type="password" placeholder="كلمة المرور" class="form-input" required minlength="6" />
            </div>
            <div class="form-group">
              <i class="fas fa-lock form-icon"></i>
              <input v-model="form.password_confirmation" type="password" placeholder="تأكيد كلمة المرور" class="form-input" required minlength="6" />
            </div>
          </div>

          <div v-if="error" class="error-message">{{ error }}</div>
          
          <button type="submit" class="submit-btn" :disabled="loading">
            <span v-if="loading">
              <i class="fas fa-spinner fa-spin"></i>
              جاري التسجيل...
            </span>
            <span v-else>
              <i class="fas fa-user-plus"></i>
              إنشاء الحساب
            </span>
          </button>
        </form>

        <div class="login-redirect">
          لديك حساب بالفعل؟ <router-link to="/login" class="link">تسجيل الدخول</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'RegisterTenant',
  data() {
    return {
      packages: [],
      form: {
        name: '',
        phone: '',
        email: '',
        project: '',
        package_id: '',
        password: '',
        password_confirmation: ''
      },
      loading: false,
      error: '',
      cursor: {
        x: 0,
        y: 0
      }
    }
  },
  mounted() {
    this.loadPackages()
    this.initCursorEffect()
  },
  methods: {
    async loadPackages() {
      try {
        const response = await axios.get('/api/packages')
        if (response.data.success) {
          this.packages = response.data.data
        }
      } catch (e) {
        this.error = 'تعذر تحميل الباقات'
      }
    },
    async registerTenant() {
      this.error = ''
      if (this.form.password !== this.form.password_confirmation) {
        this.error = 'كلمتا المرور غير متطابقتين'
        return
      }
      this.loading = true
      try {
        const formData = {
          name: this.form.name,
          phone: this.form.phone,
          email: this.form.email,
          project: this.form.project,
          package_id: this.form.package_id,
          password: this.form.password,
          password_confirmation: this.form.password_confirmation
        }

        const response = await axios.post('/api/tenants/register', formData)
        
        if (response.data.success) {
          // نجح التسجيل
          alert('تم تسجيل المستأجر بنجاح! يمكنك الآن تسجيل الدخول.')
          this.$router.push('/auth/login')
        }
      } catch (e) {
        console.error('Registration error:', e)
        if (e.response?.data?.errors) {
          const errorMessages = Object.values(e.response.data.errors).flat()
          this.error = errorMessages.join(', ')
        } else {
          this.error = e.response?.data?.message || 'حدث خطأ أثناء التسجيل'
        }
      } finally {
        this.loading = false
      }
    },
    initCursorEffect() {
      const cursor = document.createElement('div')
      cursor.classList.add('custom-cursor')
      document.body.appendChild(cursor)
      
      const promoSide = this.$el.querySelector('.promo-side')
      const featureCards = this.$el.querySelectorAll('.feature-card')
      
      if (promoSide) {
        promoSide.addEventListener('mousemove', (e) => {
          const rect = promoSide.getBoundingClientRect()
          const x = e.clientX - rect.left
          const y = e.clientY - rect.top
          
          cursor.style.left = `${e.clientX}px`
          cursor.style.top = `${e.clientY}px`
          cursor.classList.add('active')
        })
        
        promoSide.addEventListener('mouseleave', () => {
          cursor.classList.remove('active')
        })
      }
      
      featureCards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
          const rect = card.getBoundingClientRect()
          const x = e.clientX - rect.left
          const y = e.clientY - rect.top
          
          cursor.style.left = `${e.clientX}px`
          cursor.style.top = `${e.clientY}px`
          cursor.classList.add('active')
        })
        
        card.addEventListener('mouseleave', () => {
          cursor.classList.remove('active')
        })
      })
    }
  }
}
</script>

<style scoped>
.register-tenant-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f0f2f5;
  padding: 2rem;
}

.register-tenant-box {
  width: 100%;
  max-width: 1200px;
  background: white;
  border-radius: 1.5rem;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  display: flex;
  overflow: hidden;
  animation: popIn 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
}

@keyframes popIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.promo-side {
  width: 45%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 4rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  position: relative;
  overflow: hidden;
  animation: backgroundShift 15s ease-in-out infinite alternate;
  cursor: none;
}

.promo-side::before, 
.feature-card::before {
  content: "";
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0) 60%);
  animation: rotateGlow 10s linear infinite;
  z-index: 1;
  pointer-events: none;
  will-change: transform;
}

.promo-side::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 70% 30%, rgba(199, 125, 255, 0.6) 0%, rgba(75, 0, 130, 0) 50%);
  animation: pulseGlow 4s ease-in-out infinite;
  z-index: 1;
  pointer-events: none;
  will-change: opacity, transform;
}

@keyframes backgroundShift {
  0% {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  }
  50% {
    background: linear-gradient(135deg, #7a5cff 0%, #8b5fbf 100%);
  }
  100% {
    background: linear-gradient(135deg, #5e72eb 0%, #9046c9 100%);
  }
}

@keyframes rotateGlow {
  0% {
    transform: translate(-25%, -25%) rotate(0deg);
  }
  100% {
    transform: translate(-25%, -25%) rotate(360deg);
  }
}

@keyframes pulseGlow {
  0% {
    opacity: 0.3;
    transform: scale(0.8);
  }
  50% {
    opacity: 0.7;
    transform: scale(1.2);
  }
  100% {
    opacity: 0.3;
    transform: scale(0.8);
  }
}

.promo-title, .promo-subtitle, .features-grid {
  position: relative;
  z-index: 2;
}

.promo-title {
  font-size: 2.25rem;
  font-weight: 700;
  margin-bottom: 1rem;
  position: relative;
  z-index: 2;
}

.promo-subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
  line-height: 1.6;
  margin-bottom: 3rem;
  position: relative;
  z-index: 2;
}

.features-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  position: relative;
  z-index: 2;
}

.feature-card {
  background: rgba(255, 255, 255, 0.1);
  padding: 1.5rem;
  border-radius: 1rem;
  transition: all 0.5s ease;
  position: relative;
  overflow: hidden;
  backdrop-filter: blur(5px);
  z-index: 2;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.1);
  cursor: none;
}

.feature-card::before {
  content: "";
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.4) 0%, rgba(255, 255, 255, 0) 60%);
  transform: rotate(30deg);
  opacity: 0;
  transition: opacity 0.5s ease, transform 0.8s ease;
  z-index: -1;
}

.feature-card:hover {
  background: rgba(255, 255, 255, 0.15);
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
  border-color: rgba(255, 255, 255, 0.3);
}

.feature-card:hover::before {
  opacity: 1;
  animation: cardGlow 3s ease-in-out infinite;
}

@keyframes cardGlow {
  0% {
    opacity: 0.3;
    transform: rotate(30deg) scale(0.9);
  }
  50% {
    opacity: 0.7;
    transform: rotate(30deg) scale(1.1);
  }
  100% {
    opacity: 0.3;
    transform: rotate(30deg) scale(0.9);
  }
}

.feature-icon {
  font-size: 2rem;
  margin-bottom: 1rem;
  color: #c7d2fe;
}

.feature-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.feature-desc {
  font-size: 0.9rem;
  opacity: 0.8;
  line-height: 1.5;
}

.form-side {
  width: 55%;
  padding: 4rem;
  overflow-y: auto;
}

.card-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.card-title {
  font-size: 1.75rem;
  font-weight: bold;
  color: #1a202c;
}

.card-subtitle {
  font-size: 1rem;
  color: #718096;
  margin-top: 0.5rem;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-row {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 1rem;
}

.form-row .form-group {
  flex: 1;
  min-width: 0;
}

.form-group {
  margin-bottom: 1.5rem;
  position: relative;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  padding-right: 2.5rem;
  border: 2px solid #e2e8f0;
  border-radius: 0.5rem;
  font-size: 1rem;
  transition: all 0.3s ease;
  background-color: #f8fafc;
  color: #1a202c;
  direction: rtl;
}

.form-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-icon {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #a0aec0;
  font-size: 1rem;
  pointer-events: none;
}

.db-name-preview {
  display: block;
  font-size: 0.75rem;
  color: #718096;
  margin-top: 0.25rem;
  text-align: right;
}

.db-name-preview strong {
  color: #4a5568;
  font-weight: 600;
  direction: ltr;
  display: inline-block;
}

.error-message {
  background-color: #fed7d7;
  color: #c53030;
  padding: 0.75rem;
  border-radius: 0.5rem;
  margin-bottom: 1rem;
  text-align: right;
  font-size: 0.875rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.error-message::before {
  content: "\f071";
  font-family: "Font Awesome 5 Free";
  font-weight: 900;
}

.submit-btn {
  width: 100%;
  padding: 0.875rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.login-redirect {
  margin-top: 2rem;
  text-align: center;
  font-size: 0.875rem;
  color: #718096;
}

.link {
  color: #667eea;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s ease;
}

.link:hover {
  color: #5a67d8;
  text-decoration: underline;
}

.select-arrow {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #a0aec0;
  pointer-events: none;
}

select.form-input {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  padding-left: 2.5rem;
  cursor: pointer;
}

@media (max-width: 768px) {
  .register-tenant-box {
    flex-direction: column;
  }
  
  .promo-side, .form-side {
    width: 100%;
    padding: 2rem;
  }
  
  .form-row {
    flex-direction: column;
    gap: 0;
  }
  
  .form-row .form-group {
    margin-bottom: 1.5rem;
  }
}

.custom-cursor {
  position: fixed;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(199, 125, 255, 0.8) 0%, rgba(75, 0, 130, 0.4) 50%, transparent 70%);
  transform: translate(-50%, -50%);
  pointer-events: none;
  z-index: 9999;
  opacity: 0;
  transition: opacity 0.2s ease, width 0.2s ease, height 0.2s ease;
  box-shadow: 0 0 15px 5px rgba(199, 125, 255, 0.6);
  filter: blur(2px);
}

.custom-cursor.active {
  opacity: 1;
  width: 40px;
  height: 40px;
  animation: cursorPulse 2s infinite alternate;
}

@keyframes cursorPulse {
  0% {
    transform: translate(-50%, -50%) scale(1);
    box-shadow: 0 0 15px 5px rgba(199, 125, 255, 0.6);
  }
  100% {
    transform: translate(-50%, -50%) scale(1.2);
    box-shadow: 0 0 25px 8px rgba(199, 125, 255, 0.8);
  }
}
</style> 