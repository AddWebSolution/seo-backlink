// navConfig.js
export default [
  { heading: 'Dashboard' },
  {
    title: 'Dashboard',
    icon: { icon: 'tabler-dashboard' },
    // action: 'view',    
    // subject: 'dashboard', 
    to: 'dashboards-analytics',
  },
  { heading: 'Clients' },
  {
    title: 'Client',
    icon: { icon: 'tabler-user' },
    action: 'view',
    subject: 'client',
    to : 'apps-client-list',
    // children: [
    //   { title: 'Client List', to: 'apps-client-list', action: 'view', subject: 'client', icon: { icon: 'tabler-list' } },
    //   { title: 'Add Client', to: 'apps-client-add', action: 'create', subject: 'client', icon: { icon: 'tabler-circle-plus' } },
    // ],
  },
  { heading: 'Domains' },
  {
    title: 'Domain',
    icon: { icon: 'tabler-world' },
    action: 'view',
    subject: 'clientdomain',
    to : 'apps-clientdomain-list',
    // children: [
    //   { title: 'Domain List', to: 'apps-clientdomain-list', action: 'view', subject: 'clientdomain', icon: { icon: 'tabler-list' } },
    //   { title: 'Domain Add', to: 'apps-clientdomain-add', action: 'create', subject: 'clientdomain', icon: { icon: 'tabler-circle-plus' } },
    // ],
  },
  { heading: 'Backlinks' },
  {
    title: 'Backlink Report',
    action: 'view',
    subject: 'report', 
    icon: { icon: 'tabler-report' },
    to : 'apps-report-list',
    // children: [
    //   { title: 'Report List', to: 'apps-report-list', action: 'view', subject: 'report', icon: { icon: 'tabler-list' } },
    // ],
  },

  { heading: 'Keywords' },
  {
    title: 'Keyword',
    action: 'view',
    subject: 'keyword', 
    icon: { icon: 'tabler-key' },
    to : 'apps-keyword-list',
    // children: [
    //   { title: 'Keyword', to: 'apps-keyword-list', action: 'view', subject: 'keyword', icon: { icon: 'tabler-list' } },
    //   { title: 'Add Keyword', to: 'apps-keyword-add', action: 'create', subject: 'keyword', icon: { icon: 'tabler-circle-plus' } },
    // ],
  },

  {
    title: 'Report',
    icon: { icon: 'tabler-report' },
    action: 'view',
    subject: 'keywordreport', 
    to : 'apps-keywordreport-list',
    // children: [
    //   { title: 'Report List', to: 'apps-keywordreport-list', action: 'view', subject: 'keywordreport', icon: { icon: 'tabler-list' } },
    // ],
  },
]
