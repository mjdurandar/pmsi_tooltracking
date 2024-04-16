/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import 'bootstrap';
import { createApp } from 'vue';
import Swal from 'sweetalert2';
import {ServerTable, ClientTable, EventBus} from 'v-tables-3';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

import ExampleComponent from './components/ExampleComponent.vue';
import ProjectSiteComponent from './components/ProjectSiteComponent.vue';
import CategoryComponent from './components/CategoryComponent.vue';
import UnitComponent from './components/UnitComponent.vue';
import SupplierComponent from './components/SupplierComponent.vue';
import ProductComponent from './components/ProductComponent.vue';
import PowerToolsComponent from './components/PowerToolsComponent.vue';
import ScaffoldingComponent from './components/ScaffoldingComponent.vue';
import BuyToolsComponent from './components/BuyToolsComponent.vue';
import DeliveryComponent from './components/DeliveryComponent.vue';
import BorrowToolsComponent from './components/BorrowToolsComponent.vue';
import ReturnDaysComponent from './components/ReturnDaysComponent.vue';
import UsersComponent from './components/UsersComponent.vue';
import BuyingHistoryComponent from './components/BuyingHistoryComponent.vue';
import BorrowedHistoryComponent from './components/BorrowedHistoryComponent.vue';
import DashbboardComponent from './components/DashboardComponent.vue';
import CancelHistoryComponent from './components/CancelHistoryComponent.vue';
import RequestProductComponent from './components/RequestProductComponent.vue';
import ProductHistoryComponent from './components/ProductHistoryComponent.vue';
import ProfileComponent from './components/ProfileComponent.vue';

app.config.globalProperties.$swal = Swal;
app.use(ServerTable, {}, 'bootstrap4')
app.use(ClientTable, {}, 'bootstrap4')
app.use(EventBus, {}, 'bootstrap4')

app.component('example-component', ExampleComponent);
app.component('project-site-component', ProjectSiteComponent);
app.component('category-component', CategoryComponent);
app.component('unit-component', UnitComponent);
app.component('supplier-component', SupplierComponent);
app.component('powertools-component', PowerToolsComponent);
app.component('scaffolding-component', ScaffoldingComponent);
app.component('buytools-component', BuyToolsComponent);
app.component('delivery-component', DeliveryComponent);
app.component('borrowtools-component', BorrowToolsComponent);
app.component('returndays-component', ReturnDaysComponent);
app.component('users-component', UsersComponent);
app.component('buyinghistory-component', BuyingHistoryComponent);
app.component('borrowedhistory-component', BorrowedHistoryComponent);
app.component('dashboard-component', DashbboardComponent);
app.component('request-product-component', RequestProductComponent);
app.component('product-history-component', ProductHistoryComponent);
app.component('profile-component', ProfileComponent);
app.component('product-component', ProductComponent);
app.component('cancel-history-component', CancelHistoryComponent);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');
