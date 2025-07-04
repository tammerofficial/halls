<template>
  <AppLayout>
  <div class="memberships-page-v2">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h1 class="page-title">🌟 إدارة خطط العضويات</h1>
        <p class="page-subtitle">صمم خطط عضويات مرنة وقوية لجذب عملائك.</p>
      </div>
      <div class="header-actions">
        <button @click="openCreateModal()" class="btn btn-primary">
          <i class="fas fa-plus"></i>
          خطة عضوية جديدة
        </button>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-id-card"></i>
        </div>
        <div class="stat-content">
          <h3>{{ memberships.length }}</h3>
          <p>إجمالي العضويات</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
          <h3>{{ activeMemberships.length }}</h3>
          <p>العضويات النشطة</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-star"></i>
        </div>
        <div class="stat-content">
          <h3>{{ premiumMemberships.length }}</h3>
          <p>العضويات المميزة</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-content">
          <h3>{{ totalRevenue }} ريال</h3>
          <p>إجمالي الإيرادات</p>
        </div>
      </div>
    </div>

    <!-- Memberships Grid -->
    <div v-if="loading" class="loading-state">
      <div class="loader"></div>
      <p>جاري تحميل العضويات...</p>
    </div>
    <div v-else-if="memberships.length === 0" class="empty-state">
      <i class="fas fa-inbox"></i>
      <h3>لا توجد عضويات</h3>
      <p>ابدأ بإنشاء خطة عضوية جديدة</p>
      <button @click="openCreateModal()" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        إنشاء عضوية
      </button>
    </div>
    <div v-else class="packages-grid">
      <div v-for="mem in memberships" :key="mem.id" class="package-card" :class="[mem.type, { 'inactive-card': !mem.is_active }]">
        <div class="card-header">
          <h3 class="package-name">{{ mem.name }}</h3>
          <span class="membership-type-badge">{{ mem.type_text }}</span>
        </div>
        <div class="package-price">
          {{ mem.price }}<span>ر.س/{{ mem.duration_text }}</span>
        </div>
        <p class="package-description">{{ mem.description }}</p>
        
        <ul class="features-list">
          <li><i class="fas fa-check-circle"></i> <strong>{{ mem.max_halls }}</strong> قاعات</li>
          <li><i class="fas fa-check-circle"></i> <strong>{{ mem.max_bookings_per_month }}</strong> حجوزات شهرياً</li>
           <li :class="{ 'feature-disabled': !mem.priority_support }"><i :class="getFeatureIcon(mem.priority_support)"></i> دعم فني مميز</li>
           <li :class="{ 'feature-disabled': !mem.custom_branding }"><i :class="getFeatureIcon(mem.custom_branding)"></i> علامة تجارية خاصة</li>
           <li :class="{ 'feature-disabled': !mem.advanced_analytics }"><i :class="getFeatureIcon(mem.advanced_analytics)"></i> تحليلات متقدمة</li>
        </ul>

        <div class="card-footer">
          <div class="actions">
            <button @click="editMembership(mem)" class="btn-icon" title="تعديل"><i class="fas fa-edit"></i></button>
            <button @click="deleteMembership(mem)" class="btn-icon text-red" title="حذف"><i class="fas fa-trash"></i></button>
          </div>
          <div class="status-toggle">
            <span>{{ mem.is_active ? 'نشط' : 'غير نشط' }}</span>
            <label class="switch">
              <input type="checkbox" :checked="mem.is_active" @change="toggleStatus(mem)">
              <span class="slider round"></span>
            </label>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal remains the same conceptually but needs to be wired -->
  </div>
  </AppLayout>
</template>

<script>
import axios from 'axios';
import AppLayout from './Layout/AppLayout.vue';

