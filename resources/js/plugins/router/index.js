import { createRouter, createWebHistory } from 'vue-router';
import { routes } from './routes';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

// Navigation guard to check if the route requires authentication
router.beforeEach((to, from, next) => {
  // Check if the route requires authentication
  if (to.meta.requiresAuth) {
    // Check if the user is authenticated (e.g., token in localStorage)
    const token = localStorage.getItem('auth_token');
    
    if (token) {
      // User is authenticated, allow access
      next();
    } else {
      // User is not authenticated, redirect to login page
      next('/login');
    }
  } else {
    // Route does not require authentication, allow access
    next();
  }
});

export default function (app) {
  app.use(router)
}

export { router };

