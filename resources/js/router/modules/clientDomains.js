export const clientDomainRoutes = [
{
    path: "/clientdomain/add",
    name: "clientdomain-add",
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
    path: "/clientdomain/list",
    name: "clientdomain-list",
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
    path: "/:view?/:id?/history",
    name: "clientdomain-history",
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
    path: "/client/:clientId?/clientdomain/:domainId?/view",
    name: "clientdomain-view",
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