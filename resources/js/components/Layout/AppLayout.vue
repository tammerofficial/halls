<template>
  <div class="app-layout">
    <!-- Sidebar -->
    <div class="sidebar" :class="{ 'show': sidebarOpen }">
      <div class="sidebar-header">
        <h3 class="logo">نظام القاعات</h3>
        <button @click="toggleSidebar" class="close-btn">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <nav class="sidebar-nav">
        <ul class="nav-list">
          <li class="nav-item">
            <router-link to="/dashboard" class="nav-link" active-class="active">
              <i class="fas fa-tachometer-alt"></i>
              لوحة التحكم
            </router-link>
          </li>
          
          <div class="nav-separator"></div>
          
          <li class="nav-item">
            <router-link to="/halls" class="nav-link" active-class="active">
              <i class="fas fa-building"></i>
              القاعات
            </router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/bookings" class="nav-link" active-class="active">
              <i class="fas fa-calendar-alt"></i>
              الحجوزات
            </router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/customers" class="nav-link" active-class="active">
              <i class="fas fa-users"></i>
              العملاء
            </router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/services" class="nav-link" active-class="active">
              <i class="fas fa-concierge-bell"></i>
              الخدمات
            </router-link>
          </li>
          
          <div class="nav-separator"></div>
          
          <li class="nav-item">
            <router-link to="/invoices" class="nav-link" active-class="active">
              <i class="fas fa-file-invoice-dollar"></i>
              الفواتير
            </router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/reports" class="nav-link" active-class="active">
              <i class="fas fa-chart-line"></i>
              التقارير
            </router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/packages" class="nav-link" active-class="active">
              <i class="fas fa-box"></i>
              الباقات
            </router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/subscriptions" class="nav-link" active-class="active">
              <i class="fas fa-users"></i>
              الاشتراكات
            </router-link>
          </li>
          
          <li class="nav-item">
            <router-link to="/memberships" class="nav-link" active-class="active">
              <i class="fas fa-id-card"></i>
              العضويات
            </router-link>
          </li>
        </ul>
      </nav>
      
      <div class="user-info">
        <div class="user-avatar">
          {{ userInitials }}
        </div>
        <div class="user-details">
          <div class="user-name">{{ userName }}</div>
          <div class="user-role">مدير النظام</div>
        </div>
        <button @click="logout" class="logout-btn" title="تسجيل الخروج">
          <i class="fas fa-sign-out-alt"></i>
        </button>
      </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-wrapper">
      <!-- Header -->
      <header class="header">
        <button @click="toggleSidebar" class="mobile-menu-btn">
          <i class="fas fa-bars"></i>
        </button>
        <h1 class="page-title">{{ pageTitle }}</h1>
        <div class="header-actions">
          <button class="notification-btn">
            <i class="fas fa-bell"></i>
            <span class="badge">3</span>
          </button>
        </div>
      </header>
      
      <!-- Content -->
      <main class="main-content">
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AppLayout',
  data() {
    return {
      sidebarOpen: false,
      userName: 'أحمد محمد',
      userInitials: 'أم'
    }
  },
  computed: {
    pageTitle() {
      const routeNames = {
        dashboard: 'لوحة التحكم',
        halls: 'إدارة القاعات',
        bookings: 'إدارة الحجوزات',
        customers: 'إدارة العملاء',
        services: 'إدارة الخدمات',
        invoices: 'إدارة الفواتير',
        reports: 'التقارير',
        packages: 'إدارة الباقات',
        subscriptions: 'إدارة الاشتراكات',
        memberships: 'إدارة العضويات'
      };
      return routeNames[this.$route.name] || 'لوحة التحكم';
    }
  },
  methods: {
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen;
    },
    logout() {
      localStorage.removeItem('auth_token');
      this.$router.push('/login');
    }
  }
}
</script>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
  direction: rtl;
}

.sidebar {
  width: 280px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 1.5rem;
  position: fixed;
  height: 100vh;
  overflow-y: auto;
  z-index: 1000;
  transition: transform 0.3s ease;
  right: 0;
  top: 0;
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo {
  margin: 0;
  font-size: 1.5rem;
  font-weight: bold;
}

.close-btn {
  background: none;
  border: none;
  color: white;
  font-size: 1.2rem;
  cursor: pointer;
}

.nav-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-item {
  margin-bottom: 0.5rem;
}

.nav-link {
  display: flex;
  align-items: center;
  padding: 0.85rem 1rem;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
}

.nav-link:hover,
.nav-link.active {
  background: rgba(255, 255, 255, 0.15);
  color: white;
  transform: translateX(5px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.nav-link i {
  margin-left: 0.75rem;
  width: 20px;
  text-align: center;
}

.nav-separator {
  height: 1px;
  background: rgba(255, 255, 255, 0.1);
  margin: 1rem 0;
}

.user-info {
  margin-top: auto;
  padding: 1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  border-radius: 8px;
  transition: background 0.3s ease;
}

.user-info:hover {
  background: rgba(0, 0, 0, 0.1);
}

.user-avatar {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  margin-right: 0.75rem;
}

.user-details {
  margin-right: 0.75rem;
  flex: 1;
}

.user-name {
  font-weight: 600;
}

.user-role {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.7);
}

.logout-btn {
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.8);
  font-size: 1.1rem;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  color: white;
  background: rgba(255, 255, 255, 0.2);
}

.main-wrapper {
  flex: 1;
  margin-right: 280px;
  background: #f8f9fa;
  min-height: 100vh;
}

.header {
  background: white;
  padding: 1rem 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.mobile-menu-btn {
  display: none;
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
}

.page-title {
  margin: 0;
  font-size: 1.5rem;
  font-weight: bold;
  color: #333;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.notification-btn {
  position: relative;
  background: none;
  border: none;
  font-size: 1.2rem;
  color: #666;
  cursor: pointer;
}

.badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #dc3545;
  color: white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  font-size: 0.75rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.main-content {
  padding: 2rem;
}

@media (max-width: 768px) {
  .sidebar {
    transform: translateX(100%);
  }
  
  .sidebar.show {
    transform: translateX(0);
  }
  
  .main-wrapper {
    margin-right: 0;
  }
  
  .mobile-menu-btn {
    display: block;
  }
  
  .close-btn {
    display: block;
  }
}

@media (min-width: 769px) {
  .close-btn {
    display: none;
  }
}
</style> 