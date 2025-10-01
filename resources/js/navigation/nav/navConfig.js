// navConfig.js
export default [
  { heading: 'Dashboard' },
  {
    title: 'SEO Overview',
    icon: { icon: 'tabler-dashboard' },
    action: 'view',    
    subject: 'dashboard', 
    to: 'dashboards-analytics',
  },
  { heading: 'Clients' },
  {
    title: 'Client',
    icon: { icon: 'tabler-user' },
    action: 'view',
    subject: 'client', 
    children: [
      { title: 'Client List', to: 'apps-client-list', action: 'view', subject: 'client', icon: { icon: 'tabler-list' } },
      { title: 'Add Client', to: 'apps-client-add', action: 'create', subject: 'client', icon: { icon: 'tabler-circle-plus' } },
    ],
  },
  { heading: 'Backlinks' },
  {
    title: 'Backlink Report',
    action: 'view',
    subject: 'report', 
    icon: { icon: 'tabler-report' },
    children: [
      { title: 'Report List', to: 'apps-report-list', action: 'view', subject: 'report', icon: { icon: 'tabler-list' } },
    ],
  },

  { heading: 'Keywords' },
  {
    title: 'Keyword',
    action: 'view',
    subject: 'keyword', 
    icon: { icon: 'tabler-key' },
    children: [
      { title: 'Keyword', to: 'apps-keyword-list', action: 'view', subject: 'keyword', icon: { icon: 'tabler-list' } },
      { title: 'Add Keyword', to: 'apps-keyword-add', action: 'create', subject: 'keyword', icon: { icon: 'tabler-circle-plus' } },
    ],
  },

  {
    title: 'Report',
    icon: { icon: 'tabler-report' },
    action: 'view',
    subject: 'keywordreport', 
    children: [
      { title: 'Report List', to: 'apps-keywordreport-list', action: 'view', subject: 'keywordreport', icon: { icon: 'tabler-list' } },
    ],
  },
]
