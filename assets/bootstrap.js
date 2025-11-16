import Alpine from 'alpinejs'
import persist from '@alpinejs/persist'
import '@tailwindplus/elements';
 
Alpine.plugin(persist)
 
window.Alpine = Alpine
 
Alpine.start()