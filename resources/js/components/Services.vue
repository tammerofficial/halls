<template>
  <AppLayout>
    <div class="services-page">
      <!-- Header -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">🎯 إدارة الخدمات</h1>
          <p class="page-subtitle">إدارة الخدمات الإضافية والمرافق المتاحة للحجوزات</p>
        </div>
        <div class="header-actions">
          <button @click="openCreateModal" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            إضافة خدمة جديدة
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon bg-blue">
            <i class="fas fa-concierge-bell"></i>
          </div>
          <div class="stat-content">
            <h3>{{ totalServices }}</h3>
            <p>إجمالي الخدمات</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon bg-green">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-content">
            <h3>{{ activeServices }}</h3>
            <p>خدمات نشطة</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon bg-purple">
            <i class="fas fa-star"></i>
          </div>
          <div class="stat-content">
            <h3>{{ popularServices }}</h3>
            <p>خدمات مميزة</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon bg-orange">
            <i class="fas fa-chart-line"></i>
          </div>
          <div class="stat-content">
            <h3>{{ totalRevenue }} ر.س</h3>
            <p>إجمالي الإيرادات</p>
          </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="filters-section">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="البحث في الخدمات..."
            class="search-input"
          >
        </div>
        <div class="filter-buttons">
          <button 
            @click="currentFilter = 'all'" 
            :class="['filter-btn', { active: currentFilter === 'all' }]"
          >
            الكل ({{ services.length }})
          </button>
          <button 
            @click="currentFilter = 'active'" 
            :class="['filter-btn', { active: currentFilter === 'active' }]"
          >
            نشط ({{ activeServicesCount }})
          </button>
          <button 
            @click="currentFilter = 'inactive'" 
            :class="['filter-btn', { active: currentFilter === 'inactive' }]"
          >
            غير نشط ({{ inactiveServicesCount }})
          </button>
          <button 
            @click="currentFilter = 'popular'" 
            :class="['filter-btn', { active: currentFilter === 'popular' }]"
          >
            الأكثر طلباً
          </button>
        </div>
      </div>

      <!-- Services Table -->
      <div class="services-table-container">
        <table class="services-table">
          <thead>
            <tr>
              <th>الخدمة</th>
              <th>الفئة</th>
              <th>السعر</th>
              <th>مرات الطلب</th>
              <th>الحالة</th>
              <th>الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="service in filteredServices" :key="service.id">
              <td>
                <div class="service-info">
                  <div class="service-icon-small" :style="{ backgroundColor: service.color }">
              <i :class="service.icon"></i>
            </div>
                  <div>
                    <div class="service-name">{{ service.name }}</div>
                    <div class="service-description">{{ service.description }}</div>
                  </div>
                </div>
              </td>
              <td>
                <span class="category-badge" :class="service.category">
                  {{ getCategoryName(service.category) }}
                </span>
              </td>
              <td class="price-cell">
                <div class="price">{{ service.price }} ر.س</div>
                <div class="price-type">{{ service.priceType }}</div>
              </td>
              <td>
                <div class="usage-stats">
                  <span class="usage-count">{{ service.usageCount }}</span>
                  <span class="usage-trend" :class="service.trend">
                    <i :class="getTrendIcon(service.trend)"></i>
                    {{ service.trendPercentage }}%
                  </span>
                </div>
              </td>
              <td>
                <label class="switch">
                  <input 
                    type="checkbox" 
                    :checked="service.isActive" 
                    @change="toggleServiceStatus(service)"
                  >
                  <span class="slider round"></span>
                </label>
              </td>
              <td>
                <div class="action-buttons">
                  <button @click="viewService(service)" class="btn-icon" title="عرض">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button @click="editService(service)" class="btn-icon" title="تعديل">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button @click="deleteService(service)" class="btn-icon text-red" title="حذف">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Create/Edit Modal -->
      <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
          <div class="modal-header">
            <h2>{{ editingService ? 'تعديل الخدمة' : 'إضافة خدمة جديدة' }}</h2>
            <button @click="closeModal" class="close-btn">
              <i class="fas fa-times"></i>
            </button>
          </div>
          
          <form @submit.prevent="saveService" class="service-form">
            <div class="form-grid">
              <div class="form-group">
                <label>اسم الخدمة</label>
                <input 
                  v-model="form.name" 
                  type="text" 
                  required 
                  placeholder="مثال: خدمة التصوير الاحترافي"
                >
              </div>
              
              <div class="form-group">
                <label>الفئة</label>
                <select v-model="form.category" required>
                  <option value="">اختر الفئة</option>
                  <option value="photography">التصوير</option>
                  <option value="catering">الضيافة</option>
                  <option value="decoration">التزيين</option>
                  <option value="entertainment">الترفيه</option>
                  <option value="technical">تقني</option>
                  <option value="other">أخرى</option>
                </select>
              </div>
              
              <div class="form-group">
                <label>السعر</label>
                <input 
                  v-model="form.price" 
                  type="number" 
                  required 
                  min="0"
                  placeholder="0.00"
                >
              </div>
              
              <div class="form-group">
                <label>نوع السعر</label>
                <select v-model="form.priceType" required>
                  <option value="fixed">ثابت</option>
                  <option value="hourly">بالساعة</option>
                  <option value="daily">يومي</option>
                  <option value="person">للشخص</option>
                </select>
              </div>
              
              <div class="form-group full-width">
                <label>الوصف</label>
                <textarea 
                  v-model="form.description" 
                  rows="3" 
                  placeholder="وصف تفصيلي للخدمة..."
                ></textarea>
              </div>
              
              <div class="form-group">
                <label>الأيقونة</label>
                <input 
                  v-model="form.icon" 
                  type="text" 
                  placeholder="fas fa-camera"
                >
              </div>
              
              <div class="form-group">
                <label>اللون</label>
                <input 
                  v-model="form.color" 
                  type="color"
                >
              </div>
              
              <div class="form-group full-width">
                <label class="checkbox-label">
                  <input 
                    v-model="form.isPopular" 
                    type="checkbox"
                  >
                  خدمة مميزة
                </label>
              </div>
            </div>
            
            <div class="modal-footer">
              <button type="button" @click="closeModal" class="btn btn-secondary">
                إلغاء
              </button>
              <button type="submit" class="btn btn-primary">
                {{ editingService ? 'حفظ التغييرات' : 'إضافة الخدمة' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from './Layout/AppLayout.vue';

export default {
  name: 'Services',
  components: {
    AppLayout
  },
  data() {
    return {
      services: [
        {
          id: 1,
          name: 'خدمة التصوير الاحترافي',
          description: 'تصوير احترافي للمناسبات بأحدث المعدات',
          price: 1500,
          icon: 'fas fa-camera',
          category: 'photography',
          color: '#3b82f6',
          priceType: 'fixed',
          isActive: true,
          isPopular: true,
          usageCount: 145,
          trend: 'up',
          trendPercentage: 12
        },
        {
          id: 2,
          name: 'خدمة الضيافة الفاخرة',
          description: 'بوفيه مفتوح مع خدمة النادل',
          price: 150,
          icon: 'fas fa-utensils',
          category: 'catering',
          color: '#10b981',
          priceType: 'person',
          isActive: true,
          isPopular: true,
          usageCount: 230,
          trend: 'up',
          trendPercentage: 25
        },
        {
          id: 3,
          name: 'تزيين القاعة',
          description: 'تزيين احترافي حسب المناسبة',
          price: 2000,
          icon: 'fas fa-palette',
          category: 'decoration',
          color: '#8b5cf6',
          priceType: 'fixed',
          isActive: true,
          isPopular: false,
          usageCount: 89,
          trend: 'stable',
          trendPercentage: 0
        },
        {
          id: 4,
          name: 'DJ وموسيقى',
          description: 'خدمة DJ محترف مع معدات صوتية',
          price: 500,
          icon: 'fas fa-music',
          category: 'entertainment',
          color: '#f59e0b',
          priceType: 'hourly',
          isActive: true,
          isPopular: false,
          usageCount: 67,
          trend: 'down',
          trendPercentage: -5
        },
        {
          id: 5,
          name: 'شاشات عرض',
          description: 'شاشات LED كبيرة للعروض',
          price: 800,
          icon: 'fas fa-tv',
          category: 'technical',
          color: '#06b6d4',
          priceType: 'daily',
          isActive: false,
          isPopular: false,
          usageCount: 34,
          trend: 'down',
          trendPercentage: -10
        }
      ],
      showModal: false,
      editingService: null,
      form: {
        name: '',
        category: '',
        price: '',
        priceType: 'fixed',
        description: '',
        icon: 'fas fa-concierge-bell',
        color: '#667eea',
        isPopular: false
      },
      currentFilter: 'all',
      searchQuery: ''
    }
  },
  computed: {
    totalServices() {
      return this.services.length;
    },
    activeServices() {
      return this.services.filter(s => s.isActive).length;
    },
    popularServices() {
      return this.services.filter(s => s.isPopular).length;
    },
    totalRevenue() {
      return this.services.reduce((total, service) => {
        return total + (service.price * service.usageCount);
      }, 0).toLocaleString();
    },
    activeServicesCount() {
      return this.services.filter(s => s.isActive).length;
    },
    inactiveServicesCount() {
      return this.services.filter(s => !s.isActive).length;
    },
    filteredServices() {
      let filtered = this.services;
      
      // Apply search filter
      if (this.searchQuery) {
        filtered = filtered.filter(service => 
          service.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          service.description.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      }
      
      // Apply status filter
      switch (this.currentFilter) {
        case 'active':
          filtered = filtered.filter(s => s.isActive);
          break;
        case 'inactive':
          filtered = filtered.filter(s => !s.isActive);
          break;
        case 'popular':
          filtered = filtered.filter(s => s.isPopular);
          break;
      }
      
      return filtered;
    }
  },
  methods: {
    openCreateModal() {
      this.resetForm();
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.editingService = null;
      this.resetForm();
    },
    saveService() {
      if (this.editingService) {
        // Update existing service
        const index = this.services.findIndex(s => s.id === this.editingService.id);
        this.services[index] = {
          ...this.editingService,
          ...this.form,
          usageCount: this.editingService.usageCount,
          trend: this.editingService.trend,
          trendPercentage: this.editingService.trendPercentage
        };
      } else {
        // Create new service
        const newService = {
          id: Date.now(),
          ...this.form,
          isActive: true,
          usageCount: 0,
          trend: 'stable',
          trendPercentage: 0
        };
        this.services.push(newService);
      }
      
      this.closeModal();
    },
    editService(service) {
      this.editingService = service;
      this.showModal = true;
      this.form = {
        name: service.name,
        category: service.category,
        price: service.price,
        priceType: service.priceType,
        description: service.description,
        icon: service.icon,
        color: service.color,
        isPopular: service.isPopular
      };
    },
    deleteService(service) {
      if (confirm('هل أنت متأكد من حذف هذه الخدمة؟')) {
        this.services = this.services.filter(s => s.id !== service.id);
      }
    },
    toggleServiceStatus(service) {
      service.isActive = !service.isActive;
    },
    getCategoryName(category) {
      const categoryNames = {
        photography: 'التصوير',
        catering: 'الضيافة',
        decoration: 'التزيين',
        entertainment: 'الترفيه',
        technical: 'تقني',
        other: 'أخرى'
      };
      return categoryNames[category] || 'غير معروف';
    },
    getTrendIcon(trend) {
      switch (trend) {
        case 'up': return 'fas fa-arrow-up';
        case 'down': return 'fas fa-arrow-down';
        default: return 'fas fa-minus';
      }
    },
    viewService(service) {
      // عرض تفاصيل الخدمة
      alert(`عرض تفاصيل: ${service.name}`);
    },
    resetForm() {
      this.form = {
        name: '',
        description: '',
        price: '',
        category: '',
        priceType: 'fixed',
        icon: 'fas fa-concierge-bell',
        color: '#667eea',
        isPopular: false
      };
    }
  }
}
</script>

<style scoped>
.services-page {
  padding: 0;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 16px rgba(102, 126, 234, 0.08);
}

.page-title {
  margin: 0;
  font-size: 2rem;
  font-weight: bold;
  color: #3b3663;
  letter-spacing: 0.5px;
}

.page-subtitle {
  margin: 0.5rem 0 0 0;
  color: #6b7280;
  font-size: 1rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  padding: 1.5rem;
  border-radius: 1rem;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.08);
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: box-shadow 0.2s;
}

.stat-card:hover {
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.13);
}

.stat-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}

