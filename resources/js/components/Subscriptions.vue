<template>
  <AppLayout>
    <div class="subscriptions-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
          <h1 class="page-title">
            <i class="fas fa-users"></i>
            إدارة الاشتراكات
          </h1>
          <p class="page-subtitle">متابعة وإدارة اشتراكات العملاء في الباقات المختلفة</p>
      </div>
      <div class="header-actions">
          <button @click="showCreateModal = true" class="btn btn-primary">
          <i class="fas fa-plus"></i>
          اشتراك جديد
        </button>
      </div>
    </div>

      <!-- Stats Cards -->
      <div class="stats-row">
      <div class="stat-card">
          <div class="stat-icon bg-primary">
          <i class="fas fa-users"></i>
        </div>
          <div class="stat-info">
            <h3>{{ stats.total }}</h3>
          <p>إجمالي الاشتراكات</p>
        </div>
      </div>
        
      <div class="stat-card">
          <div class="stat-icon bg-success">
          <i class="fas fa-check-circle"></i>
        </div>
          <div class="stat-info">
            <h3>{{ stats.active }}</h3>
            <p>نشط</p>
          </div>
        </div>
        
      <div class="stat-card">
          <div class="stat-icon bg-warning">
          <i class="fas fa-clock"></i>
        </div>
          <div class="stat-info">
            <h3>{{ stats.expiringSoon }}</h3>
            <p>ينتهي قريباً</p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon bg-danger">
            <i class="fas fa-times-circle"></i>
      </div>
          <div class="stat-info">
            <h3>{{ stats.expired }}</h3>
            <p>منتهي</p>
        </div>
      </div>
    </div>

      <!-- Filters -->
      <div class="filters-section">
      <div class="search-box">
        <i class="fas fa-search"></i>
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="البحث بالاسم أو البريد الإلكتروني..."
            class="search-input"
          >
      </div>
        
        <div class="filters">
          <select v-model="statusFilter" class="filter-select">
            <option value="">جميع الحالات</option>
          <option value="active">نشط</option>
            <option value="expiring">ينتهي قريباً</option>
          <option value="expired">منتهي</option>
          <option value="cancelled">ملغي</option>
        </select>
          
          <select v-model="packageFilter" class="filter-select">
            <option value="">جميع الباقات</option>
            <option v-for="pkg in packages" :key="pkg.id" :value="pkg.id">
              {{ pkg.name }}
            </option>
        </select>
      </div>
    </div>

      <!-- Subscriptions List -->
      <div class="subscriptions-list">
        <div v-for="subscription in filteredSubscriptions" :key="subscription.id" class="subscription-card">
          <div class="card-header">
            <div class="customer-info">
              <img :src="subscription.customer.avatar" class="customer-avatar">
              <div>
                <h4 class="customer-name">{{ subscription.customer.name }}</h4>
                <p class="customer-email">{{ subscription.customer.email }}</p>
              </div>
            </div>
            <div :class="getStatusClass(subscription.status)">
              {{ getStatusText(subscription.status) }}
            </div>
          </div>

          <div class="card-body">
            <div class="subscription-details">
              <div class="detail-item">
                <i class="fas fa-box"></i>
                <span class="label">الباقة:</span>
                <span class="value">{{ subscription.package.name }}</span>
              </div>
              
              <div class="detail-item">
                <i class="fas fa-calendar-alt"></i>
                <span class="label">تاريخ البداية:</span>
                <span class="value">{{ formatDate(subscription.startDate) }}</span>
              </div>
              
              <div class="detail-item">
                <i class="fas fa-calendar-check"></i>
                <span class="label">تاريخ الانتهاء:</span>
                <span class="value">{{ formatDate(subscription.endDate) }}</span>
                </div>
              
              <div class="detail-item">
                <i class="fas fa-hourglass-half"></i>
                <span class="label">المتبقي:</span>
                <span class="value" :class="getDaysClass(subscription.daysRemaining)">
                  {{ subscription.daysRemaining }} يوم
                </span>
              </div>
                </div>

            <div class="progress-section">
                <div class="progress-bar">
                <div 
                  class="progress-fill" 
                  :style="{ width: subscription.progress + '%' }"
                  :class="getProgressClass(subscription.progress)"
                ></div>
              </div>
              <span class="progress-text">{{ subscription.progress }}% مكتمل</span>
                </div>

            <div class="payment-info">
              <div class="payment-amount">
                <i class="fas fa-money-bill-wave"></i>
                {{ formatCurrency(subscription.amount) }}
              </div>
              <div class="payment-status" :class="subscription.paymentStatus">
                {{ subscription.paymentStatus === 'paid' ? 'مدفوع' : 'غير مدفوع' }}
              </div>
            </div>
    </div>

          <div class="card-footer">
            <button @click="viewDetails(subscription)" class="btn btn-outline-primary btn-sm">
              <i class="fas fa-eye"></i>
              التفاصيل
            </button>
            <button @click="renewSubscription(subscription)" class="btn btn-outline-success btn-sm">
              <i class="fas fa-sync"></i>
              تجديد
            </button>
            <button @click="editSubscription(subscription)" class="btn btn-outline-info btn-sm">
              <i class="fas fa-edit"></i>
              تعديل
            </button>
            <button @click="cancelSubscription(subscription)" class="btn btn-outline-danger btn-sm">
              <i class="fas fa-times"></i>
              إلغاء
            </button>
          </div>
        </div>
        
        <div v-if="filteredSubscriptions.length === 0" class="empty-state">
          <i class="fas fa-users-slash"></i>
          <p>لا توجد اشتراكات</p>
        </div>
      </div>
  </div>
  </AppLayout>
