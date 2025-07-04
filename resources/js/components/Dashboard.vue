<template>
  <AppLayout>
    <div v-if="loading" class="loader-container">
      <div class="loader"></div>
    </div>
    <div v-else class="dashboard">
      <!-- إحصائيات سريعة -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="fas fa-building"></i>
          </div>
          <div class="stat-content">
            <h3 class="stat-number">{{ stats.totalHalls }}</h3>
            <p class="stat-label">إجمالي القاعات</p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="stat-content">
            <h3 class="stat-number">{{ stats.activeBookings }}</h3>
            <p class="stat-label">الحجوزات النشطة</p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon bg-info">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-content">
            <h3 class="stat-number">{{ stats.totalCustomers }}</h3>
            <p class="stat-label">إجمالي العملاء</p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <div class="stat-content">
            <h3 class="stat-number">{{ stats.monthlyRevenue }}</h3>
            <p class="stat-label">الإيرادات الشهرية</p>
          </div>
        </div>
      </div>

      <div class="dashboard-content">
        <!-- العمود الأول -->
        <div class="content-column">
          <!-- آخر الحجوزات -->
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fas fa-calendar-alt"></i>
                آخر الحجوزات
              </h5>
              <router-link to="/bookings" class="btn btn-primary btn-sm">
                <i class="fas fa-eye"></i>
                عرض الكل
              </router-link>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>العميل</th>
                      <th>القاعة</th>
                      <th>التاريخ</th>
                      <th>الحالة</th>
                      <th>المبلغ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="booking in recentBookings" :key="booking.id">
                      <td>
                        <div class="customer-info">
                          <img :src="booking.customer.avatar" class="customer-avatar">
                          <span>{{ booking.customer.name }}</span>
                        </div>
                      </td>
                      <td>{{ booking.hall.name }}</td>
                      <td>{{ formatDate(booking.event_date) }}</td>
                      <td>
                        <span :class="getStatusClass(booking.status)">
                          {{ getStatusText(booking.status) }}
                        </span>
                      </td>
                      <td class="amount">{{ booking.total_amount }} ر.س</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- الرسم البياني -->
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fas fa-chart-line"></i>
                الإيرادات الشهرية
              </h5>
            </div>
            <div class="card-body">
              <div class="chart-placeholder">
                <i class="fas fa-chart-area"></i>
                <p>الرسم البياني سيتم تطويره قريباً</p>
                <small>بيانات الإيرادات والتحليلات المفصلة</small>
              </div>
            </div>
          </div>
        </div>

        <!-- العمود الثاني -->
        <div class="content-column">
          <!-- حالة النظام -->
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fas fa-cogs"></i>
                حالة النظام
              </h5>
            </div>
            <div class="card-body">
              <div class="system-status">
                <div class="status-item">
                  <span>أداء الخادم</span>
                  <span class="badge bg-success">ممتاز</span>
                </div>
                <div class="progress mb-3">
                  <div class="progress-bar bg-success" style="width: 92%"></div>
                </div>

                <div class="status-item">
                  <span>استخدام قاعدة البيانات</span>
                  <span class="badge bg-warning">متوسط</span>
                </div>
                <div class="progress mb-3">
                  <div class="progress-bar bg-warning" style="width: 67%"></div>
                </div>

                <div class="status-item">
                  <span>حركة المرور</span>
                  <span class="badge bg-info">نشط</span>
                </div>
                <div class="progress mb-3">
                  <div class="progress-bar bg-info" style="width: 84%"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- القاعات المتاحة -->
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fas fa-building"></i>
                القاعات المتاحة اليوم
              </h5>
              <span class="badge bg-success">{{ availableHalls.length }} متاحة</span>
            </div>
            <div class="card-body">
              <div class="halls-list">
                <div v-for="hall in availableHalls" :key="hall.id" class="hall-item">
                  <img :src="hall.image" class="hall-image">
                  <div class="hall-info">
                    <span class="hall-name">{{ hall.name }}</span>
                    <small>سعة: {{ hall.capacity }} شخص</small>
                  </div>
                  <span :class="getAvailabilityClass(hall.status)">
                    {{ getAvailabilityText(hall.status) }}
                  </span>
                </div>
              </div>
              <div class="text-center mt-3">
                <router-link to="/halls" class="btn btn-outline-primary btn-sm">
                  <i class="fas fa-eye"></i>
                  عرض جميع القاعات
                </router-link>
              </div>
            </div>
          </div>

          <!-- الإجراءات السريعة -->
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">
                <i class="fas fa-bolt"></i>
                الإجراءات السريعة
              </h5>
            </div>
            <div class="card-body">
              <div class="quick-actions">
                <router-link to="/bookings" class="btn btn-primary">
                  <i class="fas fa-plus"></i>
                  حجز جديد
                </router-link>
                <router-link to="/customers" class="btn btn-success">
                  <i class="fas fa-user-plus"></i>
                  إضافة عميل
                </router-link>
                <router-link to="/halls" class="btn btn-info">
                  <i class="fas fa-building"></i>
                  إدارة القاعات
                </router-link>
                <router-link to="/reports" class="btn btn-warning">
                  <i class="fas fa-chart-bar"></i>
                  تقارير مفصلة
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from './Layout/AppLayout.vue';
import axios from 'axios';

