import Alpine from 'alpinejs'
import persist from '@alpinejs/persist'
import '@tailwindplus/elements';
import ApexCharts from 'apexcharts'
 
Alpine.plugin(persist)
 
window.Alpine = Alpine
 
Alpine.start()

window.ApexCharts = ApexCharts