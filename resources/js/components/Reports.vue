<template>
  <AppLayout>
    <div class="reports-page">
      <!-- Header -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">
            <i class="fas fa-chart-line"></i>
            التقارير والإحصائيات
          </h1>
          <p class="page-subtitle">تحليلات شاملة لأداء النظام والإيرادات</p>
        </div>
        <div class="header-actions">
          <button @click="exportReport" class="btn btn-primary">
            <i class="fas fa-download"></i>
            تصدير التقرير
          </button>
        </div>
      </div>

      <!-- Date Range Selector -->
      <div class="date-range-selector">
        <div class="date-inputs">
          <div class="date-group">
            <label>من تاريخ</label>
            <input v-model="dateRange.from" type="date" class="date-input">
          </div>
          <div class="date-group">
            <label>إلى تاريخ</label>
            <input v-model="dateRange.to" type="date" class="date-input">
          </div>
          <button @click="applyDateRange" class="btn btn-secondary">
            <i class="fas fa-filter"></i>
            تطبيق
          </button>
        </div>
        <div class="quick-ranges">
          <button @click="setQuickRange('today')" class="range-btn" :class="{ active: activeRange === 'today' }">
            اليوم
          </button>
          <button @click="setQuickRange('week')" class="range-btn" :class="{ active: activeRange === 'week' }">
            هذا الأسبوع
          </button>
          <button @click="setQuickRange('month')" class="range-btn" :class="{ active: activeRange === 'month' }">
            هذا الشهر
          </button>
          <button @click="setQuickRange('year')" class="range-btn" :class="{ active: activeRange === 'year' }">
            هذه السنة
          </button>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="summary-cards">
        <div class="summary-card">
          <div class="card-icon revenue">
            <i class="fas fa-dollar-sign"></i>
          </div>
          <div class="card-content">
            <h3>{{ formatCurrency(stats.totalRevenue) }}</h3>
            <p>إجمالي الإيرادات</p>
            <span class="trend positive">
              <i class="fas fa-arrow-up"></i>
              +15.3%
            </span>
          </div>
        </div>

        <div class="summary-card">
          <div class="card-icon bookings">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="card-content">
            <h3>{{ stats.totalBookings }}</h3>
            <p>إجمالي الحجوزات</p>
            <span class="trend positive">
              <i class="fas fa-arrow-up"></i>
              +8.7%
            </span>
          </div>
        </div>

        <div class="summary-card">
          <div class="card-icon customers">
            <i class="fas fa-users"></i>
          </div>
          <div class="card-content">
            <h3>{{ stats.newCustomers }}</h3>
            <p>عملاء جدد</p>
            <span class="trend positive">
              <i class="fas fa-arrow-up"></i>
              +23.1%
            </span>
          </div>
        </div>

        <div class="summary-card">
          <div class="card-icon occupancy">
            <i class="fas fa-percentage"></i>
          </div>
          <div class="card-content">
            <h3>{{ stats.occupancyRate }}%</h3>
            <p>معدل الإشغال</p>
            <span class="trend negative">
              <i class="fas fa-arrow-down"></i>
              -2.4%
            </span>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="charts-grid">
        <!-- Revenue Chart -->
        <div class="chart-card">
          <div class="chart-header">
            <h3>الإيرادات الشهرية</h3>
            <select v-model="revenueChartType" class="chart-select">
              <option value="line">خطي</option>
              <option value="bar">أعمدة</option>
              <option value="area">منطقة</option>
            </select>
          </div>
          <div class="chart-body">
            <div class="chart-placeholder">
              <i class="fas fa-chart-line"></i>
              <p>رسم بياني للإيرادات</p>
              <div class="mock-chart">
                <div class="bar" style="height: 60%"></div>
                <div class="bar" style="height: 80%"></div>
                <div class="bar" style="height: 70%"></div>
                <div class="bar" style="height: 90%"></div>
                <div class="bar" style="height: 85%"></div>
                <div class="bar" style="height: 95%"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Bookings by Hall Chart -->
        <div class="chart-card">
          <div class="chart-header">
            <h3>الحجوزات حسب القاعة</h3>
          </div>
          <div class="chart-body">
            <div class="pie-chart-placeholder">
              <div class="pie-chart">
                <div class="pie-slice slice-1"></div>
                <div class="pie-slice slice-2"></div>
                <div class="pie-slice slice-3"></div>
                <div class="pie-slice slice-4"></div>
              </div>
              <div class="pie-legend">
                <div class="legend-item">
                  <span class="color-box" style="background: #667eea"></span>
                  قاعة الماسة (35%)
                </div>
                <div class="legend-item">
                  <span class="color-box" style="background: #10b981"></span>
                  قاعة النخبة (28%)
                </div>
                <div class="legend-item">
                  <span class="color-box" style="background: #f59e0b"></span>
                  قاعة الزمرد (22%)
                </div>
                <div class="legend-item">
                  <span class="color-box" style="background: #ef4444"></span>
                  أخرى (15%)
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Detailed Tables -->
      <div class="tables-section">
        <!-- Top Services -->
        <div class="table-card">
          <div class="table-header">
            <h3>الخدمات الأكثر طلباً</h3>
          </div>
          <table class="data-table">
            <thead>
              <tr>
                <th>الخدمة</th>
                <th>عدد الطلبات</th>
                <th>الإيرادات</th>
                <th>النسبة</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="service in topServices" :key="service.id">
                <td>{{ service.name }}</td>
                <td>{{ service.count }}</td>
                <td>{{ formatCurrency(service.revenue) }}</td>
                <td>
                  <div class="progress-bar">
                    <div class="progress-fill" :style="{ width: service.percentage + '%' }"></div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Top Customers -->
        <div class="table-card">
          <div class="table-header">
            <h3>أفضل العملاء</h3>
          </div>
          <table class="data-table">
            <thead>
              <tr>
                <th>العميل</th>
                <th>عدد الحجوزات</th>
                <th>إجمالي الإنفاق</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="customer in topCustomers" :key="customer.id">
                <td>
                  <div class="customer-info">
                    <img :src="customer.avatar" class="customer-avatar">
                    {{ customer.name }}
                  </div>
                </td>
                <td>{{ customer.bookings }}</td>
                <td>{{ formatCurrency(customer.totalSpent) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Export Options Modal -->
      <div v-if="showExportModal" class="modal-overlay" @click="showExportModal = false">
        <div class="modal" @click.stop>
          <div class="modal-header">
            <h3>تصدير التقرير</h3>
            <button @click="showExportModal = false" class="close-btn">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <div class="modal-body">
            <div class="export-options">
              <label class="export-option">
                <input type="radio" v-model="exportFormat" value="pdf">
                <i class="fas fa-file-pdf"></i>
                PDF
              </label>
              <label class="export-option">
                <input type="radio" v-model="exportFormat" value="excel">
                <i class="fas fa-file-excel"></i>
                Excel
              </label>
              <label class="export-option">
                <input type="radio" v-model="exportFormat" value="csv">
                <i class="fas fa-file-csv"></i>
                CSV
              </label>
            </div>
          </div>
          <div class="modal-footer">
            <button @click="downloadReport" class="btn btn-primary">
              <i class="fas fa-download"></i>
              تحميل
            </button>
            <button @click="showExportModal = false" class="btn btn-secondary">
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
  name: 'Reports',
  components: {
    AppLayout
  },
  data() {
    return {
      dateRange: {
        from: '',
        to: ''
      },
      activeRange: 'month',
      revenueChartType: 'bar',
      showExportModal: false,
      exportFormat: 'pdf',
      stats: {
        totalRevenue: 485000,
        totalBookings: 127,
        newCustomers: 45,
        occupancyRate: 78
      },
      topServices: [
        { id: 1, name: 'خدمة التصوير', count: 85, revenue: 42500, percentage: 85 },
        { id: 2, name: 'تنسيق الزهور', count: 72, revenue: 36000, percentage: 72 },
        { id: 3, name: 'خدمة الضيافة', count: 65, revenue: 32500, percentage: 65 },
        { id: 4, name: 'الديكور الخاص', count: 45, revenue: 22500, percentage: 45 }
      ],
      topCustomers: [
        {
          id: 1,
          name: 'أحمد محمد علي',
          avatar: 'https://ui-avatars.com/api/?name=أحمد+محمد&background=667eea&color=fff&size=32&rounded=true',
          bookings: 12,
          totalSpent: 85000
        },
        {
          id: 2,
          name: 'فاطمة أحمد',
          avatar: 'https://ui-avatars.com/api/?name=فاطمة+أحمد&background=10b981&color=fff&size=32&rounded=true',
          bookings: 8,
          totalSpent: 62000
        },
        {
          id: 3,
          name: 'محمد سالم',
          avatar: 'https://ui-avatars.com/api/?name=محمد+سالم&background=f59e0b&color=fff&size=32&rounded=true',
          bookings: 6,
          totalSpent: 45000
        }
      ]
    }
  },
  methods: {
    formatCurrency(amount) {
      return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: 'SAR'
      }).format(amount);
    },
    setQuickRange(range) {
      this.activeRange = range;
      const today = new Date();
      let from, to;
      
      switch(range) {
        case 'today':
          from = to = today;
          break;
        case 'week':
          from = new Date(today.setDate(today.getDate() - 7));
          to = new Date();
          break;
        case 'month':
          from = new Date(today.getFullYear(), today.getMonth(), 1);
          to = new Date();
          break;
        case 'year':
          from = new Date(today.getFullYear(), 0, 1);
          to = new Date();
          break;
      }
      
      this.dateRange.from = from.toISOString().split('T')[0];
      this.dateRange.to = to.toISOString().split('T')[0];
    },
    applyDateRange() {
      console.log('Applying date range:', this.dateRange);
    },
    exportReport() {
      this.showExportModal = true;
    },
    downloadReport() {
      console.log('Downloading report in format:', this.exportFormat);
      this.showExportModal = false;
    }
  },
  mounted() {
    this.setQuickRange('month');
  }
}
</script>

