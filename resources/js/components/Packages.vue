<template>
  <AppLayout>
    <div class="packages-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
          <h1 class="page-title">
            <i class="fas fa-box"></i>
            إدارة الباقات
          </h1>
          <p class="page-subtitle">إدارة باقات الاشتراك وتحديد الأسعار والمميزات</p>
      </div>
      <div class="header-actions">
          <button @click="showCreateModal = true" class="btn btn-primary">
          <i class="fas fa-plus"></i>
            إضافة باقة جديدة
        </button>
      </div>
    </div>

      <!-- Packages Grid -->
      <div class="packages-grid">
        <div v-for="pkg in packages" :key="pkg.id" class="package-card" :class="{ 'popular': pkg.isPopular }">
          <div v-if="pkg.isPopular" class="popular-badge">
            <i class="fas fa-star"></i>
            الأكثر شعبية
          </div>
          
          <div class="package-header">
            <div class="package-icon">
              <i :class="pkg.icon"></i>
            </div>
            <h3 class="package-name">{{ pkg.name }}</h3>
            <p class="package-description">{{ pkg.description }}</p>
          </div>

          <div class="package-price">
            <span class="currency">ر.س</span>
            <span class="amount">{{ pkg.price }}</span>
            <span class="period">/ {{ pkg.period }}</span>
        </div>

          <div class="package-features">
            <div v-for="feature in pkg.features" :key="feature.id" class="feature-item">
              <i class="fas fa-check-circle" :class="{ 'text-muted': !feature.included }"></i>
              <span :class="{ 'text-muted line-through': !feature.included }">{{ feature.name }}</span>
              <span v-if="feature.value" class="feature-value">{{ feature.value }}</span>
        </div>
      </div>

          <div class="package-stats">
            <div class="stat">
              <i class="fas fa-users"></i>
              <span>{{ pkg.subscribers }} مشترك</span>
        </div>
            <div class="stat">
              <i class="fas fa-chart-line"></i>
              <span>{{ pkg.revenue }} ر.س شهرياً</span>
        </div>
      </div>

          <div class="package-actions">
            <button @click="editPackage(pkg)" class="btn btn-outline-primary">
              <i class="fas fa-edit"></i>
              تعديل
            </button>
            <button @click="viewSubscribers(pkg)" class="btn btn-outline-info">
          <i class="fas fa-users"></i>
              المشتركين
            </button>
            <button 
              @click="toggleStatus(pkg)" 
              class="btn"
              :class="pkg.isActive ? 'btn-outline-warning' : 'btn-outline-success'"
            >
              <i :class="pkg.isActive ? 'fas fa-pause' : 'fas fa-play'"></i>
              {{ pkg.isActive ? 'إيقاف' : 'تفعيل' }}
            </button>
            <button @click="deletePackage(pkg)" class="btn btn-outline-danger">
              <i class="fas fa-trash"></i>
              حذف
            </button>
        </div>
        </div>
      </div>

      <!-- Create/Edit Package Modal -->
      <div v-if="showCreateModal || editingPackage" class="modal-overlay" @click="closeModal">
        <div class="modal large" @click.stop>
          <div class="modal-header">
            <h3>{{ editingPackage ? 'تعديل الباقة' : 'إضافة باقة جديدة' }}</h3>
            <button @click="closeModal" class="close-btn">
              <i class="fas fa-times"></i>
            </button>
          </div>
          
          <div class="modal-body">
            <form @submit.prevent="savePackage">
              <div class="form-grid">
                <div class="form-group">
                  <label>اسم الباقة</label>
                  <input 
                    v-model="packageForm.name" 
                    type="text" 
                    class="form-input" 
                    required
                    placeholder="مثال: الباقة الذهبية"
                  >
                </div>
                
                <div class="form-group">
                  <label>السعر</label>
                  <input 
                    v-model="packageForm.price" 
                    type="number" 
                    class="form-input" 
                    required
                    placeholder="0"
                  >
        </div>
                
                <div class="form-group">
                  <label>المدة</label>
                  <select v-model="packageForm.period" class="form-input">
                    <option value="شهر">شهرياً</option>
                    <option value="3 أشهر">ربع سنوي</option>
                    <option value="6 أشهر">نصف سنوي</option>
                    <option value="سنة">سنوياً</option>
                  </select>
        </div>
                
                <div class="form-group">
                  <label>الأيقونة</label>
                  <select v-model="packageForm.icon" class="form-input">
                    <option value="fas fa-crown">تاج</option>
                    <option value="fas fa-gem">جوهرة</option>
                    <option value="fas fa-star">نجمة</option>
                    <option value="fas fa-rocket">صاروخ</option>
                  </select>
      </div>
    </div>

              <div class="form-group">
                <label>الوصف</label>
                <textarea 
                  v-model="packageForm.description" 
                  class="form-input" 
                  rows="3"
                  placeholder="وصف مختصر للباقة"
                ></textarea>
              </div>
              
              <div class="form-group">
                <label>المميزات</label>
                <div class="features-list">
                  <div v-for="(feature, index) in packageForm.features" :key="index" class="feature-row">
                    <input 
                      v-model="feature.name" 
                      type="text" 
                      class="form-input" 
                      placeholder="اسم الميزة"
                    >
                    <input 
                      v-model="feature.value" 
                      type="text" 
                      class="form-input small" 
                      placeholder="القيمة"
                    >
                    <label class="checkbox">
                      <input v-model="feature.included" type="checkbox">
                      <span>متضمنة</span>
                    </label>
                    <button @click="removeFeature(index)" type="button" class="btn-icon danger">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                  <button @click="addFeature" type="button" class="btn btn-outline-primary">
                    <i class="fas fa-plus"></i>
                    إضافة ميزة
                  </button>
          </div>
        </div>
              
              <div class="form-group">
                <label class="checkbox">
                  <input v-model="packageForm.isPopular" type="checkbox">
                  <span>تحديد كالأكثر شعبية</span>
                </label>
              </div>
            </form>
          </div>
          
          <div class="modal-footer">
            <button @click="savePackage" class="btn btn-primary">
              <i class="fas fa-save"></i>
              حفظ
            </button>
            <button @click="closeModal" class="btn btn-secondary">
              إلغاء
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from './Layout/AppLayout.vue';

