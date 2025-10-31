export const userRoutes = [
  {
    path: '/users/list',
    name: 'apps-users-list',
    component: () => import('@/pages/apps/users/list/index.vue'),
    props: true,
    // meta: {
    //   title: 'User List',
    //   action: 'view',
    //   subject: 'users',
    //   roles: [1]
    // }
  },
  {
    path: "/users/:id",
    name: "apps-users-view",
    component: () => import("@/pages/apps/users/view/[id].vue"),
    props: true,
    // meta: {
    //   title: "User View",
    //   action: 'view',
    //   subject: 'users',
    //   roles: [1],
    // }
  },
  {
    path: "/users/:id/edit",
    name: "apps-users-edit",
    component: () => import("@/pages/apps/users/edit/[id].vue"),
    props: true,
    // meta: {
    //   title: "User Edit",
    //   action: 'update',
    //   subject: 'users',
    //   roles: [1],
    // }
  },
];
