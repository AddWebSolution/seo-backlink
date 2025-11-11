export const userRoutes = [
    {
        path: '/users/list',
        name: 'users-list',
        component: () => import('@/pages/apps/users/list/index.vue'),
        meta: { title: 'User List', action: 'view', subject: 'user', roles: [1] },
    },
    {
        path: '/users/add',
        name: 'users-add',
        component: () => import('@/pages/apps/users/add/add.vue'),
        meta: { title: 'Add User', action: 'create', subject: 'user', roles: [1] },
    },
    {
        path: '/users/:id',
        name: 'users-view',
        component: () => import('@/pages/apps/users/view/[id].vue'),
        meta: { title: 'User View', action: 'view', subject: 'user', roles: [1] },
    },
    {
        path: '/users/:id/edit',
        name: 'users-edit',
        component: () => import('@/pages/apps/users/edit/[id].vue'),
        meta: { title: 'User Edit', action: 'update', subject: 'user', roles: [1] },
    },
]