export default {
    name: 'Memberships',
    components: { AppLayout },
    data() {
        return {
            loading: true,
            memberships: [],
            showCreateModal: false,
            editingMembership: null,
            form: {
                name: '',
                description: '',
                price: '',
                duration_days: 30,
                max_halls: 1,
                max_bookings_per_month: 10,
                type: 'basic',
                benefits_text: '',
                priority_support: false,
                custom_branding: false,
                advanced_analytics: false
            }
        }
    },
    computed: {
        activeMemberships() {
            return this.memberships.filter(m => m.is_active)
        },
        premiumMemberships() {
            return this.memberships.filter(m => m.type === 'premium' && m.is_active)
        },
        totalRevenue() {
            return this.memberships.reduce((total, mem) => {
                return total + (mem.price * (mem.subscriptions_count || 0))
            }, 0).toFixed(2)
        }
    },
    mounted() {
        this.loadMemberships()
    },
    methods: {
        getFeatureIcon(hasFeature) {
            return hasFeature ? 'fas fa-check-circle' : 'fas fa-times-circle';
        },
        async loadMemberships() {
            try {
                this.loading = true;
                // محاكاة تحميل البيانات مع تأخير بسيط
                await new Promise(resolve => setTimeout(resolve, 500));
                
                this.memberships = [
                    {
                        id: 1,
                        name: 'العضوية الأساسية',
                        description: 'مناسبة للقاعات الصغيرة والمبتدئة',
                        price: 99.00,
                        duration_days: 30,
                        max_halls: 2,
                        max_bookings_per_month: 50,
                        type: 'basic',
                        type_text: 'أساسية',
                        is_active: true,
                        subscriptions_count: 25,
                        formatted_price: '99.00 ريال',
                        duration_text: 'شهري',
                        priority_support: false,
                        custom_branding: false,
                        advanced_analytics: false
                    },
                    {
                        id: 2,
                        name: 'العضوية المميزة',
                        description: 'مناسبة للقاعات المتوسطة والمتطورة',
                        price: 199.00,
                        duration_days: 30,
                        max_halls: 5,
                        max_bookings_per_month: 150,
                        type: 'premium',
                        type_text: 'مميزة',
                        is_active: true,
                        subscriptions_count: 12,
                        formatted_price: '199.00 ريال',
                        duration_text: 'شهري',
                        priority_support: true,
                        custom_branding: true,
                        advanced_analytics: false
                    },
                    {
                        id: 3,
                        name: 'العضوية المؤسسية',
                        description: 'مناسبة للشركات الكبيرة والمؤسسات',
                        price: 399.00,
                        duration_days: 30,
                        max_halls: 10,
                        max_bookings_per_month: 500,
                        type: 'enterprise',
                        type_text: 'مؤسسية',
                        is_active: true,
                        subscriptions_count: 5,
                        formatted_price: '399.00 ريال',
                        duration_text: 'شهري',
                        priority_support: true,
                        custom_branding: true,
                        advanced_analytics: true
                    }
                ]
            } catch (error) {
                console.error('خطأ في تحميل العضويات:', error)
            } finally {
                this.loading = false;
            }
        },
        editMembership(membership) {
            this.editingMembership = membership
            this.form = {
                name: membership.name,
                description: membership.description,
                price: membership.price,
                duration_days: membership.duration_days,
                max_halls: membership.max_halls,
                max_bookings_per_month: membership.max_bookings_per_month,
                type: membership.type,
                benefits_text: membership.benefits ? membership.benefits.join(', ') : '',
                priority_support: membership.priority_support,
                custom_branding: membership.custom_branding,
                advanced_analytics: membership.advanced_analytics
            }
            this.showCreateModal = true
        },
        viewFeatures(membership) {
            // عرض ميزات العضوية
            alert(`ميزات عضوية ${membership.name}`)
        },
        async toggleStatus(membership) {
            try {
                membership.is_active = !membership.is_active
                // هنا يتم إرسال الطلب للخادم
            } catch (error) {
                console.error('خطأ في تغيير الحالة:', error)
            }
        },
        async deleteMembership(membership) {
            if (confirm(`هل أنت متأكد من حذف عضوية "${membership.name}"؟`)) {
                try {
                    this.memberships = this.memberships.filter(m => m.id !== membership.id)
                    // هنا يتم إرسال طلب الحذف للخادم
                } catch (error) {
                    console.error('خطأ في حذف العضوية:', error)
                }
            }
        },
        async saveMembership() {
            try {
                const benefits = this.form.benefits_text ? this.form.benefits_text.split(',').map(b => b.trim()) : []
                
                if (this.editingMembership) {
                    // تحديث العضوية
                    const index = this.memberships.findIndex(m => m.id === this.editingMembership.id)
                    this.memberships[index] = {
                        ...this.editingMembership,
                        ...this.form,
                        benefits,
                        type_text: this.getTypeText(this.form.type)
                    }
                } else {
                    // إنشاء عضوية جديدة
                    const newMembership = {
                        id: Date.now(),
                        ...this.form,
                        benefits,
                        type_text: this.getTypeText(this.form.type),
                        is_active: true,
                        subscriptions_count: 0,
                        formatted_price: `${this.form.price} ريال`,
                        duration_text: this.form.duration_days === 30 ? 'شهري' : `${this.form.duration_days} يوم`
                    }
                    this.memberships.push(newMembership)
                }
                
                this.showCreateModal = false
                this.resetForm()
            } catch (error) {
                console.error('خطأ في حفظ العضوية:', error)
            }
        },
        getTypeText(type) {
            const types = {
                'basic': 'أساسية',
                'premium': 'مميزة',
                'enterprise': 'مؤسسية'
            }
            return types[type] || 'غير محدد'
        },
        resetForm() {
            this.form = {
                name: '',
                description: '',
                price: '',
                duration_days: 30,
                max_halls: 1,
                max_bookings_per_month: 10,
                type: 'basic',
                benefits_text: '',
                priority_support: false,
                custom_branding: false,
                advanced_analytics: false
            }
            this.editingMembership = null
        },
        openCreateModal() {
            this.resetForm();
            this.showCreateModal = true;
        }
    }
}
</script>

