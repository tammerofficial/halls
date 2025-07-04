<template>
  <AppLayout>
    <div class="bookings-page">
      <div class="page-header">
        <div class="header-content">
          <h1 class="page-title">📅 إدارة الحجوزات</h1>
          <p class="page-subtitle">إدارة جميع الحجوزات والمواعيد</p>
        </div>
        <div class="header-actions">
          <button @click="showCalendarView = !showCalendarView" class="btn btn-secondary">
            <i :class="showCalendarView ? 'fas fa-list' : 'fas fa-calendar'"></i>
            {{ showCalendarView ? 'عرض القائمة' : 'عرض التقويم' }}
          </button>
          <button @click="showCreateModal = true" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            حجز جديد
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon bg-blue">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="stat-content">
            <h3>{{ totalBookings }}</h3>
            <p>إجمالي الحجوزات</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon bg-green">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-content">
            <h3>{{ confirmedBookings }}</h3>
            <p>حجوزات مؤكدة</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon bg-orange">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-content">
            <h3>{{ pendingBookings }}</h3>
            <p>قيد المراجعة</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon bg-purple">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <div class="stat-content">
            <h3>{{ totalRevenue }} ر.س</h3>
            <p>إجمالي الإيرادات</p>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="filters-section">
        <div class="date-filter">
          <input type="date" v-model="filterDate" class="date-input">
        </div>
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="البحث في الحجوزات..."
            class="search-input"
          >
        </div>
        <div class="filter-buttons">
          <button 
            @click="currentFilter = 'all'" 
            :class="['filter-btn', { active: currentFilter === 'all' }]"
          >
            الكل
          </button>
          <button 
            @click="currentFilter = 'today'" 
            :class="['filter-btn', { active: currentFilter === 'today' }]"
          >
            اليوم
          </button>
          <button 
            @click="currentFilter = 'week'" 
            :class="['filter-btn', { active: currentFilter === 'week' }]"
          >
            هذا الأسبوع
          </button>
          <button 
            @click="currentFilter = 'month'" 
            :class="['filter-btn', { active: currentFilter === 'month' }]"
          >
            هذا الشهر
          </button>
        </div>
      </div>

      <!-- Calendar View -->
      <div v-if="showCalendarView" class="calendar-view">
        <div class="calendar-header">
          <button @click="previousMonth" class="calendar-nav">
            <i class="fas fa-chevron-right"></i>
          </button>
          <h3>{{ currentMonthYear }}</h3>
          <button @click="nextMonth" class="calendar-nav">
            <i class="fas fa-chevron-left"></i>
          </button>
        </div>
        <div class="calendar-grid">
          <div class="calendar-day-header" v-for="day in weekDays" :key="day">
            {{ day }}
          </div>
          <div 
            v-for="day in calendarDays" 
            :key="day.date"
            :class="['calendar-day', { 
              'other-month': !day.currentMonth,
              'today': day.isToday,
              'has-bookings': day.bookings.length > 0
            }]"
            @click="selectDate(day)"
          >
            <div class="day-number">{{ day.day }}</div>
            <div class="day-bookings">
              <div 
                v-for="booking in day.bookings.slice(0, 2)" 
                :key="booking.id"
                class="booking-item"
                :class="'status-' + booking.status"
              >
                {{ booking.hall.name }}
              </div>
              <div v-if="day.bookings.length > 2" class="more-bookings">
                +{{ day.bookings.length - 2 }} أخرى
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Table View -->
      <div v-else class="bookings-content">
        <div class="bookings-table">
          <table class="table">
            <thead>
              <tr>
                <th>رقم الحجز</th>
                <th>العميل</th>
                <th>القاعة</th>
                <th>التاريخ</th>
                <th>الوقت</th>
                <th>الخدمات</th>
                <th>المبلغ</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="booking in filteredBookings" :key="booking.id">
                <td class="booking-id">#{{ booking.id }}</td>
                <td>
                  <div class="customer-info">
                    <img :src="booking.customer.avatar" class="customer-avatar">
                    <div>
                      <div class="customer-name">{{ booking.customer.name }}</div>
                      <div class="customer-phone">{{ booking.customer.phone }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="hall-info">
                    <div class="hall-name">{{ booking.hall.name }}</div>
                    <div class="hall-type">{{ booking.hall.type }}</div>
                  </div>
                </td>
                <td>{{ formatDate(booking.date) }}</td>
                <td>
                  <div class="time-info">
                    <div>{{ booking.start_time }} - {{ booking.end_time }}</div>
                    <div class="duration">{{ booking.duration }} ساعة</div>
                  </div>
                </td>
                <td>
                  <div class="services-list">
                    <span v-for="service in booking.services" :key="service" class="service-tag">
                      {{ service }}
                    </span>
                  </div>
                </td>
                <td class="amount">{{ booking.amount }} ر.س</td>
                <td>
                  <span :class="getStatusClass(booking.status)">
                    {{ getStatusText(booking.status) }}
                  </span>
                </td>
                <td>
                  <div class="actions">
                    <button @click="viewBooking(booking)" class="btn-icon" title="عرض">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button @click="editBooking(booking)" class="btn-icon" title="تعديل">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button @click="confirmBooking(booking)" v-if="booking.status === 'pending'" class="btn-icon text-green" title="تأكيد">
                      <i class="fas fa-check"></i>
                    </button>
                    <button @click="cancelBooking(booking)" v-if="booking.status !== 'cancelled'" class="btn-icon text-orange" title="إلغاء">
                      <i class="fas fa-times"></i>
                    </button>
                    <button @click="deleteBooking(booking)" class="btn-icon text-red" title="حذف">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from './Layout/AppLayout.vue';

export default {
  name: 'Bookings',
  components: {
    AppLayout
  },
  data() {
    return {
      bookings: [
        {
          id: 1001,
          customer: {
            name: 'أحمد محمد علي',
            phone: '0501234567',
            avatar: 'https://ui-avatars.com/api/?name=أحمد+محمد&background=667eea&color=fff&size=32&rounded=true&bold=true'
          },
          hall: { 
            name: 'قاعة الماسة الكبرى',
            type: 'قاعة أفراح'
          },
          date: '2025-01-15',
          start_time: '14:00',
          end_time: '18:00',
          duration: 4,
          services: ['تصوير', 'ضيافة', 'تزيين'],
          amount: '8,500',
          status: 'confirmed'
        },
        {
          id: 1002,
          customer: {
            name: 'فاطمة أحمد',
            phone: '0509876543',
            avatar: 'https://ui-avatars.com/api/?name=فاطمة+أحمد&background=10b981&color=fff&size=32&rounded=true&bold=true'
          },
          hall: { 
            name: 'قاعة النخبة',
            type: 'قاعة اجتماعات'
          },
          date: '2025-01-18',
          start_time: '19:00',
          end_time: '23:00',
          duration: 4,
          services: ['ضيافة', 'شاشات عرض'],
          amount: '6,200',
          status: 'pending'
        },
        {
          id: 1003,
          customer: {
            name: 'محمد السعيد',
            phone: '0555555555',
            avatar: 'https://ui-avatars.com/api/?name=محمد+السعيد&background=f59e0b&color=fff&size=32&rounded=true&bold=true'
          },
          hall: { 
            name: 'قاعة الياقوت',
            type: 'قاعة مؤتمرات'
          },
          date: '2025-01-20',
          start_time: '09:00',
          end_time: '17:00',
          duration: 8,
          services: ['ضيافة', 'تصوير', 'ترجمة فورية'],
          amount: '12,000',
          status: 'confirmed'
        },
        {
          id: 1004,
          customer: {
            name: 'نورا عبدالله',
            phone: '0544444444',
            avatar: 'https://ui-avatars.com/api/?name=نورا+عبدالله&background=8b5cf6&color=fff&size=32&rounded=true&bold=true'
          },
          hall: { 
            name: 'قاعة الزمرد',
            type: 'قاعة أفراح'
          },
          date: '2025-01-22',
          start_time: '20:00',
          end_time: '00:00',
          duration: 4,
          services: ['تصوير', 'DJ', 'تزيين فاخر'],
          amount: '15,000',
          status: 'cancelled'
        }
      ],
      showCreateModal: false,
      showCalendarView: false,
      searchQuery: '',
      filterDate: '',
      currentFilter: 'all',
      currentMonth: new Date().getMonth(),
      currentYear: new Date().getFullYear(),
      weekDays: ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت']
    }
  },
  computed: {
    totalBookings() {
      return this.bookings.length;
    },
    confirmedBookings() {
      return this.bookings.filter(b => b.status === 'confirmed').length;
    },
    pendingBookings() {
      return this.bookings.filter(b => b.status === 'pending').length;
    },
    totalRevenue() {
      return this.bookings
        .filter(b => b.status === 'confirmed')
        .reduce((total, booking) => {
          return total + parseFloat(booking.amount.replace(',', ''));
        }, 0)
        .toLocaleString();
    },
    filteredBookings() {
      let filtered = this.bookings;
      
      // Search filter
      if (this.searchQuery) {
        filtered = filtered.filter(booking => 
          booking.customer.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          booking.hall.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          booking.customer.phone.includes(this.searchQuery)
        );
      }
      
      // Date filter
      if (this.filterDate) {
        filtered = filtered.filter(booking => booking.date === this.filterDate);
      }
      
      // Period filter
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      
      switch (this.currentFilter) {
        case 'today':
          filtered = filtered.filter(booking => {
            const bookingDate = new Date(booking.date);
            return bookingDate.toDateString() === today.toDateString();
          });
          break;
        case 'week':
          const weekStart = new Date(today);
          weekStart.setDate(today.getDate() - today.getDay());
          const weekEnd = new Date(weekStart);
          weekEnd.setDate(weekStart.getDate() + 6);
          
          filtered = filtered.filter(booking => {
            const bookingDate = new Date(booking.date);
            return bookingDate >= weekStart && bookingDate <= weekEnd;
          });
          break;
        case 'month':
          filtered = filtered.filter(booking => {
            const bookingDate = new Date(booking.date);
            return bookingDate.getMonth() === today.getMonth() && 
                   bookingDate.getFullYear() === today.getFullYear();
          });
          break;
      }
      
      return filtered;
    },
    currentMonthYear() {
      const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 
                     'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
      return `${months[this.currentMonth]} ${this.currentYear}`;
    },
    calendarDays() {
      const days = [];
      const firstDay = new Date(this.currentYear, this.currentMonth, 1);
      const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
      const prevLastDay = new Date(this.currentYear, this.currentMonth, 0);
      
      const firstDayOfWeek = firstDay.getDay();
      const daysInMonth = lastDay.getDate();
      const daysInPrevMonth = prevLastDay.getDate();
      
      // Previous month days
      for (let i = firstDayOfWeek - 1; i >= 0; i--) {
        const date = new Date(this.currentYear, this.currentMonth - 1, daysInPrevMonth - i);
        days.push({
          day: daysInPrevMonth - i,
          date: date.toISOString().split('T')[0],
          currentMonth: false,
          isToday: false,
          bookings: this.getBookingsForDate(date)
        });
      }
      
      // Current month days
      for (let i = 1; i <= daysInMonth; i++) {
        const date = new Date(this.currentYear, this.currentMonth, i);
        const today = new Date();
        days.push({
          day: i,
          date: date.toISOString().split('T')[0],
          currentMonth: true,
          isToday: date.toDateString() === today.toDateString(),
          bookings: this.getBookingsForDate(date)
        });
      }
      
      // Next month days
      const remainingDays = 42 - days.length;
      for (let i = 1; i <= remainingDays; i++) {
        const date = new Date(this.currentYear, this.currentMonth + 1, i);
        days.push({
          day: i,
          date: date.toISOString().split('T')[0],
          currentMonth: false,
          isToday: false,
          bookings: this.getBookingsForDate(date)
        });
      }
      
      return days;
    }
  },
  methods: {
    formatDate(date) {
      return new Date(date).toLocaleDateString('ar-SA');
    },
    getStatusClass(status) {
      const classes = {
        confirmed: 'badge bg-success',
        pending: 'badge bg-warning',
        cancelled: 'badge bg-danger'
      };
      return classes[status] || 'badge bg-secondary';
    },
    getStatusText(status) {
      const texts = {
        confirmed: 'مؤكد',
        pending: 'قيد المراجعة',
        cancelled: 'ملغي'
      };
      return texts[status] || 'غير محدد';
    },
    getBookingsForDate(date) {
      const dateStr = date.toISOString().split('T')[0];
      return this.bookings.filter(booking => booking.date === dateStr);
    },
    previousMonth() {
      if (this.currentMonth === 0) {
        this.currentMonth = 11;
        this.currentYear--;
      } else {
        this.currentMonth--;
      }
    },
    nextMonth() {
      if (this.currentMonth === 11) {
        this.currentMonth = 0;
        this.currentYear++;
      } else {
        this.currentMonth++;
      }
    },
    selectDate(day) {
      if (day.bookings.length > 0) {
        this.filterDate = day.date;
        this.showCalendarView = false;
      }
    },
    viewBooking(booking) {
      console.log('View booking:', booking);
    },
    editBooking(booking) {
      console.log('Edit booking:', booking);
    },
    confirmBooking(booking) {
      booking.status = 'confirmed';
    },
    cancelBooking(booking) {
      if (confirm('هل أنت متأكد من إلغاء هذا الحجز؟')) {
        booking.status = 'cancelled';
      }
    },
    deleteBooking(booking) {
      if (confirm('هل أنت متأكد من حذف هذا الحجز؟')) {
        this.bookings = this.bookings.filter(b => b.id !== booking.id);
      }
    }
  }
}
</script>

<style scoped>
.bookings-page {
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

.header-actions {
  display: flex;
  gap: 0.75rem;
}

.page-title {
  margin: 0;
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
}

.page-subtitle {
  margin: 0.5rem 0 0 0;
  color: #6b7280;
}

/* Stats Grid */
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
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
}

.bg-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.bg-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bg-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.bg-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }

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

