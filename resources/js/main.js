import App from '@/App.vue'
import { registerPlugins } from '@core/utils/plugins'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { ability , updateAbilities } from './router/casl/ability'
import piniaPluginPersistedState from 'pinia-plugin-persistedstate'

// Styles
import '@core-scss/template/index.scss'
import '@styles/styles.scss'

// Create vue app
const app = createApp(App)
const pinia = createPinia()

pinia.use(piniaPluginPersistedState) 

app.use(pinia)

updateAbilities()

// Register plugins
registerPlugins(app)

registerRoutes(app)

// Mount vue app
app.mount('#app')