<style scoped>
.memberships-page-v2 {
  padding: 2rem;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-title {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
}

.stat-icon {
  width: 3rem;
  height: 3rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
}

.stat-content h3 {
  font-size: 1.5rem;
  font-weight: bold;
  color: #1f2937;
  margin: 0;
}

.stat-content p {
  color: #6b7280;
  margin: 0;
}

.packages-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
}

.package-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
  border-top: 4px solid;
  display: flex;
  flex-direction: column;
}

.package-card.basic { border-color: #3498db; }
.package-card.premium { border-color: #9b59b6; }
.package-card.enterprise { border-color: #f1c40f; }

.membership-type-badge {
  font-size: 0.8rem;
  font-weight: bold;
  padding: 0.25rem 0.75rem;
  border-radius: 99px;
  color: white;
}

.package-card.basic .membership-type-badge { background-color: #3498db; }
.package-card.premium .membership-type-badge { background-color: #9b59b6; }
.package-card.enterprise .membership-type-badge { background-color: #f1c40f; }

.card-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.package-name {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
}

.package-price {
  margin: 0;
  font-size: 1rem;
  font-weight: 500;
  color: #6b7280;
}

.package-description {
  margin: 0;
  font-size: 0.875rem;
  color: #6b7280;
  line-height: 1.4;
}

.features-list {
  margin: 1.5rem 0;
  padding-left: 1.5rem;
  list-style: none;
}

.features-list li {
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
}

.features-list li i {
  margin-right: 0.5rem;
  color: #6b7280;
}

.features-list li.feature-disabled {
  opacity: 0.5;
}

.card-footer {
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  width: 2rem;
  height: 2rem;
  border: none;
  border-radius: 0.375rem;
  background: #f3f4f6;
  color: #6b7280;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.btn-icon:hover {
  background: #e5e7eb;
  color: #374151;
}

.btn-icon.text-red:hover {
  background: #fee2e2;
  color: #dc2626;
}

.status-toggle {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

/* Loading State */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  color: #6b7280;
}

.loader {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f4f6;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-state p {
  font-size: 1.1rem;
  margin: 0;
}

/* Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  text-align: center;
}

.empty-state i {
  font-size: 4rem;
  color: #d1d5db;
  margin-bottom: 1rem;
}

.empty-state h3 {
  font-size: 1.5rem;
  color: #374151;
  margin: 0 0 0.5rem 0;
}

.empty-state p {
  color: #6b7280;
  margin: 0 0 2rem 0;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

.loading-state {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 200px;
}

.loader {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #3498db;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style> 