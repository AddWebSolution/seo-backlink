import charts from './charts'
import dashboard from './dashboard'
import forms from './forms'
import others from './others'
import uiElements from './ui-elements'
import navConfig from '../nav/navConfig'

export default [...dashboard, ...navConfig, ...uiElements, ...forms, ...charts, ...others]
