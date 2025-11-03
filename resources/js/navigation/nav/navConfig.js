// navConfig.js

export default [
  // ==========================
  // Dashboard
  // ==========================
  { heading: 'Dashboard' },
  {
    title: 'Dashboard',
    icon: { icon: 'tabler-dashboard' },
    to: 'dashboards-analytics',
    meta: {
      action: 'view',
      subject: 'dashboard',
      roles: [1, 2],
    },
  },

  // ==========================
  // Client Management
  // ==========================
  { heading: 'Clients' },
  {
    title: 'Clients',
    icon: { icon: 'tabler-user' },
    to: 'apps-client-list',
    meta: {
      action: 'view',
      subject: 'client',
      roles: [1],
    },
  },

  // ==========================
  // User Management
  // ==========================
  { heading: 'Users' },
  {
    title: 'Users',
    icon: { icon: 'tabler-users' },
    to: 'apps-users-list',
    meta: {
      action: 'view',
      subject: 'user',
      roles: [1],
    },
  },

  // ==========================
  // Domains
  // ==========================
  { heading: 'Domains' },
  {
    title: 'Domains',
    icon: { icon: 'tabler-world' },
    to: 'apps-clientdomain-list',
    meta: {
      action: 'view',
      subject: 'clientdomain',
      roles: [1, 2],
    },
  },

  // ==========================
  // Backlinks
  // ==========================
  { heading: 'Backlinks' },
  {
    title: 'Backlink Reports',
    icon: { icon: 'tabler-report' },
    to: 'apps-report-list',
    meta: {
      action: 'view',
      subject: 'report',
      roles: [1, 2],
    },
  },

  // ==========================
  // Keywords
  // ==========================
  { heading: 'Keywords' },
  {
    title: 'Keywords',
    icon: { icon: 'tabler-key' },
    to: 'apps-keyword-list',
    meta: {
      action: 'view',
      subject: 'keyword',
      roles: [1, 2],
    },
  },
  {
    title: 'Keyword Reports',
    icon: { icon: 'tabler-report-analytics' },
    to: 'apps-keywordreport-list',
    meta: {
      action: 'view',
      subject: 'keywordreport',
      roles: [1, 2],
    },
  },

  // ==========================
  // Settings
  // ==========================
  { heading: 'Settings' },
  {
    title: 'Role & Permissions',
    icon: { icon: 'tabler-lock' },
    to: 'apps-role-permissions-list',
    meta: {
      action: 'view',
      subject: 'role_permission',
      roles: [1],
    },
  },
]