export default {
  name: 'Packages',
  components: {
    AppLayout
  },
  data() {
    return {
      packages: [
        {
          id: 1,
          name: 'الباقة الأساسية',
          description: 'مثالية للأفراد والمناسبات الصغيرة',
          price: 299,
          period: 'شهر',
          icon: 'fas fa-star',
          isPopular: false,
          isActive: true,
          subscribers: 45,
          revenue: 13455,
          features: [
            { id: 1, name: 'عدد القاعات', value: '1', included: true },
            { id: 2, name: 'عدد الحجوزات الشهرية', value: '10', included: true },
            { id: 3, name: 'الدعم الفني', value: 'أساسي', included: true },
            { id: 4, name: 'التقارير المتقدمة', included: false },
            { id: 5, name: 'API متقدم', included: false }
          ]
        },
        {
          id: 2,
          name: 'الباقة الذهبية',
          description: 'للشركات المتوسطة والمناسبات الكبيرة',
          price: 599,
          period: 'شهر',
          icon: 'fas fa-crown',
          isPopular: true,
          isActive: true,
          subscribers: 128,
          revenue: 76672,
          features: [
            { id: 1, name: 'عدد القاعات', value: '5', included: true },
            { id: 2, name: 'عدد الحجوزات الشهرية', value: '50', included: true },
            { id: 3, name: 'الدعم الفني', value: '24/7', included: true },
            { id: 4, name: 'التقارير المتقدمة', included: true },
            { id: 5, name: 'API متقدم', included: false }
          ]
        },
        {
          id: 3,
          name: 'الباقة البلاتينية',
          description: 'للمؤسسات الكبرى بدون قيود',
          price: 999,
          period: 'شهر',
          icon: 'fas fa-gem',
          isPopular: false,
          isActive: true,
          subscribers: 32,
          revenue: 31968,
          features: [
            { id: 1, name: 'عدد القاعات', value: 'غير محدود', included: true },
            { id: 2, name: 'عدد الحجوزات الشهرية', value: 'غير محدود', included: true },
            { id: 3, name: 'الدعم الفني', value: 'VIP 24/7', included: true },
            { id: 4, name: 'التقارير المتقدمة', included: true },
            { id: 5, name: 'API متقدم', included: true }
          ]
        }
      ],
      showCreateModal: false,
      editingPackage: null,
      packageForm: {
        name: '',
        description: '',
        price: '',
        period: 'شهر',
        icon: 'fas fa-star',
        isPopular: false,
        features: []
      }
    }
  },
  methods: {
    editPackage(pkg) {
      this.editingPackage = pkg;
      this.packageForm = { ...pkg, features: [...pkg.features] };
    },
    viewSubscribers(pkg) {
      console.log('View subscribers for package:', pkg);
      // يمكن توجيه المستخدم إلى صفحة المشتركين مع فلتر للباقة
    },
    toggleStatus(pkg) {
      pkg.isActive = !pkg.isActive;
    },
    deletePackage(pkg) {
      if (confirm('هل أنت متأكد من حذف هذه الباقة؟')) {
        this.packages = this.packages.filter(p => p.id !== pkg.id);
      }
    },
    addFeature() {
      this.packageForm.features.push({
        name: '',
        value: '',
        included: true
      });
    },
    removeFeature(index) {
      this.packageForm.features.splice(index, 1);
    },
    savePackage() {
      if (this.editingPackage) {
        // تحديث الباقة الموجودة
        const index = this.packages.findIndex(p => p.id === this.editingPackage.id);
        this.packages[index] = { ...this.packageForm };
      } else {
        // إضافة باقة جديدة
        this.packages.push({
          ...this.packageForm,
          id: Date.now(),
          isActive: true,
          subscribers: 0,
          revenue: 0
        });
      }
      this.closeModal();
    },
    closeModal() {
      this.showCreateModal = false;
      this.editingPackage = null;
      this.packageForm = {
        name: '',
        description: '',
        price: '',
        period: 'شهر',
        icon: 'fas fa-star',
        isPopular: false,
        features: []
      };
    }
  }
}
</script>

