// navConfig.js
export default [
  { heading: 'Dashboard' },
  {
    title: 'SEO Overview',
    icon: { icon: 'tabler-dashboard' },
    roles: ['super_admin'], 
    to: 'dashboards-analytics',
  },
  { heading: 'Clients' },
  {
    title: 'Client',
    icon: { icon: 'tabler-user' },
    children: [
      { title: 'Client List', to: 'apps-client-list', roles: ['super_admin','client'], icon: { icon: 'tabler-list' } },
      { title: 'Add Client', to: 'apps-client-add', roles: ['super_admin'], icon: { icon: 'tabler-circle-plus' } },
    ],
  },

  { heading: 'Backlinks' },
  {
    title: 'Backlink Report',
    icon: { icon: 'tabler-report' },
    children: [
      { title: 'Report List', to: 'apps-report-list', roles: ['super_admin','client'], icon: { icon: 'tabler-list' } },
    ],
  },

  { heading: 'Keywords' },
  {
    title: 'Keyword',
    icon: { icon: 'tabler-key' },
    children: [
      { title: 'Keyword', to: 'apps-keyword-list', roles: ['super_admin','client'], icon: { icon: 'tabler-list' } },
      { title: 'Add Keyword', to: 'apps-keyword-add', roles: ['super_admin','client'], icon: { icon: 'tabler-circle-plus' } },
    ],
  },

  {
    title: 'Report',
    icon: { icon: 'tabler-report' },
    children: [
      { title: 'Report List', to: 'apps-keywordreport-list', roles: ['super_admin','client'], icon: { icon: 'tabler-list' } },
    ],
  },
]
