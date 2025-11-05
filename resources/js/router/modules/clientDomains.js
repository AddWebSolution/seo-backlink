export const clientDomainRoutes = [
{
    path: "/apps/clientdomain/add",
    name: "apps-clientdomain-add",
    component: () => import("@/pages/apps/clientdomain/add/[id].vue"),
    props: true,
    meta: {
      title: "Add Client Domain",
      action: "create",
      subject: "clientdomain",
      roles: [1,2] 
    }
  },
  {
    path: "/apps/clientdomain/list",
    name: "apps-clientdomain-list",
    component: () => import("@/pages/apps/clientdomain/list/[id].vue"),
    props: true,
    meta: {
      title: "Client Domain List",
      action: "view",
      subject: "clientdomain",
      roles: [1 ,2] 
    }
  },
  {
    path: "/apps/:view?/:id?/history",
    name: "apps-clientdomain-history",
    component: () => import("@/pages/apps/clientdomain/history/[id].vue"),
    props: true,
    meta: {
      title: "Domain History",
      action: "view",
      subject: "clientdomain",
      roles: [1 ,2] 
    }
  },
  {
    path: "/apps/client/:clientId?/clientdomain/:domainId?/view",
    name: "apps-clientdomain-view",
    component: () => import("@/pages/apps/clientdomain/view/[domainId].vue"),
    props: true,
    meta: {
      title: "Client Domain View",
      action: "view",
      subject: "clientdomain",
      roles: [1 ,2] 
    }
  },

]