export const reportRoutes = [
  {
    path: "/report/:id",
    name: "apps-report-view",
    component: () => import("@/pages/apps/report/view/[id].vue"),
    props: true,
    meta: { title: "Report View" }
  },
  {
    path: "/report/backlink/:id",
    name: "apps-report-backlink-view",
    component: () => import("@/pages/apps/report/backlink/view/[id].vue"),
    props: true,
    meta: { title: "Backlink Report View" }
  },
  {
    path: "/keywordreport/:id",
    name: "apps-keywordreport-view",
    component: () => import("@/pages/apps/keywordreport/view/[id].vue"),
    props: true,
    meta: { title: "Keyword Report View" }
  },
  {
    path: "/keywordreport/keyword/:id",
    name: "apps-keywordreport-keyword-view",
    component: () => import("@/pages/apps/keywordreport/keyword/view/[id].vue"),
    props: true,
    meta: { title: "Keyword Report Detail" }
  },
];
