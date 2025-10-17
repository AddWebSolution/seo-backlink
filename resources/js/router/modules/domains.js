export const domainRoutes = [
  {
    path: "/domains/:id",
    name: "apps-domain-view",
    component: () => import("@/pages/apps/domain/view/[id].vue"),
    props: true,
    meta: {
      title: "Domain View",
      action: "view",
      subject: "clientdomain",
      roles: [1 ,3] 
    }
  },
  {
    path: "/domains/:id/edit",
    name: "apps-domain-edit",
    component: () => import("@/pages/apps/domain/edit/[id].vue"),
    props: true,
    meta: {
      title: "Domain Edit",
      action: "edit",
      subject: "clientdomain",
      roles: [1] 
    }
  },
  {
    path: "/clientdomain/add",
    name: "apps-clientdomain-add",
    component: () => import("@/pages/apps/clientdomain/add/[id].vue"),
    props: true,
    meta: {
      title: "Add Client Domain",
      action: "create",
      subject: "clientdomain",
      roles: [1,3] 
    }
  },
  {
    path: "/clientdomain/list",
    name: "apps-clientdomain-list",
    component: () => import("@/pages/apps/clientdomain/list/[id].vue"),
    props: true,
    meta: {
      title: "Client Domain List",
      action: "view",
      subject: "clientdomain",
      roles: [1 ,3] 
    }
  },
  {
    path: "/:view?/clientdomain/:id?/history",
    name: "apps-clientdomain-history",
    component: () => import("@/pages/apps/clientdomain/history/[id].vue"),
    props: true,
    meta: {
      title: "Domain History",
      action: "view",
      subject: "clientdomain",
      roles: [1 ,3] 
    }
  },
  {
    path: "/client/:clientId?/clientdomain/:domainId?/view",
    name: "apps-clientdomain-view",
    component: () => import("@/pages/apps/clientdomain/view/[domainId].vue"),
    props: true,
    meta: {
      title: "Client Domain View",
      action: "view",
      subject: "clientdomain",
      roles: [1 ,3] 
    }
  },
];
