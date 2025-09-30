export const dashboardRoutes = [
  {
    path: "/dashboards/logistics",
    name: "dashboards-logistics",
    component: () => import("@/pages/apps/logistics/dashboard.vue"),
    meta: { title: "Logistics Dashboard" }
  },
  {
    path: "/apps/ecommerce/dashboard",
    name: "apps-ecommerce-dashboard",
    component: () => import("@/pages/dashboards/ecommerce.vue"),
    meta: { title: "Ecommerce Dashboard" }
  },
];
