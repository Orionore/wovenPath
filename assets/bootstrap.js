import { Application } from '@hotwired/stimulus'
import MobileMenuController from './controllers/mobile-menu_controller.js'
import Dropdown from './controllers/dropdown-select_controller.js'
import StoryImagePreview from './controllers/story-image-preview_controller.js'

const app = Application.start()

app.register('mobile-menu', MobileMenuController)
app.register('dropdown-select', Dropdown)
app.register('story-image-preview', StoryImagePreview)

export { app }