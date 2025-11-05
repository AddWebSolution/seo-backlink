export const roleRoutes = [

  {
    path: "/role-permissions/list",
    name: "apps-role-permissions-list",
    component: () => import("@/pages/apps/role-permissions/list/index.vue"),
    props: true,
    meta: {
      title: "Role & Permissions List",
      action: 'view',
      subject: 'role_permission',
      roles: [1, 2],
    }
  },
];
