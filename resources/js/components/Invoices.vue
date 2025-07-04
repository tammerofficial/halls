<template>
  <AppLayout>
    <div class="invoices-page">
      <!-- Header -->
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">
            <i class="fas fa-file-invoice-dollar"></i>
            إدارة الفواتير
          </h1>
          <p class="page-subtitle">عرض وإدارة جميع الفواتير والمدفوعات</p>
        </div>
        <div class="header-actions">
          <button @click="showCreateModal = true" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            إنشاء فاتورة جديدة
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon bg-primary">
            <i class="fas fa-file-invoice"></i>
          </div>
          <div class="stat-info">
            <h3>{{ stats.total }}</h3>
            <p>إجمالي الفواتير</p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon bg-success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-info">
            <h3>{{ stats.paid }}</h3>
            <p>المدفوعة</p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon bg-warning">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-info">
            <h3>{{ stats.pending }}</h3>
            <p>قيد الانتظار</p>
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon bg-danger">
            <i class="fas fa-exclamation-circle"></i>
          </div>
          <div class="stat-info">
            <h3>{{ stats.overdue }}</h3>
            <p>متأخرة</p>
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
            placeholder="البحث في الفواتير..."
            class="search-input"
          >
        </div>
        
        <div class="filters">
          <select v-model="statusFilter" class="filter-select">
            <option value="">جميع الحالات</option>
            <option value="paid">مدفوعة</option>
            <option value="pending">قيد الانتظار</option>
            <option value="overdue">متأخرة</option>
            <option value="cancelled">ملغاة</option>
          </select>
          
          <input 
            v-model="dateFilter" 
            type="date" 
            class="filter-date"
            placeholder="تاريخ الفاتورة"
          >
          
          <button @click="clearFilters" class="btn btn-outline">
            <i class="fas fa-times"></i>
            مسح الفلاتر
          </button>
        </div>
      </div>

      <!-- Invoices Table -->
      <div class="invoices-table-container">
        <table class="invoices-table">
          <thead>
            <tr>
              <th>رقم الفاتورة</th>
              <th>العميل</th>
              <th>التاريخ</th>
              <th>تاريخ الاستحقاق</th>
              <th>المبلغ</th>
              <th>الحالة</th>
              <th>الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="invoice in filteredInvoices" :key="invoice.id">
              <td class="invoice-number">
                <span class="invoice-badge">#{{ invoice.number }}</span>
              </td>
              <td>
                <div class="customer-cell">
                  <img :src="invoice.customer.avatar" class="customer-avatar">
                  <div>
                    <div class="customer-name">{{ invoice.customer.name }}</div>
                    <div class="customer-email">{{ invoice.customer.email }}</div>
                  </div>
                </div>
              </td>
              <td>{{ formatDate(invoice.date) }}</td>
              <td>{{ formatDate(invoice.dueDate) }}</td>
              <td class="amount-cell">
                <span class="amount">{{ formatCurrency(invoice.amount) }}</span>
              </td>
              <td>
                <span :class="getStatusClass(invoice.status)">
                  {{ getStatusText(invoice.status) }}
                </span>
              </td>
              <td>
                <div class="actions">
                  <button @click="viewInvoice(invoice)" class="action-btn" title="عرض">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button @click="printInvoice(invoice)" class="action-btn" title="طباعة">
                    <i class="fas fa-print"></i>
                  </button>
                  <button @click="sendInvoice(invoice)" class="action-btn" title="إرسال">
                    <i class="fas fa-envelope"></i>
                  </button>
                  <button 
                    v-if="invoice.status === 'pending'" 
                    @click="markAsPaid(invoice)" 
                    class="action-btn success" 
                    title="تحديد كمدفوعة"
                  >
                    <i class="fas fa-check"></i>
                  </button>
                  <button @click="deleteInvoice(invoice)" class="action-btn danger" title="حذف">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        
        <div v-if="filteredInvoices.length === 0" class="empty-state">
          <i class="fas fa-file-invoice"></i>
          <p>لا توجد فواتير</p>
        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination">
        <button class="page-btn" :disabled="currentPage === 1" @click="currentPage--">
          <i class="fas fa-chevron-right"></i>
        </button>
        <span class="page-info">صفحة {{ currentPage }} من {{ totalPages }}</span>
        <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage++">
          <i class="fas fa-chevron-left"></i>
        </button>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from './Layout/AppLayout.vue';

