<template>
  <AppLayout>
    <div class="halls-page">
      <!-- Header -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">إدارة القاعات</h1>
          <p class="page-subtitle">إدارة جميع القاعات والمرافق المتاحة</p>
        </div>
        <div class="header-actions">
          <button @click="openCreateModal" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            إضافة قاعة جديدة
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="filters-section">
        <div class="search-box">
          <input 
            v-model="filters.search" 
            type="text" 
            placeholder="البحث في القاعات..."
            class="search-input"
          >
          <i class="fas fa-search search-icon"></i>
        </div>
        
        <div class="filter-options">
          <select v-model="filters.status" class="filter-select">
            <option value="">جميع الحالات</option>
            <option value="available">متاحة</option>
            <option value="booked">محجوزة</option>
            <option value="maintenance">صيانة</option>
          </select>
          
          <select v-model="filters.capacity" class="filter-select">
            <option value="">جميع الأحجام</option>
            <option value="small">صغيرة (أقل من 100)</option>
            <option value="medium">متوسطة (100-300)</option>
            <option value="large">كبيرة (أكثر من 300)</option>
          </select>
        </div>
      </div>

      <!-- Loading state -->
      <div v-if="loading" class="loading-state">
        <div class="loader"></div>
        <p>جاري تحميل القاعات...</p>
      </div>

      <!-- Halls Grid -->
      <div v-else-if="halls.length > 0" class="halls-grid">
        <div v-for="hall in halls" :key="hall.id" class="hall-card">
          <div class="hall-image">
            <img :src="hall.image" :alt="hall.name">
            <div class="hall-status" :class="getStatusClass(hall.status)">
              {{ getStatusText(hall.status) }}
            </div>
          </div>
          
          <div class="hall-content">
            <h3 class="hall-name">{{ hall.name }}</h3>
            <p class="hall-description">{{ hall.description }}</p>
            
            <div class="hall-details">
              <div class="detail-item">
                <i class="fas fa-users"></i>
                <span>السعة: {{ hall.capacity }} شخص</span>
              </div>
              <div class="detail-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ hall.location }}</span>
              </div>
              <div class="detail-item">
                <i class="fas fa-money-bill-wave"></i>
                <span>{{ hall.price_per_hour }} ر.س/ساعة</span>
              </div>
            </div>
            
            <div class="hall-amenities">
              <span v-for="amenity in hall.amenities.slice(0, 3)" :key="amenity" class="amenity-tag">
                {{ amenity }}
              </span>
              <span v-if="hall.amenities.length > 3" class="amenity-more">
                +{{ hall.amenities.length - 3 }} أكثر
              </span>
            </div>
          </div>
          
          <div class="hall-actions">
            <button @click="viewHall(hall)" class="btn btn-outline-primary btn-sm">
              <i class="fas fa-eye"></i>
              عرض
            </button>
            <button @click="editHall(hall)" class="btn btn-outline-info btn-sm">
              <i class="fas fa-edit"></i>
              تعديل
            </button>
            <button @click="deleteHall(hall)" class="btn btn-outline-danger btn-sm">
              <i class="fas fa-trash"></i>
              حذف
            </button>
          </div>
        </div>
      </div>
      
      <!-- Empty State -->
      <div v-else class="empty-state">
        <i class="fas fa-building"></i>
        <h3>لا توجد قاعات</h3>
        <p>لم يتم العثور على قاعات تطابق معايير البحث</p>
        <button @click="openCreateModal" class="btn btn-primary">
          إضافة قاعة جديدة
        </button>
      </div>

      <!-- Pagination Controls -->
      <div v-if="!loading && pagination.last_page > 1" class="pagination-controls">
        <button @click="fetchHalls(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="btn">
          <i class="fas fa-chevron-right"></i> السابق
        </button>
        <span>صفحة {{ pagination.current_page }} من {{ pagination.last_page }}</span>
        <button @click="fetchHalls(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="btn">
          التالي <i class="fas fa-chevron-left"></i>
        </button>
      </div>

      <!-- Create/Edit Modal -->
      <div v-if="showCreateModal || showEditModal" class="modal-overlay" @click="closeModal">
        <div class="modal" @click.stop>
          <div class="modal-header">
            <h3>{{ showEditModal ? 'تعديل القاعة' : 'إضافة قاعة جديدة' }}</h3>
            <button @click="closeModal" class="close-btn">
              <i class="fas fa-times"></i>
            </button>
          </div>
          
          <form @submit.prevent="saveHall" class="modal-body">
            <div class="form-group">
              <label>اسم القاعة</label>
              <input v-model="form.name" type="text" required class="form-control">
            </div>
            
            <div class="form-group">
              <label>الوصف</label>
              <textarea v-model="form.description" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label>السعة</label>
                <input v-model="form.capacity" type="number" required class="form-control">
              </div>
              <div class="form-group">
                <label>السعر بالساعة</label>
                <input v-model="form.price_per_hour" type="number" required class="form-control">
              </div>
            </div>
            
            <div class="form-group">
              <label>الموقع</label>
              <input v-model="form.location" type="text" class="form-control">
            </div>
            
            <div class="form-group">
              <label>المرافق</label>
              <div class="amenities-input">
                <div v-for="(amenity, index) in form.amenities" :key="index" class="amenity-input">
                  <input v-model="form.amenities[index]" type="text" class="form-control">
                  <button @click="removeAmenity(index)" type="button" class="remove-btn">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                <button @click="addAmenity" type="button" class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-plus"></i>
                  إضافة مرفق
                </button>
              </div>
            </div>
            
            <div class="form-group">
              <label class="checkbox-label">
                <input v-model="form.is_available" type="checkbox">
                متاحة للحجز
              </label>
            </div>
          </form>
          
          <div class="modal-footer">
            <button @click="closeModal" class="btn btn-secondary">إلغاء</button>
            <button @click="saveHall" class="btn btn-primary">
              {{ showEditModal ? 'تحديث' : 'إضافة' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from './Layout/AppLayout.vue';
import axios from 'axios';
import debounce from 'lodash/debounce';

export default {
  name: 'Halls',
  components: {
    AppLayout
  },
  data() {
    return {
      loading: true,
      halls: [],
      pagination: {},
      filters: {
        search: '',
        status: '',
        capacity: ''
      },
      showCreateModal: false,
      showEditModal: false,
      editingHallId: null,
      form: {
        name: '',
        description: '',
        capacity: null,
        price_per_hour: null,
        location: '',
        amenities: [],
        is_available: true
      }
    }
  },
  watch: {
    'filters.search': debounce(function() {
      this.fetchHalls();
    }, 300),
    'filters.status'() {
      this.fetchHalls();
    },
    'filters.capacity'() {
      this.fetchHalls();
    }
  },
  created() {
    this.fetchHalls();
  },
  methods: {
    async fetchHalls(page = 1) {
      this.loading = true;
      try {
        const params = {
          page,
          ...this.filters
        };
        const response = await axios.get('/api/halls', { params });
        this.halls = response.data.data;
        this.pagination = response.data.meta;
      } catch (error) {
        console.error('Error fetching halls:', error);
      } finally {
        this.loading = false;
      }
    },
    async saveHall() {
      const isEditing = !!this.editingHallId;
      const url = isEditing ? `/api/halls/${this.editingHallId}` : '/api/halls';
      const method = isEditing ? 'put' : 'post';

      try {
        await axios[method](url, this.form);
        this.closeModal();
        this.fetchHalls(); // Refresh list
      } catch (error) {
        console.error('Error saving hall:', error);
      }
    },
    async deleteHall(hall) {
      if (!confirm(`هل أنت متأكد من رغبتك في حذف "${hall.name}"؟`)) {
        return;
      }
      try {
        await axios.delete(`/api/halls/${hall.id}`);
        this.fetchHalls(); // Refresh list
      } catch (error) {
        console.error('Error deleting hall:', error);
      }
    },
    editHall(hall) {
      this.editingHallId = hall.id;
      this.form = { ...hall };
      this.showEditModal = true;
    },
    openCreateModal() {
      this.editingHallId = null;
      this.form = {
        name: '',
        description: '',
        capacity: null,
        price_per_hour: null,
        location: '',
        amenities: [''],
        is_available: true
      };
      this.showCreateModal = true;
    },
    closeModal() {
      this.showCreateModal = false;
      this.showEditModal = false;
      this.editingHallId = null;
    },
    addAmenity() {
      if (!this.form.amenities) {
        this.form.amenities = [];
      }
      this.form.amenities.push('');
    },
    removeAmenity(index) {
      this.form.amenities.splice(index, 1);
    },
    getStatusClass(status) {
      const classes = { available: 'status-available', booked: 'status-booked', maintenance: 'status-maintenance' };
      return classes[status] || 'status-default';
    },
    getStatusText(status) {
      const texts = { available: 'متاحة', booked: 'محجوزة', maintenance: 'صيانة' };
      return texts[status] || 'غير معروف';
    },
    viewHall(hall) {
      // Navigate to hall details
      console.log('View hall:', hall);
    }
  }
}
</script>

<style scoped>
.halls-page {
  padding: 0;
}

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

.page-title {
  margin: 0;
  font-size: 1.75rem;
  font-weight: bold;
  color: #333;
}

.page-subtitle {
  margin: 0.5rem 0 0 0;
  color: #666;
}

.filters-section {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.search-box {
  position: relative;
  flex: 1;
  min-width: 300px;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 0.875rem;
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
}

.filter-options {
  display: flex;
  gap: 0.75rem;
}

.filter-select {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: white;
  min-width: 150px;
}

.halls-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.hall-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.hall-card:hover {
  transform: translateY(-4px);
}

.hall-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.hall-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hall-status {
  position: absolute;
  top: 1rem;
  right: 1rem;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
  color: white;
}

.status-available {
  background: #10b981;
}

.status-booked {
  background: #f59e0b;
}

.status-maintenance {
  background: #ef4444;
}

.hall-content {
  padding: 1.5rem;
}

.hall-name {
  margin: 0 0 0.5rem 0;
  font-size: 1.25rem;
  font-weight: bold;
  color: #333;
}

.hall-description {
  margin: 0 0 1rem 0;
  color: #666;
  line-height: 1.5;
}

.hall-details {
  margin-bottom: 1rem;
}

.detail-item {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  color: #666;
}

.detail-item i {
  margin-left: 0.5rem;
  width: 16px;
  color: #667eea;
}

.hall-amenities {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.amenity-tag {
  background: #f1f5f9;
  color: #475569;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
}

.amenity-more {
  color: #667eea;
  font-size: 0.75rem;
  font-weight: 500;
}

.hall-actions {
  padding: 1rem 1.5rem;
  border-top: 1px solid #f1f5f9;
  display: flex;
  gap: 0.5rem;
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.empty-state i {
  font-size: 4rem;
  color: #cbd5e1;
  margin-bottom: 1rem;
}

.empty-state h3 {
  margin: 0 0 0.5rem 0;
  color: #475569;
}

.empty-state p {
  margin: 0 0 2rem 0;
  color: #64748b;
}

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

.modal {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: bold;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.25rem;
  cursor: pointer;
  color: #666;
}

.modal-body {
  padding: 1.5rem;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #333;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 0.875rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.amenities-input {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.amenity-input {
  display: flex;
  gap: 0.5rem;
}

.remove-btn {
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 4px;
  width: 32px;
  cursor: pointer;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid #eee;
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 6px;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 500;
  transition: all 0.3s ease;
  border: none;
  cursor: pointer;
}

.btn-primary {
  background: #667eea;
  color: white;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-outline-primary {
  background: transparent;
  color: #667eea;
  border: 1px solid #667eea;
}

.btn-outline-info {
  background: transparent;
  color: #06b6d4;
  border: 1px solid #06b6d4;
}

.btn-outline-danger {
  background: transparent;
  color: #ef4444;
  border: 1px solid #ef4444;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .filters-section {
    flex-direction: column;
  }
  
  .search-box {
    min-width: auto;
  }
  
  .filter-options {
    flex-direction: column;
  }
  
  .halls-grid {
    grid-template-columns: 1fr;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem;
  color: #6c757d;
}

.loader {
  border: 5px solid #f3f3f3;
  border-top: 5px solid #667eea;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.pagination-controls {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
}

.pagination-controls .btn {
  background-color: white;
  border: 1px solid #dee2e6;
}

.pagination-controls .btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style> 