export default {
  name: 'Dashboard',
  components: {
    AppLayout
  },
  data() {
    return {
      loading: true,
      stats: {
        totalHalls: 0,
        activeBookings: 0,
        totalCustomers: 0,
        monthlyRevenue: '0.00'
      },
      recentBookings: [],
      availableHalls: []
    }
  },
  mounted() {
    this.checkAuth();
    this.fetchDashboardData();
  },
    methods: {
    checkAuth() {
      const token = localStorage.getItem('auth_token');
      const user = localStorage.getItem('user');
      
      console.log('Checking auth - token:', token);
      console.log('Checking auth - user:', user);
      
      if (!token || !user) {
        console.log('No token or user found, redirecting to login');
        this.$router.push('/auth/login');
        return;
      }
      
      // إعادة تعيين الـ token في axios headers
      axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
      console.log('Token set in axios headers');
    },
    async fetchDashboardData() {
      this.loading = true;
      try {
        console.log('Fetching dashboard data...');
        const response = await axios.get('/api/dashboard/stats');
        console.log('Dashboard data received:', response.data);
        const data = response.data;
        this.stats = data.stats;
        this.recentBookings = data.recentBookings;
        this.availableHalls = data.availableHalls;
      } catch (error) {
        console.error("Error fetching dashboard data:", error);
        
        // إذا كان الخطأ 401، توجيه إلى صفحة تسجيل الدخول
        if (error.response && error.response.status === 401) {
          console.log('401 error, redirecting to login');
          localStorage.removeItem('auth_token');
          localStorage.removeItem('user');
          this.$router.push('/auth/login');
          return;
        }
        
        // عرض رسالة خطأ للمستخدم
        alert('حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.');
      } finally {
        this.loading = false;
      }
    },
    formatDate(dateString) {
      if (!dateString) return '';
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('ar-EG', options);
    },
    getStatusClass(status) {
      const classes = {
        confirmed: 'status-confirmed',
        pending: 'status-pending',
        cancelled: 'status-cancelled'
      };
      return ['status-badge', classes[status] || 'status-default'];
    },
    getStatusText(status) {
      const texts = {
        confirmed: 'مؤكد',
        pending: 'قيد الانتظار',
        cancelled: 'ملغي'
      };
      return texts[status] || 'غير معروف';
    },
    getAvailabilityClass(status) {
      return status === 'available' ? 'availability-available' : 'availability-occupied';
    },
    getAvailabilityText(status) {
      return status === 'available' ? 'متاحة' : 'محجوزة';
    }
  }
}
</script>

<style scoped>
.dashboard {
  padding: 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
  margin-left: 1rem;
}

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: 2rem;
  font-weight: bold;
  margin: 0;
  color: #333;
}

.stat-label {
  margin: 0;
  color: #666;
  font-size: 0.875rem;
}

.dashboard-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
}

.content-column {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.card-header {
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-title {
  margin: 0;
  font-size: 1.1rem;
  font-weight: bold;
  color: #333;
}

.card-title i {
  margin-left: 0.5rem;
  color: #667eea;
}

.card-body {
  padding: 1.5rem;
}

.table {
  margin: 0;
}

.table th {
  border: none;
  font-weight: 600;
  color: #666;
  font-size: 0.875rem;
}

.table td {
  border: none;
  padding: 1rem 0.75rem;
  vertical-align: middle;
}

.customer-info {
  display: flex;
  align-items: center;
}

.customer-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  margin-left: 0.5rem;
}

.amount {
  font-weight: bold;
  color: #28a745;
}

.chart-placeholder {
  height: 300px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border: 2px dashed #cbd5e1;
  border-radius: 12px;
  color: #667eea;
}

.chart-placeholder i {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.3;
}

.chart-placeholder p {
  margin: 0;
  color: #666;
}

.system-status .status-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.progress {
  height: 8px;
  border-radius: 10px;
  background-color: #f1f5f9;
}

.progress-bar {
  border-radius: 10px;
}

.halls-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.hall-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f1f5f9;
}

.hall-item:last-child {
  border-bottom: none;
}

.hall-image {
  width: 50px;
  height: 30px;
  border-radius: 6px;
  object-fit: cover;
  margin-left: 0.75rem;
}

.hall-info {
  flex: 1;
}

.hall-name {
  font-weight: 600;
  display: block;
}

.quick-actions {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.quick-actions .btn {
  width: 100%;
  text-align: center;
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

.btn-success {
  background: #10b981;
  color: white;
}

.btn-info {
  background: #06b6d4;
  color: white;
}

.btn-warning {
  background: #f59e0b;
  color: white;
}

.btn-outline-primary {
  background: transparent;
  color: #667eea;
  border: 1px solid #667eea;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.badge {
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 500;
}

.bg-success {
  background: #10b981 !important;
  color: white;
}

.bg-warning {
  background: #f59e0b !important;
  color: white;
}

.bg-info {
  background: #06b6d4 !important;
  color: white;
}

.bg-danger {
  background: #ef4444 !important;
  color: white;
}

.bg-primary {
  background: #667eea !important;
  color: white;
}

@media (max-width: 768px) {
  .dashboard-content {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  }
}

/* Add a simple loader */
.loader-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 80vh; /* Adjust as needed */
}

.loader {
  border: 5px solid #f3f3f3;
  border-top: 5px solid #667eea;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style> 