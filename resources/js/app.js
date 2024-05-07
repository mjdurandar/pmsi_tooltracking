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
import AddBalanceComponent from './components/AddBalanceComponent.vue';
import CancelHistoryComponent from './components/CancelHistoryComponent.vue';
import RequestProductComponent from './components/RequestProductComponent.vue';
import ProductHistoryComponent from './components/ProductHistoryComponent.vue';
import OrderComponent from './components/OrderComponent.vue';
import QRComponent from './components/QRComponent.vue';
import ProductReturnComponent from './components/ProductReturnComponent.vue';
import HomeComponent from './components/HomeComponent.vue';
import ProfileComponent from './components/ProfileComponent.vue';
import DamagedReturnComponent from './components/DamagedReturnComponent.vue';
import DefectiveProductsComponent from './components/DefectiveProductsComponent.vue';
import ReturnedProductComponent from './components/ReturnedProductComponent.vue';
import CanceledProductComponent from './components/CanceledProductComponent.vue';
import TrackOrderComponent from './components/TrackOrderComponent.vue'; 
import OrderedProductComponent from './components/OrderedProductComponent.vue';
import CanceledOrderComponent from './components/CanceledOrderComponent.vue';
import AdminProductsComponent from './components/AdminProductsComponent.vue';
import CompletedOrderAdminComponent from './components/CompletedOrderAdminComponent.vue';
import CompletedOrderedProductComponent from './components/CompletedOrderedProductComponent.vue';
import CanceledOrderedProductComponent from './components/CanceledOrderedProductComponent.vue';
import AdminReturnedProductComponent from './components/AdminReturnedProductComponent.vue';
import ReturnedProductsSupplierComponent from './components/ReturnedProductsSupplierComponent.vue';

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
app.component('add-balance-component', AddBalanceComponent);
app.component('order-component', OrderComponent);
app.component('qr-component', QRComponent);
app.component('productreturn-component', ProductReturnComponent);
app.component('home-component', HomeComponent);
app.component('returned-product-component', ReturnedProductComponent);
app.component('damaged-return-component', DamagedReturnComponent);
app.component('defective-products-component', DefectiveProductsComponent);
app.component('canceled-product-component', CanceledProductComponent);
app.component('track-order-component', TrackOrderComponent);
app.component('ordered-product-component', OrderedProductComponent);
app.component('canceled-order-component', CanceledOrderComponent);
app.component('admin-products-component', AdminProductsComponent);
app.component('completed-order-admin-component', CompletedOrderAdminComponent);
app.component('completed-ordered-product-component', CompletedOrderedProductComponent);
app.component('canceled-ordered-product-component', CanceledOrderedProductComponent);
app.component('admin-returned-product-component', AdminReturnedProductComponent);
app.component('returned-products-supplier-component', ReturnedProductsSupplierComponent);

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