export default {
  name: 'Invoices',
  components: {
    AppLayout
  },
  data() {
    return {
      invoices: [
        {
          id: 1,
          number: 'INV-2025-001',
          customer: {
            name: 'أحمد محمد علي',
            email: 'ahmed@example.com',
            avatar: 'https://ui-avatars.com/api/?name=أحمد+محمد&background=667eea&color=fff&size=40&rounded=true'
          },
          date: '2025-01-10',
          dueDate: '2025-01-25',
          amount: 15000,
          status: 'paid',
          items: [
            { description: 'حجز قاعة الماسة', amount: 12000 },
            { description: 'خدمات إضافية', amount: 3000 }
          ]
        },
        {
          id: 2,
          number: 'INV-2025-002',
          customer: {
            name: 'فاطمة أحمد',
            email: 'fatima@example.com',
            avatar: 'https://ui-avatars.com/api/?name=فاطمة+أحمد&background=10b981&color=fff&size=40&rounded=true'
          },
          date: '2025-01-12',
          dueDate: '2025-01-27',
          amount: 8500,
          status: 'pending',
          items: [
            { description: 'حجز قاعة النخبة', amount: 8500 }
          ]
        },
        {
          id: 3,
          number: 'INV-2025-003',
          customer: {
            name: 'محمد سالم',
            email: 'mohammed@example.com',
            avatar: 'https://ui-avatars.com/api/?name=محمد+سالم&background=f59e0b&color=fff&size=40&rounded=true'
          },
          date: '2024-12-15',
          dueDate: '2024-12-30',
          amount: 20000,
          status: 'overdue',
          items: [
            { description: 'حجز قاعتين', amount: 18000 },
            { description: 'تجهيزات خاصة', amount: 2000 }
          ]
        }
      ],
      searchQuery: '',
      statusFilter: '',
      dateFilter: '',
      currentPage: 1,
      itemsPerPage: 10,
      showCreateModal: false,
      stats: {
        total: 45,
        paid: 32,
        pending: 8,
        overdue: 5
      }
    }
  },
  computed: {
    filteredInvoices() {
      return this.invoices.filter(invoice => {
        const matchesSearch = invoice.number.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            invoice.customer.name.toLowerCase().includes(this.searchQuery.toLowerCase());
        const matchesStatus = !this.statusFilter || invoice.status === this.statusFilter;
        const matchesDate = !this.dateFilter || invoice.date === this.dateFilter;
        
        return matchesSearch && matchesStatus && matchesDate;
      });
    },
    totalPages() {
      return Math.ceil(this.filteredInvoices.length / this.itemsPerPage);
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
        paid: 'status-paid',
        pending: 'status-pending',
        overdue: 'status-overdue',
        cancelled: 'status-cancelled'
      };
      return `status-badge ${classes[status]}`;
    },
    getStatusText(status) {
      const texts = {
        paid: 'مدفوعة',
        pending: 'قيد الانتظار',
        overdue: 'متأخرة',
        cancelled: 'ملغاة'
      };
      return texts[status];
    },
    clearFilters() {
      this.searchQuery = '';
      this.statusFilter = '';
      this.dateFilter = '';
    },
    viewInvoice(invoice) {
      console.log('View invoice:', invoice);
    },
    printInvoice(invoice) {
      console.log('Print invoice:', invoice);
    },
    sendInvoice(invoice) {
      console.log('Send invoice:', invoice);
    },
    markAsPaid(invoice) {
      invoice.status = 'paid';
      this.stats.paid++;
      this.stats.pending--;
    },
    deleteInvoice(invoice) {
      if (confirm('هل أنت متأكد من حذف هذه الفاتورة؟')) {
        this.invoices = this.invoices.filter(i => i.id !== invoice.id);
      }
    }
  }
}
</script> 

<style scoped>
.invoices-page {
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

.filter-select,
.filter-date {
  padding: 0.75rem 1rem;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 0.95rem;
  background: white;
  transition: all 0.3s ease;
}

.filter-select:focus,
.filter-date:focus {
  outline: none;
  border-color: #667eea;
}

/* Table */
.invoices-table-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.invoices-table {
  width: 100%;
  border-collapse: collapse;
}

.invoices-table thead {
  background: #f8f9fa;
}

.invoices-table th {
  padding: 1rem;
  text-align: right;
  font-weight: 600;
  color: #333;
  border-bottom: 2px solid #e1e5e9;
}

.invoices-table td {
  padding: 1rem;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.invoice-number {
  font-weight: 600;
}

.invoice-badge {
  background: #e0e7ff;
  color: #4f46e5;
  padding: 0.25rem 0.75rem;
  border-radius: 6px;
  font-size: 0.875rem;
}

.customer-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.customer-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
}

.customer-name {
  font-weight: 600;
  color: #333;
}

.customer-email {
  font-size: 0.875rem;
  color: #666;
}

.amount-cell {
  font-weight: bold;
}

.amount {
  color: #059669;
  font-size: 1.1rem;
}

/* Status Badges */
.status-badge {
  padding: 0.375rem 0.75rem;
  border-radius: 20px;
  font-size: 0.875rem;
  font-weight: 500;
  display: inline-block;
}

.status-paid {
  background: #d1fae5;
  color: #065f46;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.status-overdue {
  background: #fee2e2;
  color: #991b1b;
}

.status-cancelled {
  background: #e5e7eb;
  color: #374151;
}

/* Actions */
.actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: #f3f4f6;
  color: #6b7280;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.action-btn:hover {
  background: #e5e7eb;
  color: #374151;
}

.action-btn.success {
  background: #d1fae5;
  color: #059669;
}

.action-btn.success:hover {
  background: #a7f3d0;
}

.action-btn.danger {
  background: #fee2e2;
  color: #dc2626;
}

.action-btn.danger:hover {
  background: #fecaca;
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

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
}

.page-btn {
  width: 36px;
  height: 36px;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.page-btn:hover:not(:disabled) {
  background: #f3f4f6;
  border-color: #d1d5db;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  color: #6b7280;
  font-size: 0.875rem;
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

.btn-outline {
  background: transparent;
  color: #6b7280;
  border: 1px solid #e5e7eb;
}

.btn-outline:hover {
  background: #f3f4f6;
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
  
  .filter-select,
  .filter-date,
  .btn-outline {
    width: 100%;
  }
  
  .invoices-table {
    font-size: 0.875rem;
  }
  
  .invoices-table th,
  .invoices-table td {
    padding: 0.75rem 0.5rem;
  }
  
  .customer-cell {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .actions {
    flex-wrap: wrap;
  }
}
</style> 