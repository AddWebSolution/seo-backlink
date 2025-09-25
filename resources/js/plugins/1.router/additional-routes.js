const emailRouteComponent = () => import('@/pages/apps/email/index.vue')

// 👉 Redirects
export const redirects = [
  // ℹ️ We are redirecting to different pages based on role.
  // NOTE: Role is just for UI purposes. ACL is based on abilities.
  {
    path: '/',
    name: 'index',
    redirect: to => {
      return { name: 'login' }
    },
  },
  {
    path: '/pages/user-profile',
    name: 'pages-user-profile',
    redirect: () => ({ name: 'pages-user-profile-tab', params: { tab: 'profile' } }),
  },
  {
    path: '/pages/account-settings',
    name: 'pages-account-settings',
    redirect: () => ({ name: 'pages-account-settings-tab', params: { tab: 'account' } }),
  },
]
export const routes = [
  // Domain
  {
    path: "/domains/:id",
    name: "apps-domain-view",
    component: () => import("@/pages/apps/domain/view/[id].vue"),
    props: true,
  },
  {
    path: "/domains/:id/edit",
    name: "apps-domain-edit",
    component: () => import("@/pages/apps/domain/edit/[id].vue"),
    props: true,
  },

  // Rival Domain
  {
    path: "/rivalsdomains/:id",
    name: "apps-rivaldomain-view",
    component: () => import("@/pages/apps/rivaldomain/view/[id].vue"),
    props: true,
  },
  {
    path: "/rivaldomains/:id/edit",
    name: "apps-rivaldomain-edit",
    component: () => import("@/pages/apps/rivaldomain/edit/[id].vue"),
    props: true,
  },



  // Client
  {
    path: "/clients/:id",
    name: "apps-client-view",
    component: () => import("@/pages/apps/client/view/[id].vue"),
    props: true,
  },
  {
    path: "/clients/:id/edit",
    name: "apps-client-edit",
    component: () => import("@/pages/apps/client/edit/[id].vue"),
    props: true,
  },


  // Keyword
  {
    path: "/keywords/:id",
    name: "apps-keyword-view",
    component: () => import("@/pages/apps/keyword/view/[id].vue"),
    props: true,
  },
  {
    path: "/keywords/:id/edit",
    name: "apps-keyword-edit",
    component: () => import("@/pages/apps/keyword/edit/[id].vue"),
    props: true,
  },
  // Backlinks Report
  {
    path: "/report/:id",
    name: "apps-report-view",
    component: () => import("@/pages/apps/report/view/[id].vue"),
    props: true,
  },

  {
    path: "/report/backlink/:id",
    name: "apps-report-backlink-view",
    component: () => import("@/pages/apps/report/backlink/view/[id].vue"),
    props: true,
  },


  // Keyword Report

  {
    path: "/keywordreport/:id",
    name: "apps-keywordreport-view",
    component: () => import("@/pages/apps/keywordreport/view/[id].vue"),
    props: true,
  },

  {
    path: "/keywordreport/keyword/:id",
    name: "apps-keywordreport-keyword-view",
    component: () => import("@/pages/apps/keywordreport/keyword/view/[id].vue"),
    props: true,
  },

  // Email filter
  {
    path: "/apps/email/filter/:filter",
    name: "apps-email-filter",
    component: emailRouteComponent,
    meta: {
      navActiveLink: "apps-email",
      layoutWrapperClasses: "layout-content-height-fixed",
    },
  },

  // Email label
  {
    path: "/apps/email/label/:label",
    name: "apps-email-label",
    component: emailRouteComponent,
    meta: {
      // contentClass: 'email-application',
      navActiveLink: "apps-email",
      layoutWrapperClasses: "layout-content-height-fixed",
    },
  },
  {
    path: "/dashboards/logistics",
    name: "dashboards-logistics",
    component: () => import("@/pages/apps/logistics/dashboard.vue"),
  },
  // {
  //   path: '/dashboards/academy',
  //   name: 'dashboards-academy',
  //   component: () => import('@/pages/apps/academy/dashboard.vue'),
  // },
  {
    path: "/apps/ecommerce/dashboard",
    name: "apps-ecommerce-dashboard",
    component: () => import("@/pages/dashboards/ecommerce.vue"),
  },
];
