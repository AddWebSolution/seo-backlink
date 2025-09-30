import charts from './charts'
import dashboard from './dashboard'
import forms from './forms'
import others from './others'
import uiElements from './ui-elements'
import navByRole from '../nav/navByRole'

console.log('Filtered Navigation:', navByRole);

export default [...dashboard, ...navByRole, ...uiElements, ...forms, ...charts, ...others]