<style scoped>
.reports-page {
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

/* Date Range Selector */
.date-range-selector {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.date-inputs {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  align-items: flex-end;
}

.date-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.date-group label {
  font-size: 0.875rem;
  color: #666;
  font-weight: 500;
}

.date-input {
  padding: 0.75rem 1rem;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 1rem;
}

.date-input:focus {
  outline: none;
  border-color: #667eea;
}

.quick-ranges {
  display: flex;
  gap: 0.5rem;
}

.range-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #e1e5e9;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.range-btn:hover {
  background: #f3f4f6;
}

.range-btn.active {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

/* Summary Cards */
.summary-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.summary-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  gap: 1rem;
}

.card-icon {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
}

.card-icon.revenue {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.card-icon.bookings {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card-icon.customers {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.card-icon.occupancy {
  background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
}

.card-content {
  flex: 1;
}

.card-content h3 {
  margin: 0;
  font-size: 1.75rem;
  font-weight: bold;
  color: #333;
}

.card-content p {
  margin: 0.25rem 0;
  color: #666;
  font-size: 0.875rem;
}

.trend {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.875rem;
  font-weight: 500;
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
}

.trend.positive {
  color: #059669;
  background: #d1fae5;
}

.trend.negative {
  color: #dc2626;
  background: #fee2e2;
}

/* Charts Grid */
.charts-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.chart-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.chart-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e1e5e9;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chart-header h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: bold;
  color: #333;
}

.chart-select {
  padding: 0.5rem 1rem;
  border: 1px solid #e1e5e9;
  border-radius: 6px;
  font-size: 0.875rem;
}

.chart-body {
  padding: 2rem;
}

.chart-placeholder {
  text-align: center;
  color: #9ca3af;
}

.chart-placeholder i {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.3;
}

.mock-chart {
  display: flex;
  gap: 1rem;
  justify-content: center;
  align-items: flex-end;
  height: 200px;
  margin-top: 2rem;
}

.bar {
  width: 40px;
  background: linear-gradient(to top, #667eea, #764ba2);
  border-radius: 8px 8px 0 0;
  transition: height 0.3s ease;
}

/* Pie Chart */
.pie-chart-placeholder {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.pie-chart {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  position: relative;
  overflow: hidden;
  background: conic-gradient(
    #667eea 0deg 126deg,
    #10b981 126deg 226deg,
    #f59e0b 226deg 305deg,
    #ef4444 305deg
  );
}

.pie-legend {
  flex: 1;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
  font-size: 0.875rem;
  color: #666;
}

.color-box {
  width: 16px;
  height: 16px;
  border-radius: 4px;
}

/* Tables Section */
.tables-section {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 1.5rem;
}

.table-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.table-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e1e5e9;
}

.table-header h3 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: bold;
  color: #333;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  padding: 1rem;
  text-align: right;
  font-weight: 600;
  color: #666;
  background: #f8f9fa;
  font-size: 0.875rem;
}

.data-table td {
  padding: 1rem;
  border-bottom: 1px solid #f1f5f9;
}

.customer-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.customer-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
}

.progress-bar {
  width: 100%;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(to right, #667eea, #764ba2);
  transition: width 0.3s ease;
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

.modal {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
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
}

.export-options {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.export-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  padding: 1.5rem;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.export-option:hover {
  border-color: #667eea;
  background: #f8f9ff;
}

.export-option input[type="radio"] {
  display: none;
}

.export-option i {
  font-size: 2rem;
  color: #667eea;
}

.export-option:has(input:checked) {
  border-color: #667eea;
  background: #f8f9ff;
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

/* Responsive */
@media (max-width: 1024px) {
  .charts-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .date-inputs {
    flex-direction: column;
  }
  
  .quick-ranges {
    flex-wrap: wrap;
  }
  
  .summary-cards {
    grid-template-columns: 1fr 1fr;
  }
  
  .tables-section {
    grid-template-columns: 1fr;
  }
  
  .export-options {
    grid-template-columns: 1fr;
  }
}
</style> 