.bg-blue { background: linear-gradient(135deg, #667eea 0%, #5f72bd 100%); }
.bg-green { background: linear-gradient(135deg, #10b981 0%, #43e97b 100%); }
.bg-purple { background: linear-gradient(135deg, #8b5cf6 0%, #764ba2 100%); }
.bg-orange { background: linear-gradient(135deg, #f59e0b 0%, #ffb347 100%); }

.stat-content h3 {
  font-size: 1.5rem;
  font-weight: bold;
  color: #3b3663;
  margin: 0;
}

.stat-content p {
  color: #6b7280;
  margin: 0;
  font-size: 0.95rem;
}

.filters-section {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  align-items: center;
  margin-bottom: 1.5rem;
}

.search-box {
  position: relative;
  flex: 1;
  max-width: 350px;
}

.search-box i {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #a0aec0;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem;
  padding-right: 2.5rem;
  border: 1.5px solid #e5e7eb;
  border-radius: 0.75rem;
  font-size: 1rem;
  background: #f8fafc;
  transition: border 0.2s, box-shadow 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 2px #667eea22;
}

.filter-buttons {
  display: flex;
  gap: 0.5rem;
}

.filter-btn {
  padding: 0.5rem 1.2rem;
  border: 1.5px solid #e5e7eb;
  background: white;
  border-radius: 0.75rem;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.2s;
  color: #3b3663;
}

.filter-btn.active, .filter-btn:focus {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-color: #667eea;
}

.filter-btn:hover {
  background: #f3f4f6;
}

.services-table-container {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.08);
  overflow: hidden;
}

.services-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 1rem;
}

.services-table thead {
  background: #f8fafc;
}

.services-table th {
  padding: 1rem;
  text-align: right;
  font-weight: 600;
  color: #3b3663;
  border-bottom: 1.5px solid #e5e7eb;
}

.services-table td {
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
  vertical-align: middle;
}

.services-table tbody tr:hover {
  background: #f3f4f6;
}

.service-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.service-icon-small {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.2rem;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.10);
}

.service-name {
  font-weight: 600;
  color: #3b3663;
  margin: 0;
}

.service-description {
  font-size: 0.92rem;
  color: #6b7280;
  margin: 0;
}

.category-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
  font-size: 0.8rem;
  font-weight: 500;
}

.category-badge.photography { background: #dbeafe; color: #1e40af; }
.category-badge.catering { background: #d1fae5; color: #065f46; }
.category-badge.decoration { background: #ede9fe; color: #5b21b6; }
.category-badge.entertainment { background: #fed7aa; color: #92400e; }
.category-badge.technical { background: #cffafe; color: #155e75; }
.category-badge.other { background: #f3f4f6; color: #374151; }

.price-cell {
  text-align: center;
}

.price {
  font-weight: 600;
  color: #059669;
}

.price-type {
  font-size: 0.8rem;
  color: #6b7280;
}

.usage-stats {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.25rem;
}

.usage-count {
  font-weight: 600;
  color: #3b3663;
}

.usage-trend {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.8rem;
}

.usage-trend.up { color: #059669; }
.usage-trend.down { color: #dc2626; }
.usage-trend.stable { color: #6b7280; }

/* Switch */
.switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 24px;
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
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
}

input:checked + .slider {
  background-color: #10b981;
}

input:checked + .slider:before {
  transform: translateX(24px);
}

.slider.round {
  border-radius: 24px;
}

.slider.round:before {
  border-radius: 50%;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
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
}

.modal-content {
  background: white;
  border-radius: 1rem;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 8px 32px rgba(102, 126, 234, 0.18);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.5rem;
  color: #3b3663;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #6b7280;
  cursor: pointer;
}

.service-form {
  padding: 1.5rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: span 2;
}

.form-group label {
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 0.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.375rem;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  padding: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 0.75rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

@media (max-width: 900px) {
  .stats-grid { grid-template-columns: 1fr 1fr; }
}

@media (max-width: 600px) {
  .page-header { flex-direction: column; gap: 1rem; text-align: center; }
  .stats-grid { grid-template-columns: 1fr; }
  .filters-section { flex-direction: column; }
  .search-box { max-width: 100%; }
  .filter-buttons { width: 100%; justify-content: space-between; }
  .services-table { font-size: 0.92rem; }
  .form-grid { grid-template-columns: 1fr; }
  .form-group.full-width { grid-column: span 1; }
}
</style> 