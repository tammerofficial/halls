<template>
  <div class="landing-page">
    <!-- Header -->
    <header class="header">
      <nav class="navbar">
        <div class="nav-container">
          <div class="nav-logo">
            <i class="fas fa-building"></i>
            <span>نظام القاعات</span>
          </div>
          <div class="nav-links">
            <a href="#features" class="nav-link">المميزات</a>
            <a href="#pricing" class="nav-link">الأسعار</a>
            <a href="#screenshots" class="nav-link">الشاشات</a>
            <a href="#contact" class="nav-link">اتصل بنا</a>
            <router-link to="/auth/login" class="nav-link login-btn">تسجيل الدخول</router-link>
          </div>
        </div>
      </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-container">
        <div class="hero-content">
          <h1 class="hero-title">
            نظام إدارة القاعات
            <span class="highlight">الأكثر تطوراً</span>
          </h1>
          <p class="hero-subtitle">
            حل متكامل لإدارة قاعات الأحداث والحفلات مع واجهة سهلة الاستخدام وميزات متقدمة
          </p>
          <div class="hero-buttons">
            <router-link to="/register-tenant" class="btn btn-primary">
              <i class="fas fa-rocket"></i>
              ابدأ الآن مجاناً
            </router-link>
            <button @click="scrollToSection('features')" class="btn btn-secondary">
              <i class="fas fa-play"></i>
              شاهد المميزات
            </button>
          </div>
        </div>
        <div class="hero-image">
          <div class="floating-card">
            <i class="fas fa-calendar-check"></i>
            <span>حجز سريع</span>
          </div>
          <div class="floating-card">
            <i class="fas fa-chart-line"></i>
            <span>تقارير متقدمة</span>
          </div>
          <div class="floating-card">
            <i class="fas fa-users"></i>
            <span>إدارة العملاء</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">مميزات النظام</h2>
          <p class="section-subtitle">اكتشف القوة الحقيقية لنظام إدارة القاعات</p>
        </div>
        
        <div class="features-grid">
          <div class="feature-card" v-for="feature in features" :key="feature.id">
            <div class="feature-icon">
              <i :class="feature.icon"></i>
            </div>
            <h3 class="feature-title">{{ feature.title }}</h3>
            <p class="feature-description">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">خطط الأسعار</h2>
          <p class="section-subtitle">اختر الخطة المناسبة لاحتياجاتك</p>
        </div>
        
        <div class="pricing-grid">
          <div class="pricing-card" v-for="pkg in packages" :key="pkg.id" :class="{ 'popular': pkg.isPopular }">
            <div v-if="pkg.isPopular" class="popular-badge">
              <i class="fas fa-star"></i>
              الأكثر شعبية
            </div>
            
            <div class="pricing-header">
              <div class="package-icon">
                <i :class="pkg.icon || 'fas fa-box'"></i>
              </div>
              <h3 class="package-name">{{ pkg.name }}</h3>
              <p class="package-description">{{ pkg.description || 'باقة مميزة مع خدمات شاملة' }}</p>
            </div>
            
            <div class="package-price">
              <span class="price">{{ pkg.price_monthly || pkg.price }}</span>
              <span class="currency">د.ك</span>
              <span class="period">/شهرياً</span>
            </div>
            
            <div class="package-features">
              <div v-for="feature in pkg.features" :key="feature.id || feature.name" class="feature-item">
                <i class="fas fa-check-circle" :class="{ 'text-muted': !feature.included }"></i>
                <span :class="{ 'text-muted line-through': !feature.included }">
                  {{ feature.name || getFeatureName(feature) }}
                </span>
                <span v-if="feature.value" class="feature-value">{{ feature.value }}</span>
              </div>
            </div>
            
            <router-link :to="`/register-tenant?package=${pkg.id}`" class="btn btn-primary btn-block">
              <i class="fas fa-rocket"></i>
              ابدأ الآن
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Screenshots Section -->
    <section id="screenshots" class="screenshots">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">شاشات النظام</h2>
          <p class="section-subtitle">تعرف على واجهة النظام من خلال هذه الشاشات</p>
        </div>
        
        <div class="screenshots-slider">
          <div class="slider-container">
            <div class="slider-track" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
              <div class="slide" v-for="(screenshot, index) in screenshots" :key="index">
                <div class="screenshot-card">
                  <div class="screenshot-placeholder">
                    <i class="fas fa-desktop"></i>
                    <h4>{{ screenshot.title }}</h4>
                    <p>{{ screenshot.description }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="slider-controls">
            <button @click="prevSlide" class="slider-btn" :disabled="currentSlide === 0">
              <i class="fas fa-chevron-right"></i>
            </button>
            <div class="slider-dots">
              <button 
                v-for="(screenshot, index) in screenshots" 
                :key="index"
                @click="goToSlide(index)"
                class="dot"
                :class="{ active: currentSlide === index }"
              ></button>
            </div>
            <button @click="nextSlide" class="slider-btn" :disabled="currentSlide === screenshots.length - 1">
              <i class="fas fa-chevron-left"></i>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">اتصل بنا</h2>
          <p class="section-subtitle">نحن هنا لمساعدتك في أي وقت</p>
        </div>
        
        <div class="contact-content">
          <div class="contact-info">
            <div class="contact-item">
              <i class="fas fa-envelope"></i>
              <div>
                <h4>البريد الإلكتروني</h4>
                <p>info@halls-system.com</p>
              </div>
            </div>
            <div class="contact-item">
              <i class="fas fa-phone"></i>
              <div>
                <h4>الهاتف</h4>
                <p>+965 123 456 789</p>
              </div>
            </div>
            <div class="contact-item">
              <i class="fas fa-map-marker-alt"></i>
              <div>
                <h4>العنوان</h4>
                <p>الكويت، مدينة الكويت</p>
              </div>
            </div>
          </div>
          
          <div class="contact-form">
            <form @submit.prevent="submitContact">
              <div class="form-group">
                <input v-model="contactForm.name" type="text" placeholder="الاسم الكامل" required>
              </div>
              <div class="form-group">
                <input v-model="contactForm.email" type="email" placeholder="البريد الإلكتروني" required>
              </div>
              <div class="form-group">
                <textarea v-model="contactForm.message" placeholder="رسالتك" rows="5" required></textarea>
              </div>
              <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-paper-plane"></i>
                إرسال الرسالة
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-section">
            <div class="footer-logo">
              <i class="fas fa-building"></i>
              <span>نظام القاعات</span>
            </div>
            <p>الحل الأمثل لإدارة قاعات الأحداث والحفلات</p>
          </div>
          
          <div class="footer-section">
            <h4>روابط سريعة</h4>
            <ul>
              <li><a href="#features">المميزات</a></li>
              <li><a href="#pricing">الأسعار</a></li>
              <li><a href="#screenshots">الشاشات</a></li>
              <li><router-link to="/register-tenant">التسجيل</router-link></li>
            </ul>
          </div>
          
          <div class="footer-section">
            <h4>الدعم</h4>
            <ul>
              <li><a href="#contact">اتصل بنا</a></li>
              <li><a href="#">الدليل الإرشادي</a></li>
              <li><a href="#">الأسئلة الشائعة</a></li>
              <li><a href="#">الدعم الفني</a></li>
            </ul>
          </div>
        </div>
        
        <div class="footer-bottom">
          <p>&copy; 2025 نظام القاعات. جميع الحقوق محفوظة.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'LandingPage',
  data() {
    return {
      currentSlide: 0,
      packages: [],
      contactForm: {
        name: '',
        email: '',
        message: ''
      },
      features: [
        {
          id: 1,
          icon: 'fas fa-calendar-alt',
          title: 'إدارة الحجوزات',
          description: 'نظام حجز متقدم مع إدارة شاملة للتقويم والحجوزات'
        },
        {
          id: 2,
          icon: 'fas fa-users',
          title: 'إدارة العملاء',
          description: 'قاعدة بيانات شاملة للعملاء مع سجل الحجوزات'
        },
        {
          id: 3,
          icon: 'fas fa-chart-bar',
          title: 'التقارير والإحصائيات',
          description: 'تقارير مفصلة وإحصائيات متقدمة لتحليل الأداء'
        },
        {
          id: 4,
          icon: 'fas fa-file-invoice',
          title: 'إدارة الفواتير',
          description: 'نظام فواتير متكامل مع إدارة المدفوعات'
        },
        {
          id: 5,
          icon: 'fas fa-cog',
          title: 'إدارة الخدمات',
          description: 'إدارة شاملة للخدمات الإضافية والمنتجات'
        },
        {
          id: 6,
          icon: 'fas fa-mobile-alt',
          title: 'واجهة متجاوبة',
          description: 'واجهة مستخدم حديثة تعمل على جميع الأجهزة'
        }
      ],
      screenshots: [
        {
          title: 'لوحة التحكم',
          description: 'لوحة تحكم شاملة مع إحصائيات مباشرة'
        },
        {
          title: 'إدارة الحجوزات',
          description: 'واجهة سهلة لإدارة جميع الحجوزات'
        },
        {
          title: 'إدارة العملاء',
          description: 'قاعدة بيانات شاملة للعملاء'
        },
        {
          title: 'التقارير',
          description: 'تقارير مفصلة وإحصائيات متقدمة'
        }
      ]
    }
  },
  mounted() {
    this.loadPackages();
    this.startAutoSlide();
  },
  methods: {
    async loadPackages() {
      try {
        const response = await axios.get('/api/packages');
        this.packages = response.data.data;
      } catch (error) {
        console.error('خطأ في تحميل الباقات:', error);
      }
    },
    
    scrollToSection(sectionId) {
      const element = document.getElementById(sectionId);
      if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
      }
    },
    
    nextSlide() {
      if (this.currentSlide < this.screenshots.length - 1) {
        this.currentSlide++;
      }
    },
    
    prevSlide() {
      if (this.currentSlide > 0) {
        this.currentSlide--;
      }
    },
    
    goToSlide(index) {
      this.currentSlide = index;
    },
    
    startAutoSlide() {
      setInterval(() => {
        if (this.currentSlide < this.screenshots.length - 1) {
          this.currentSlide++;
        } else {
          this.currentSlide = 0;
        }
      }, 5000);
    },
    
    getFeatureName(key) {
      // إذا كان key كائن، نعيد اسم الميزة
      if (typeof key === 'object' && key.name) {
        return key.name;
      }
      
      // إذا كان key نص، نبحث في القاموس
      const featureNames = {
        invoices: 'إدارة الفواتير',
        reports: 'التقارير',
        custom_branding: 'التخصيص',
        api_access: 'واجهة برمجة',
        multiple_halls: 'قاعات متعددة',
        'booking_management': 'إدارة الحجوزات',
        'customer_management': 'إدارة العملاء',
        'invoice_management': 'إدارة الفواتير',
        'reporting': 'التقارير والإحصائيات',
        'service_management': 'إدارة الخدمات',
        'multi_hall': 'قاعات متعددة',
        'api_access': 'واجهة برمجة التطبيقات',
        'custom_branding': 'تخصيص العلامة التجارية',
        'priority_support': 'دعم فني ذو أولوية',
        'advanced_analytics': 'تحليلات متقدمة'
      };
      return featureNames[key] || key;
    },
    
    async submitContact() {
      try {
        alert('تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.');
        this.contactForm = { name: '', email: '', message: '' };
      } catch (error) {
        alert('حدث خطأ في إرسال الرسالة. يرجى المحاولة مرة أخرى.');
      }
    }
  }
}
</script>