/* Filters */
.filters-section {
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.date-filter {
  display: flex;
  align-items: center;
}

.date-input {
  padding: 0.5rem 1rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1rem;
}

.search-box {
  position: relative;
  flex: 1;
  max-width: 300px;
}

.search-box i {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #6b7280;
}

.search-input {
  width: 100%;
  padding: 0.5rem 1rem;
  padding-right: 2.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1rem;
}

.filter-buttons {
  display: flex;
  gap: 0.5rem;
}

.filter-btn {
  padding: 0.5rem 1rem;
  border: 1px solid #e5e7eb;
  background: white;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-btn:hover {
  background: #f9fafb;
}

.filter-btn.active {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

/* Calendar View */
.calendar-view {
  background: white;
  border-radius: 0.75rem;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

.calendar-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
}

.calendar-nav {
  background: none;
  border: none;
  font-size: 1.25rem;
  color: #6b7280;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 0.5rem;
  transition: all 0.3s ease;
}

.calendar-nav:hover {
  background: #f3f4f6;
  color: #374151;
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 1px;
  background: #e5e7eb;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  overflow: hidden;
}

.calendar-day-header {
  background: #f9fafb;
  padding: 0.75rem;
  text-align: center;
  font-weight: 600;
  color: #374151;
  font-size: 0.875rem;
}

.calendar-day {
  background: white;
  min-height: 100px;
  padding: 0.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.calendar-day:hover {
  background: #f9fafb;
}

.calendar-day.other-month {
  background: #fafafa;
  color: #9ca3af;
}

.calendar-day.today {
  background: #eff6ff;
}

.calendar-day.has-bookings {
  border-top: 3px solid #667eea;
}

.day-number {
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.day-bookings {
  font-size: 0.75rem;
}

.booking-item {
  padding: 0.125rem 0.25rem;
  margin-bottom: 0.125rem;
  border-radius: 0.25rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.booking-item.status-confirmed {
  background: #d1fae5;
  color: #065f46;
}

.booking-item.status-pending {
  background: #fed7aa;
  color: #92400e;
}

.booking-item.status-cancelled {
  background: #fee2e2;
  color: #991b1b;
}

.more-bookings {
  color: #6b7280;
  font-size: 0.75rem;
  font-style: italic;
}

/* Table View */
.bookings-content {
  background: white;
  border-radius: 0.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.bookings-table {
  overflow-x: auto;
}

.table {
  width: 100%;
  margin: 0;
  border-collapse: collapse;
}

.table th {
  background: #f9fafb;
  padding: 1rem;
  text-align: right;
  font-weight: 600;
  color: #374151;
  border-bottom: 1px solid #e5e7eb;
  white-space: nowrap;
}

.table td {
  padding: 1rem;
  border-bottom: 1px solid #f3f4f6;
  vertical-align: middle;
}

.table tbody tr:hover {
  background: #f9fafb;
}

.booking-id {
  font-weight: 600;
  color: #6b7280;
}

.customer-info {
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
  color: #1f2937;
}

.customer-phone {
  font-size: 0.875rem;
  color: #6b7280;
}

.hall-info {
  line-height: 1.4;
}

.hall-name {
  font-weight: 600;
  color: #1f2937;
}

.hall-type {
  font-size: 0.875rem;
  color: #6b7280;
}

.time-info {
  line-height: 1.4;
}

.duration {
  font-size: 0.875rem;
  color: #6b7280;
}

.services-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.25rem;
}

.service-tag {
  padding: 0.125rem 0.5rem;
  background: #e0e7ff;
  color: #4338ca;
  border-radius: 9999px;
  font-size: 0.75rem;
  font-weight: 500;
}

.amount {
  font-weight: bold;
  color: #059669;
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

.btn-icon.text-green:hover {
  background: #d1fae5;
  color: #065f46;
}

.btn-icon.text-orange:hover {
  background: #fed7aa;
  color: #92400e;
}

.btn-icon.text-red:hover {
  background: #fee2e2;
  color: #dc2626;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
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

.badge {
  padding: 0.25rem 0.75rem;
  border-radius: 9999px;
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

.bg-danger {
  background: #ef4444 !important;
  color: white;
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .bookings-table {
    font-size: 0.875rem;
  }
  
  .table th,
  .table td {
    padding: 0.5rem;
  }
}
</style> 