import Dashboard from 'pages/Dashboard.vue';
import Leads from 'pages/Leads.vue';
import Login from 'pages/login.vue';
import Register from 'pages/Register.vue';
import { createRouter, createWebHistory } from 'vue-router';

const routes = [
  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
  { path: '/leads', name: 'leads', component: Leads }, // No authentication required for Leads
  { path: '/dashboard', name: 'dashboard', component: Dashboard, meta: { requiresAuth: true } }, // Authentication required for Dashboard
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Global navigation guard
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('auth_token'); // Check if the user is authenticated

  // If the route requires authentication and the user is not authenticated, redirect to login
  if (to.matched.some(record => record.meta.requiresAuth) && !isAuthenticated) {
    next('/login');
  } else {
    next(); // Allow access to the route
  }
});

export default router;
