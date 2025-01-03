export const routes = [
  { path: '/', redirect: '/dashboard' },
  {
    path: '/',
    component: () => import('@/layouts/default.vue'),
    children: [
      {
        path: 'dashboard',
        component: () => import('@/pages/dashboard.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'account-settings',
        component: () => import('@/pages/account-settings.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'typography',
        component: () => import('@/pages/typography.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'icons',
        component: () => import('@/pages/icons.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'cards',
        component: () => import('@/pages/cards.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'tables',
        component: () => import('@/pages/tables.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'form-layouts',
        component: () => import('@/pages/form-layouts.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'api/leads',
        component: () => import('@/pages/api/leads.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'integration',
        component: () => import('@/pages/integration.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'settings/tags-stages-sources',
        component: () => import('@/pages/settings/tags-stages-sources.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'settings/signature',
        component: () => import('@/pages/settings/signature.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'admin/my-profile',
        component: () => import('@/pages/profile/profile.vue'),
        meta: { requiresAuth: true }
      }
    ],
  },
  {
    path: '/',
    component: () => import('@/layouts/blank.vue'),
    children: [
      {
        path: 'auth/login',
        component: () => import('@/pages/login.vue'),
      },
      {
        path: 'auth/register',
        component: () => import('@/pages/register.vue'),
      },
      {
        path: '/:pathMatch(.*)*',
        component: () => import('@/pages/[...error].vue'),
      },
    ],
  },
]