</template>

<script>
import AppLayout from './Layout/AppLayout.vue';

export default {
  name: 'Subscriptions',
  components: {
    AppLayout
  },
  data() {
    return {
      subscriptions: [
        {
          id: 1,
          customer: {
            name: 'أحمد محمد علي',
            email: 'ahmed@example.com',
            avatar: 'https://ui-avatars.com/api/?name=أحمد+محمد&background=667eea&color=fff&size=40&rounded=true'
          },
          package: {
            id: 2,
            name: 'الباقة الذهبية'
          },
          startDate: '2025-01-01',
          endDate: '2025-02-01',
          daysRemaining: 15,
          progress: 50,
          amount: 599,
          status: 'active',
          paymentStatus: 'paid'
        },
        {
          id: 2,
          customer: {
            name: 'فاطمة أحمد',
            email: 'fatima@example.com',
            avatar: 'https://ui-avatars.com/api/?name=فاطمة+أحمد&background=10b981&color=fff&size=40&rounded=true'
          },
          package: {
            id: 1,
            name: 'الباقة الأساسية'
          },
          startDate: '2024-12-15',
          endDate: '2025-01-15',
          daysRemaining: 3,
          progress: 90,
          amount: 299,
          status: 'expiring',
          paymentStatus: 'paid'
        },
        {
          id: 3,
          customer: {
            name: 'محمد سالم',
            email: 'mohammed@example.com',
            avatar: 'https://ui-avatars.com/api/?name=محمد+سالم&background=f59e0b&color=fff&size=40&rounded=true'
          },
          package: {
            id: 3,
            name: 'الباقة البلاتينية'
          },
          startDate: '2024-11-01',
          endDate: '2024-12-01',
          daysRemaining: -30,
          progress: 100,
          amount: 999,
          status: 'expired',
          paymentStatus: 'paid'
        }
      ],
      packages: [
        { id: 1, name: 'الباقة الأساسية' },
        { id: 2, name: 'الباقة الذهبية' },
        { id: 3, name: 'الباقة البلاتينية' }
      ],
      searchQuery: '',
      statusFilter: '',
      packageFilter: '',
      showCreateModal: false,
      stats: {
        total: 205,
        active: 168,
        expiringSoon: 23,
        expired: 14
      }
    }
  },
  computed: {
    filteredSubscriptions() {
      return this.subscriptions.filter(subscription => {
        const matchesSearch = 
          subscription.customer.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          subscription.customer.email.toLowerCase().includes(this.searchQuery.toLowerCase());
        
        const matchesStatus = !this.statusFilter || subscription.status === this.statusFilter;
        const matchesPackage = !this.packageFilter || subscription.package.id == this.packageFilter;
      
        return matchesSearch && matchesStatus && matchesPackage;
      });
    }
  },
  methods: {
    formatDate(date) {
      return new Date(date).toLocaleDateString('ar-SA');
    },
    formatCurrency(amount) {
      return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: 'SAR'
      }).format(amount);
    },
    getStatusClass(status) {
      const classes = {
        active: 'status-active',
        expiring: 'status-expiring',
        expired: 'status-expired',
        cancelled: 'status-cancelled'
      };
      return `status-badge ${classes[status]}`;
    },
    getStatusText(status) {
      const texts = {
        active: 'نشط',
        expiring: 'ينتهي قريباً',
        expired: 'منتهي',
        cancelled: 'ملغي'
      };
      return texts[status];
    },
    getDaysClass(days) {
      if (days <= 0) return 'text-danger';
      if (days <= 7) return 'text-warning';
      return 'text-success';
    },
    getProgressClass(progress) {
      if (progress >= 90) return 'progress-danger';
      if (progress >= 70) return 'progress-warning';
      return 'progress-success';
    },
    viewDetails(subscription) {
      console.log('View subscription details:', subscription);
    },
    editSubscription(subscription) {
      console.log('Edit subscription:', subscription);
    },
    renewSubscription(subscription) {
      if (confirm(`هل تريد تجديد اشتراك ${subscription.customer.name}؟`)) {
        // تجديد الاشتراك
        const newEndDate = new Date(subscription.endDate);
        newEndDate.setMonth(newEndDate.getMonth() + 1);
        subscription.endDate = newEndDate.toISOString().split('T')[0];
        subscription.status = 'active';
        subscription.daysRemaining = 30;
        subscription.progress = 0;
      }
    },
    cancelSubscription(subscription) {
      if (confirm(`هل أنت متأكد من إلغاء اشتراك ${subscription.customer.name}؟`)) {
        subscription.status = 'cancelled';
      }
    }
  }
}
</script>

