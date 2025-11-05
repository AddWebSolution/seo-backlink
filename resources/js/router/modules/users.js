export const userRoutes = [
    {
        path: '/apps/users/list',
        name: 'apps-users-list',
        component: () => import('@/pages/apps/users/list/index.vue'),
        meta: { title: 'User List', action: 'view', subject: 'user', roles: [1] },
    },
    {
        path: '/apps/users/add',
        name: 'apps-users-add',
        component: () => import('@/pages/apps/users/add/add.vue'),
        meta: { title: 'Add User', action: 'create', subject: 'user', roles: [1] },
    },
    {
        path: '/apps/users/:id',
        name: 'apps-users-view',
        component: () => import('@/pages/apps/users/view/[id].vue'),
        meta: { title: 'User View', action: 'view', subject: 'user', roles: [1] },
    },
    {
        path: '/apps/users/:id/edit',
        name: 'apps-users-edit',
        component: () => import('@/pages/apps/users/edit/[id].vue'),
        meta: { title: 'User Edit', action: 'update', subject: 'user', roles: [1] },
    },
]