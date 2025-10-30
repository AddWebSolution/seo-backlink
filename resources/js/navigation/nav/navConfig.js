// navConfig.js
export default [
  { heading: 'Dashboard' },
  {
    title: 'Dashboard',
    icon: { icon: 'tabler-dashboard' },
    to: 'dashboards-analytics',
  },
  { heading: 'Clients' },
  {
    title: 'Client',
    to: 'apps-client-list',
    icon: { icon: 'tabler-user' },
    meta: {
      action: 'view',
      subject: 'client',
      roles: [1],
    },
    // children: [
    //   { title: 'Client List', to: 'apps-client-list', action: 'view', subject: 'client', icon: { icon: 'tabler-list' } },
    //   { title: 'Add Client', to: 'apps-client-add', action: 'create', subject: 'client', icon: { icon: 'tabler-circle-plus' } },
    // ],
  },
  { heading: 'Domains' },
  {
    title: 'Domain',
    icon: { icon: 'tabler-world' },
    to: 'apps-clientdomain-list',
    meta: {
      action: 'view',
      subject: 'clientdomain',
      roles: [1, 3],
    },
    // children: [
    //   { title: 'Domain List', to: 'apps-clientdomain-list', action: 'view', subject: 'clientdomain', icon: { icon: 'tabler-list' } },
    //   { title: 'Domain Add', to: 'apps-clientdomain-add', action: 'create', subject: 'clientdomain', icon: { icon: 'tabler-circle-plus' } },
    // ],
  },
  { heading: 'Backlinks' },
  {
    title: 'Backlink Report',
    icon: { icon: 'tabler-report' },
    to: 'apps-report-list',
    meta: {
      action: 'view',
      subject: 'report',
      roles: [1, 3],
    },
    // children: [
    //   { title: 'Report List', to: 'apps-report-list', action: 'view', subject: 'report', icon: { icon: 'tabler-list' } },
    // ],
  },

  { heading: 'Keywords' },
  {
    title: 'Keyword',
    icon: { icon: 'tabler-key' },
    to: 'apps-keyword-list',
    meta: {
      action: 'view',
      subject: 'keyword',
      roles: [1, 3],
    },
    // children: [
    //   { title: 'Keyword', to: 'apps-keyword-list', action: 'view', subject: 'keyword', icon: { icon: 'tabler-list' } },
    //   { title: 'Add Keyword', to: 'apps-keyword-add', action: 'create', subject: 'keyword', icon: { icon: 'tabler-circle-plus' } },
    // ],
  },

  {
    title: 'Report',
    icon: { icon: 'tabler-report' },
    to: 'apps-keywordreport-list',
    meta: {
      action: 'view',
      subject: 'keywordreport',
      roles: [1, 3],
    },
    // children: [
    //   { title: 'Report List', to: 'apps-keywordreport-list', action: 'view', subject: 'keywordreport', icon: { icon: 'tabler-list' } },
    // ],
  },

    { heading: 'Settings' },
    {
        title: 'Role & Permissions',
        icon: { icon: 'tabler-lock' },
        to: 'apps-role-permissions-list',
        // meta: {
        //     action: 'view',
        //     subject: 'report',
        //     roles: [1, 3],
        // },
        // children: [
        //   { title: 'Report List', to: 'apps-report-list', action: 'view', subject: 'report', icon: { icon: 'tabler-list' } },
        // ],
    },
]
