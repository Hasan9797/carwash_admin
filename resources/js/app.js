import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import registerThemeSwitcher from './theme-switcher';

window.Alpine = Alpine;
window.Chart = Chart;

registerThemeSwitcher(Alpine);

Alpine.start();
