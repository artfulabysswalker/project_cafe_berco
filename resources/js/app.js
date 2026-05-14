import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import { filterHistory, resetFilter } from './history';

window.filterHistory = filterHistory;
window.resetFilter = resetFilter;