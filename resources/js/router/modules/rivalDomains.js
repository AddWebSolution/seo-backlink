export const rivalDomainRoutes = [
  // Admin Side
  {
    path: "/admin/:clientId/clientdomain/:domainId/rivaldomain/add",
    name: "domain-clientdomain-rivaldomain-add",
    component: () => import("@/pages/apps/domain/clientdomain/[clientId]/rivaldomain/add/[domainId].vue"),
    props: true,
    meta: {
      title: "Add Rival Domain",
      action: 'create',
      subject: 'rivaldomain',
      roles: [1]
    }
  },
  {
    path: "/admin/:clientId/clientdomain/:domainId/rivaldomain/list",
    name: "domain-clientdomain-rivaldomain-list",
    component: () => import("@/pages/apps/domain/clientdomain/[clientId]/rivaldomain/list/[domainId].vue"),
    props: true,
    meta: {
      title: "Rival Domain List",
      action: 'view',
      subject: 'rivaldomain',
      roles: [1, 2]
    }
  },
  {
    path: "/admin/:clientId/clientdomain/:domainId/rivaldomain/:id/view",
    name: "domain-clientdomain-rivaldomain-view",
    component: () => import("@/pages/apps/domain/clientdomain/[clientId]/rivaldomain/view/[id].vue"),
    props: true,
    meta: {
      title: "Rival Domain View",
      action: 'view',
      subject: 'rivaldomain',
      roles: [1, 2]
    }
  },

  // Shortcut routes (Global views)
  {
    path: "/rivalsdomains/:id",
    name: "rivaldomain-view",
    component: () => import("@/pages/apps/rivaldomain/view/[id].vue"),
    props: true,
    meta: {
      title: "Rival Domain View",
      action: 'view',
      subject: 'rivaldomain',
      roles: [1, 2]
    }
  },
  {
    path: "/rivaldomains/:id/edit",
    name: "rivaldomain-edit",
    component: () => import("@/pages/apps/rivaldomain/edit/[id].vue"),
    props: true,
    meta: {
      title: "Rival Domain Edit",
      action: 'update',
      subject: 'rivaldomain',
      roles: [1]
    }
  },

  // Client Side
  {
    path: "/client/:clientId/domain/:domainId/rivaldomain/list",
    name: "clientdomain-rivaldomain-list",
    component: () => import("@/pages/apps/clientdomain/[clientId]/rivaldomain/list/[domainId].vue"),
    props: true,
    meta: {
      title: "Rival Domain List",
      action: 'view',
      subject: 'rivaldomain',
      roles: [2]
    }
  },
  {
    path: "/client/:clientId/domain/:domainId/rivaldomain/add",
    name: "clientdomain-rivaldomain-add",
    component: () => import("@/pages/apps/clientdomain/[clientId]/rivaldomain/add/[domainId].vue"),
    props: true,
    meta: {
      title: "Add Rival Domain",
      action: 'create',
      subject: 'rivaldomain',
      roles: [2]
    }
  },
  {
    path: "/client/:clientId/domain/:domainId/rivaldomain/:id/view",
    name: "clientdomain-rivaldomain-view",
    component: () => import("@/pages/apps/clientdomain/[clientId]/rivaldomain/view/[id].vue"),
    props: true,
    meta: {
      title: "Rival Domain View",
      action: 'view',
      subject: 'rivaldomain',
      roles: [2]
    }
  },
];
