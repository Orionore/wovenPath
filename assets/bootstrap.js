import { Application } from '@hotwired/stimulus'
import MobileMenuController from './controllers/mobile-menu_controller.js'

const app = Application.start()

app.register('mobile-menu', MobileMenuController)

export { app }