<style scoped>
.subscriptions-page {
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

/* Stats Row */
.stats-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.stat-icon {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
}

.stat-info h3 {
  margin: 0;
  font-size: 1.75rem;
  font-weight: bold;
  color: #333;
}

.stat-info p {
  margin: 0;
  color: #666;
  font-size: 0.875rem;
}

/* Filters Section */
.filters-section {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 1rem;
  align-items: center;
  flex-wrap: wrap;
}

.search-box {
  flex: 1;
  min-width: 300px;
  position: relative;
}

.search-box i {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #999;
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filters {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.filter-select {
  padding: 0.75rem 1rem;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 0.95rem;
  background: white;
  transition: all 0.3s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #667eea;
}

/* Subscriptions List */
.subscriptions-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 1.5rem;
}

.subscription-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.3s ease;
}

.subscription-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.card-header {
  padding: 1.5rem;
  border-bottom: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.customer-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.customer-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
}

.customer-name {
  margin: 0;
  font-size: 1.1rem;
  font-weight: bold;
  color: #333;
}

.customer-email {
  margin: 0;
  font-size: 0.875rem;
  color: #666;
}

.status-badge {
  padding: 0.375rem 0.75rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
}

.status-active {
  background: #d1fae5;
  color: #065f46;
}

.status-expiring {
  background: #fef3c7;
  color: #92400e;
}

.status-expired {
  background: #fee2e2;
  color: #991b1b;
}

.status-cancelled {
  background: #e5e7eb;
  color: #374151;
}

.card-body {
  padding: 1.5rem;
}

.subscription-details {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.detail-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.detail-item i {
  color: #667eea;
  width: 16px;
}

.detail-item .label {
  color: #666;
}

.detail-item .value {
  font-weight: 600;
  color: #333;
}

.progress-section {
  margin-bottom: 1.5rem;
}

.progress-bar {
  width: 100%;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 0.5rem;
}

.progress-fill {
  height: 100%;
  transition: width 0.3s ease;
}

.progress-success {
  background: linear-gradient(to right, #10b981, #059669);
}

.progress-warning {
  background: linear-gradient(to right, #f59e0b, #d97706);
}

.progress-danger {
  background: linear-gradient(to right, #ef4444, #dc2626);
}

.progress-text {
  font-size: 0.875rem;
  color: #666;
}

.payment-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.payment-amount {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.25rem;
  font-weight: bold;
  color: #333;
}

.payment-amount i {
  color: #10b981;
}

.payment-status {
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-size: 0.875rem;
  font-weight: 500;
}

.payment-status.paid {
  background: #d1fae5;
  color: #065f46;
}

.payment-status.unpaid {
  background: #fee2e2;
  color: #991b1b;
}

.card-footer {
  padding: 1rem 1.5rem;
  background: #f8f9fa;
  display: flex;
  gap: 0.5rem;
  justify-content: center;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 4rem;
  color: #9ca3af;
}

.empty-state i {
  font-size: 4rem;
  margin-bottom: 1rem;
  opacity: 0.3;
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

.btn-sm {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

.btn-outline-primary {
  background: transparent;
  color: #667eea;
  border: 1px solid #667eea;
}

.btn-outline-success {
  background: transparent;
  color: #10b981;
  border: 1px solid #10b981;
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

.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-info:hover,
.btn-outline-danger:hover {
  color: white;
}

.btn-outline-primary:hover {
  background: #667eea;
}

.btn-outline-success:hover {
  background: #10b981;
}

.btn-outline-info:hover {
  background: #06b6d4;
}

.btn-outline-danger:hover {
  background: #ef4444;
}

/* Utilities */
.bg-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.bg-warning {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.bg-danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.text-success {
  color: #10b981;
}

.text-warning {
  color: #f59e0b;
}

.text-danger {
  color: #ef4444;
}

/* Responsive */
@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .stats-row {
    grid-template-columns: 1fr 1fr;
  }
  
  .filters-section {
    flex-direction: column;
  }
  
  .search-box {
    min-width: 100%;
  }
  
  .filters {
    width: 100%;
    flex-direction: column;
  }
  
  .filter-select {
    width: 100%;
  }
  
  .subscriptions-list {
    grid-template-columns: 1fr;
  }
  
  .subscription-details {
    grid-template-columns: 1fr;
  }
}
</style> 