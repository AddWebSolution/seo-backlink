export const dashboardRoutes = [
    {
    path: "/dashboards/analytics",
    name: "dashboards-analytics",
    component: () => import("@/pages/dashboards/index.vue"),
    meta: {
      title: "Dashboard Analytics",
      action: 'view',
      subject: 'dashboard',
      roles: [1, 2],
    }

  },
  // {
  //   path: "/dashboards/logistics",
  //   name: "dashboards-logistics",
  //   component: () => import("@/pages/apps/logistics/dashboard.vue"),
  //   meta: {
  //     title: "Logistics Dashboard",
  //     action: 'view',
  //     subject: 'dashboard',
  //     roles: [1, 2],
  //   }

  // },
  // {
  //   path: "/apps/ecommerce/dashboard",
  //   name: "apps-ecommerce-dashboard",
  //   component: () => import("@/pages/dashboards/ecommerce.vue"),
  //   meta: {
  //     title: "Logistics Dashboard",
  //     action: 'view',
  //     subject: 'dashboard',
  //     roles: [1, 2],
  //   }
  // },
];