<style scoped>
.landing-page {
  direction: rtl;
  font-family: 'Cairo', sans-serif;
}

/* Header */
.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  z-index: 1000;
  box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
}

.navbar {
  padding: 1rem 0;
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav-logo {
  display: flex;
  align-items: center;
  font-size: 1.5rem;
  font-weight: bold;
  color: #667eea;
}

.nav-logo i {
  margin-left: 0.5rem;
  font-size: 2rem;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.nav-link {
  text-decoration: none;
  color: #333;
  font-weight: 500;
  transition: color 0.3s ease;
}

.nav-link:hover {
  color: #667eea;
}

.login-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white !important;
  padding: 0.5rem 1.5rem;
  border-radius: 25px;
}

/* Hero Section */
.hero {
  padding: 120px 0 80px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  position: relative;
  overflow: hidden;
}

.hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,100 1000,0 1000,100"/></svg>');
  background-size: cover;
}

.hero-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
  position: relative;
  z-index: 1;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: bold;
  margin-bottom: 1.5rem;
  line-height: 1.2;
}

.highlight {
  background: linear-gradient(45deg, #ffd700, #ffed4e);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-subtitle {
  font-size: 1.2rem;
  margin-bottom: 2rem;
  opacity: 0.9;
  line-height: 1.6;
}

.hero-buttons {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 2rem;
  border: none;
  border-radius: 50px;
  font-size: 1rem;
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary {
  background: linear-gradient(45deg, #ffd700, #ffed4e);
  color: #333;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(255, 215, 0, 0.3);
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateY(-2px);
}

.hero-image {
  position: relative;
  height: 400px;
}

.floating-card {
  position: absolute;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  padding: 1rem 1.5rem;
  border-radius: 15px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  animation: float 3s ease-in-out infinite;
}

.floating-card:nth-child(1) {
  top: 20%;
  left: 10%;
  animation-delay: 0s;
}

.floating-card:nth-child(2) {
  top: 50%;
  right: 20%;
  animation-delay: 1s;
}

.floating-card:nth-child(3) {
  bottom: 20%;
  left: 30%;
  animation-delay: 2s;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}

/* Sections */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.section-header {
  text-align: center;
  margin-bottom: 4rem;
}

.section-title {
  font-size: 2.5rem;
  font-weight: bold;
  color: #333;
  margin-bottom: 1rem;
}

.section-subtitle {
  font-size: 1.1rem;
  color: #666;
  max-width: 600px;
  margin: 0 auto;
}

/* Features Section */
.features {
  padding: 80px 0;
  background: #f8f9fa;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.feature-card {
  background: white;
  padding: 2rem;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-5px);
}

.feature-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
}

.feature-icon i {
  font-size: 2rem;
  color: white;
}

.feature-title {
  font-size: 1.5rem;
  font-weight: bold;
  color: #333;
  margin-bottom: 1rem;
}

.feature-description {
  color: #666;
  line-height: 1.6;
}

/* Pricing Section */
.pricing {
  padding: 80px 0;
}

.pricing-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.pricing-card {
  background: white;
  border-radius: 15px;
  padding: 2rem;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
  text-align: center;
  transition: transform 0.3s ease;
  border: 2px solid transparent;
  position: relative;
  overflow: hidden;
}

.pricing-card:hover {
  transform: translateY(-5px);
  border-color: #667eea;
}

.pricing-card.popular {
  border-color: #ffd700;
  box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
}

.popular-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: linear-gradient(45deg, #ffd700, #ffed4e);
  color: #333;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.package-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
}

.package-icon i {
  font-size: 1.5rem;
  color: white;
}

.package-description {
  color: #666;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
  line-height: 1.4;
}

.package-name {
  font-size: 1.5rem;
  font-weight: bold;
  color: #333;
  margin-bottom: 1rem;
}

.package-price {
  margin-bottom: 2rem;
}

.price {
  font-size: 3rem;
  font-weight: bold;
  color: #667eea;
}

.currency {
  font-size: 1.5rem;
  color: #667eea;
}

.period {
  color: #666;
  font-size: 1rem;
}

.package-features {
  margin-bottom: 2rem;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
  justify-content: flex-start;
  padding: 0.5rem 0;
  border-bottom: 1px solid #f0f0f0;
}

.feature-item:last-child {
  border-bottom: none;
}

.feature-item i {
  width: 20px;
  font-size: 1rem;
}

.feature-item i.fa-check-circle {
  color: #28a745;
}

.feature-item i.text-muted {
  color: #6c757d;
}

.feature-item span {
  flex: 1;
  font-size: 0.9rem;
}

.feature-item .text-muted {
  color: #6c757d;
}

.feature-item .line-through {
  text-decoration: line-through;
}

.feature-value {
  background: #f8f9fa;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
  color: #667eea;
  font-weight: 500;
}

.btn-block {
  width: 100%;
  justify-content: center;
}

/* Screenshots Section */
.screenshots {
  padding: 80px 0;
  background: #f8f9fa;
}

.screenshots-slider {
  position: relative;
  max-width: 800px;
  margin: 0 auto;
}

.slider-container {
  overflow: hidden;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.slider-track {
  display: flex;
  transition: transform 0.5s ease;
}

.slide {
  min-width: 100%;
}

.screenshot-card {
  position: relative;
}

.screenshot-placeholder {
  width: 100%;
  height: 400px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  text-align: center;
  padding: 2rem;
}

.screenshot-placeholder i {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.screenshot-placeholder h4 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.slider-controls {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 2rem;
  margin-top: 2rem;
}

.slider-btn {
  background: #667eea;
  color: white;
  border: none;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.3s ease;
}

.slider-btn:hover:not(:disabled) {
  background: #5a6fd8;
  transform: scale(1.1);
}

.slider-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.slider-dots {
  display: flex;
  gap: 0.5rem;
}

.dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: none;
  background: #ddd;
  cursor: pointer;
  transition: background 0.3s ease;
}

.dot.active {
  background: #667eea;
}

/* Contact Section */
.contact {
  padding: 80px 0;
}

.contact-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: start;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 2rem;
}

.contact-item i {
  width: 50px;
  height: 50px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.contact-item h4 {
  margin: 0 0 0.5rem 0;
  color: #333;
}

.contact-item p {
  margin: 0;
  color: #666;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e1e5e9;
  border-radius: 10px;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
}

/* Footer */
.footer {
  background: #333;
  color: white;
  padding: 3rem 0 1rem;
}

.footer-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.footer-logo {
  display: flex;
  align-items: center;
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 1rem;
}

.footer-logo i {
  margin-left: 0.5rem;
  color: #667eea;
}

.footer-section h4 {
  margin-bottom: 1rem;
  color: #667eea;
}

.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section ul li {
  margin-bottom: 0.5rem;
}

.footer-section ul li a {
  color: #ccc;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-section ul li a:hover {
  color: #667eea;
}

.footer-bottom {
  text-align: center;
  padding-top: 2rem;
  border-top: 1px solid #444;
  color: #ccc;
}

/* Responsive */
@media (max-width: 768px) {
  .hero-container {
    grid-template-columns: 1fr;
    text-align: center;
  }
  
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-buttons {
    justify-content: center;
  }
  
  .contact-content {
    grid-template-columns: 1fr;
  }
  
  .nav-links {
    display: none;
  }
  
  .section-title {
    font-size: 2rem;
  }
}
</style> 