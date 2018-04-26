require('./bootstrap.js');
import Employee from './components/Employee/index.vue';
import DataViewer from './components/Employee/DataViewer/DataViewer.vue';
new Vue({
    el: '#app',
    components: { Employee, DataViewer }
})