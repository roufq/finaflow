import './bootstrap';
import Alpine from 'alpinejs';
import { loadLazyModules } from './lazy-modules';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    loadLazyModules();
});
