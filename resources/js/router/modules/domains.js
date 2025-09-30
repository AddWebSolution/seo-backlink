export const domainRoutes = [
  {
    path: "/domains/:id",
    name: "apps-domain-view",
    component: () => import("@/pages/apps/domain/view/[id].vue"),
    props: true,
    meta: { title: "Domain View" }
  },
  {
    path: "/domains/:id/edit",
    name: "apps-domain-edit",
    component: () => import("@/pages/apps/domain/edit/[id].vue"),
    props: true,
    meta: { title: "Domain Edit" }
  },
];
