import { abilitiesPlugin } from '@casl/vue'
import { ability } from './ability'

export default function (app) {
  app.use(abilitiesPlugin, ability, { useGlobalProperties: true })
}
