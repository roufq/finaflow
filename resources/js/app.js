import './bootstrap';
import { loadLazyModules } from './lazy-modules';

document.addEventListener('DOMContentLoaded', () => {
    loadLazyModules();
});