<style scoped>
.packages-page {
  padding: 0;
}

/* Header */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-content {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.page-title {
  margin: 0;
  font-size: 1.75rem;
  font-weight: bold;
  color: #333;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.page-title i {
  color: #667eea;
}

.page-subtitle {
  margin: 0;
  color: #666;
  font-size: 0.95rem;
}

/* Packages Grid */
.packages-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.package-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  position: relative;
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.package-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.package-card.popular {
  border-color: #667eea;
  background: linear-gradient(to bottom, #f8f9ff, white);
}

.popular-badge {
  position: absolute;
  top: -12px;
  left: 50%;
  transform: translateX(-50%);
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.5rem 1.5rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.package-header {
  text-align: center;
  margin-bottom: 2rem;
}

.package-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 1rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  color: white;
}

.package-name {
  margin: 0 0 0.5rem 0;
  font-size: 1.5rem;
  font-weight: bold;
  color: #333;
}

.package-description {
  margin: 0;
  color: #666;
  font-size: 0.95rem;
}

.package-price {
  text-align: center;
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: #f8f9fa;
  border-radius: 12px;
}

.currency {
  font-size: 1.25rem;
  color: #666;
  margin-left: 0.5rem;
}

.amount {
  font-size: 3rem;
  font-weight: bold;
  color: #333;
  margin: 0 0.5rem;
}

.period {
  font-size: 1rem;
  color: #666;
}

.package-features {
  margin-bottom: 2rem;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f1f5f9;
}

.feature-item:last-child {
  border-bottom: none;
}

.feature-item i {
  color: #10b981;
  font-size: 1.1rem;
}

.feature-item i.text-muted {
  color: #d1d5db;
}

.feature-value {
  margin-right: auto;
  font-weight: 600;
  color: #667eea;
}

.text-muted {
  color: #9ca3af;
}

.line-through {
  text-decoration: line-through;
}

.package-stats {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.stat {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #666;
}

.stat i {
  color: #667eea;
}

.package-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 2rem;
}

.modal {
  background: white;
  border-radius: 16px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.modal.large {
  max-width: 800px;
}

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e1e5e9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: bold;
  color: #333;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.25rem;
  color: #666;
  cursor: pointer;
}

.modal-body {
  padding: 2rem;
  overflow-y: auto;
  flex: 1;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
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
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input.small {
  max-width: 100px;
}

textarea.form-input {
  resize: vertical;
}

.features-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.feature-row {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.checkbox input {
  cursor: pointer;
}

.btn-icon {
  width: 36px;
  height: 36px;
  border: none;
  background: #f3f4f6;
  color: #6b7280;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.btn-icon.danger {
  background: #fee2e2;
  color: #dc2626;
}

.btn-icon:hover {
  background: #e5e7eb;
}

.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid #e1e5e9;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

/* Buttons */
.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  font-size: 0.95rem;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
  background: #e5e7eb;
  color: #374151;
}

.btn-secondary:hover {
  background: #d1d5db;
}

.btn-outline-primary {
  background: transparent;
  color: #667eea;
  border: 1px solid #667eea;
}

.btn-outline-primary:hover {
  background: #667eea;
  color: white;
}

.btn-outline-info {
  background: transparent;
  color: #06b6d4;
  border: 1px solid #06b6d4;
}

.btn-outline-info:hover {
  background: #06b6d4;
  color: white;
}

.btn-outline-warning {
  background: transparent;
  color: #f59e0b;
  border: 1px solid #f59e0b;
}

.btn-outline-warning:hover {
  background: #f59e0b;
  color: white;
}

.btn-outline-success {
  background: transparent;
  color: #10b981;
  border: 1px solid #10b981;
}

.btn-outline-success:hover {
  background: #10b981;
  color: white;
}

.btn-outline-danger {
  background: transparent;
  color: #ef4444;
  border: 1px solid #ef4444;
}

.btn-outline-danger:hover {
  background: #ef4444;
  color: white;
}

/* Responsive */
@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .packages-grid {
    grid-template-columns: 1fr;
  }
  
  .package-actions {
    grid-template-columns: 1fr;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
  
  .feature-row {
    flex-wrap: wrap;
  }
}
</style> 