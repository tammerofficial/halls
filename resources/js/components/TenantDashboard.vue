<template>
  <div class="tenant-dashboard">
    <!-- Header -->
    <header class="dashboard-header">
      <div class="header-content">
        <div class="tenant-info">
          <h1 class="tenant-name">{{ tenant.name }}</h1>
          <p class="tenant-domain">{{ tenant.domain }}</p>
          <span class="package-badge">{{ tenant.package }}</span>
        </div>
        <div class="user-info">
          <div class="user-avatar">
            {{ userInitials }}
          </div>
          <div class="user-details">
            <div class="user-name">{{ user.name }}</div>
            <div class="user-role">{{ user.role }}</div>
          </div>
          <button @click="logout" class="logout-btn" title="تسجيل الخروج">
            <i class="fas fa-sign-out-alt"></i>
          </button>
        </div>
      </div>
    </header>

    <!-- Navigation -->
    <nav class="dashboard-nav">
      <ul class="nav-list">
        <li class="nav-item">
          <router-link to="/dashboard" class="nav-link" active-class="active">
            <i class="fas fa-tachometer-alt"></i>
            لوحة التحكم
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/halls" class="nav-link" active-class="active">
            <i class="fas fa-building"></i>
            القاعات
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/bookings" class="nav-link" active-class="active">
            <i class="fas fa-calendar-check"></i>
            الحجوزات
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/customers" class="nav-link" active-class="active">
            <i class="fas fa-users"></i>
            العملاء
          </router-link>
        </li>
        <li class="nav-item" v-if="tenant.features.has_invoices">
          <router-link to="/invoices" class="nav-link" active-class="active">
            <i class="fas fa-file-invoice-dollar"></i>
            الفواتير
          </router-link>
        </li>
        <li class="nav-item" v-if="tenant.features.has_reports">
          <router-link to="/reports" class="nav-link" active-class="active">
            <i class="fas fa-chart-line"></i>
            التقارير
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/services" class="nav-link" active-class="active">
            <i class="fas fa-concierge-bell"></i>
            الخدمات
          </router-link>
        </li>
      </ul>
    </nav>

    <!-- Main Content -->
    <main class="dashboard-content">
      <router-view />
    </main>

    <!-- Trial Notice -->
    <div v-if="tenant.trial_ends_at && isOnTrial" class="trial-notice">
      <i class="fas fa-clock"></i>
      <span>تجربة مجانية تنتهي في {{ formatDate(tenant.trial_ends_at) }}</span>
      <button @click="upgradePlan" class="upgrade-btn">ترقية الخطة</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'TenantDashboard',
  data() {
    return {
      user: {
        name: '',
        email: '',
        role: ''
      },
      tenant: {
        name: '',
        domain: '',
        package: '',
        features: {},
        trial_ends_at: null
      },
      loading: true
    }
  },
  computed: {
    userInitials() {
      return this.user.name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
    },
    isOnTrial() {
      if (!this.tenant.trial_ends_at) return false;
      return new Date(this.tenant.trial_ends_at) > new Date();
    }
  },
  mounted() {
    this.loadUserData();
  },
  methods: {
    async loadUserData() {
      try {
        const response = await axios.get('/api/tenant/auth/user');
        if (response.data.success) {
          this.user = response.data.data.user;
          this.tenant = response.data.data.tenant;
        }
      } catch (error) {
        console.error('Error loading user data:', error);
        this.$router.push('/auth/login');
      } finally {
        this.loading = false;
      }
    },
    async logout() {
      try {
        await axios.post('/api/tenant/auth/logout');
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        this.$router.push('/auth/login');
      } catch (error) {
        console.error('Error logging out:', error);
      }
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('ar-SA');
    },
    upgradePlan() {
      // توجيه لصفحة ترقية الخطة
      this.$router.push('/upgrade');
    }
  }
}
</script>

<style scoped>
.tenant-dashboard {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  flex-direction: column;
}

.dashboard-header {
  background: white;
  padding: 1rem 2rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.tenant-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.tenant-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2d3748;
  margin: 0;
}

.tenant-domain {
  color: #718096;
  margin: 0;
  font-size: 0.9rem;
}

.package-badge {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 15px;
  font-size: 0.8rem;
  font-weight: 600;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-avatar {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 0.9rem;
}

.user-details {
  text-align: right;
}

.user-name {
  font-weight: 600;
  color: #2d3748;
  font-size: 0.9rem;
}

.user-role {
  color: #718096;
  font-size: 0.8rem;
}

.logout-btn {
  background: none;
  border: none;
  color: #718096;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  background: #f7fafc;
  color: #4a5568;
}

.dashboard-nav {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  padding: 1rem 2rem;
}

.nav-list {
  display: flex;
  gap: 2rem;
  list-style: none;
  margin: 0;
  padding: 0;
  overflow-x: auto;
}

.nav-item {
  flex-shrink: 0;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: white;
  text-decoration: none;
  padding: 0.75rem 1rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.1);
}

.nav-link.active {
  background: rgba(255, 255, 255, 0.2);
  font-weight: 600;
}

.dashboard-content {
  flex: 1;
  padding: 2rem;
  overflow-y: auto;
}

.trial-notice {
  position: fixed;
  bottom: 2rem;
  right: 2rem;
  background: #f59e0b;
  color: white;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  z-index: 1000;
}

.upgrade-btn {
  background: white;
  color: #f59e0b;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.upgrade-btn:hover {
  background: #f7fafc;
  transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .nav-list {
    gap: 1rem;
  }
  
  .nav-link {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
  }
  
  .dashboard-content {
    padding: 1rem;
  }
  
  .trial-notice {
    bottom: 1rem;
    right: 1rem;
    left: 1rem;
    flex-direction: column;
    text-align: center;
  }
}
</style> 