import { createRouter, createWebHistory } from 'vue-router';

// Import components
import LandingPage from '../components/LandingPage.vue';
import Dashboard from '../components/Dashboard.vue';
import Halls from '../components/Halls.vue';
import Bookings from '../components/Bookings.vue';
import Customers from '../components/Customers.vue';
import Services from '../components/Services.vue';
import Invoices from '../components/Invoices.vue';
import Reports from '../components/Reports.vue';
import Login from '../components/Auth/Login.vue';
import Register from '../components/Auth/Register.vue';
import RegisterTenant from '../components/RegisterTenant.vue';
import Packages from '../components/Packages.vue';
import Subscriptions from '../components/Subscriptions.vue';
import Memberships from '../components/Memberships.vue';

const routes = [
  {
    path: '/',
    name: 'home',
    redirect: '/login'
  },
  {
    path: '/login',
    name: 'landing',
    component: LandingPage
  },
  {
    path: '/auth/login',
    name: 'login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: { requiresGuest: true }
  },
  {
    path: '/register-tenant',
    name: 'register-tenant',
    component: RegisterTenant,
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/halls',
    name: 'halls',
    component: Halls,
    meta: { requiresAuth: true }
  },
  {
    path: '/bookings',
    name: 'bookings',
    component: Bookings,
    meta: { requiresAuth: true }
  },
  {
    path: '/customers',
    name: 'customers',
    component: Customers,
    meta: { requiresAuth: true }
  },
  {
    path: '/services',
    name: 'services',
    component: Services,
    meta: { requiresAuth: true }
  },
  {
    path: '/invoices',
    name: 'invoices',
    component: Invoices,
    meta: { requiresAuth: true }
  },
  {
    path: '/reports',
    name: 'reports',
    component: Reports,
    meta: { requiresAuth: true }
  },
  {
    path: '/packages',
    name: 'packages',
    component: Packages,
    meta: { requiresAuth: true }
  },
  {
    path: '/subscriptions',
    name: 'subscriptions',
    component: Subscriptions,
    meta: { requiresAuth: true }
  },
  {
    path: '/memberships',
    name: 'memberships',
    component: Memberships,
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Navigation guards
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('auth_token');
  
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/auth/login');
  } else if (to.meta.requiresGuest && isAuthenticated) {
    next('/dashboard');
  } else {
    next();
  }
});

export default router; 