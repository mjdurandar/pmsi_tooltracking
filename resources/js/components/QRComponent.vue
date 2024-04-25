<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Delivery Status"></BreadCrumbComponent>
        <div class="card" style="outline: 5px solid #f18f4e  !important;">
            <div class="card-body d-flex justify-content-center align-items-center">
                <div v-for="item in data" :key="item.id">
                    <h1 class="text-center mt-4 mb-5" :class="{ 'text-green': item.status === 'Completed' }">{{ item.status }}</h1>
                    <div class="text-center">
                        <div class="d-flex justify-content-center mb-5">
                            <p class="mr-5">Shipment Date: <b>{{ item.shipment_date }}</b></p>
                            <p>Delivery Date: <b>{{ item.delivery_date }}</b></p>
                        </div>
                        <p style="font-size: 13px;">Note: Please wait for the seller to ship your product. Usually it takes <b>2-3 business days</b> after that the shipment date will update and you can see the exact date your item will arrive.</p>
                        <p class="mb-5" style="font-size: 13px;">Shipment Date and Delivery Date may changed without prior notice due to unbearable circumstances.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>    
import FormComponent from "./partials/FormComponent.vue";   
import ModalComponent from "./partials/ModalComponent.vue";
import BreadCrumbComponent from "./partials/BreadCrumbComponent.vue";
import Swal from 'sweetalert2'
import axios from 'axios';

export default{
    props: ['data'],
    data(){
        return{
            id : [],
            status: '', // Add a status property to bind to the input field
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent,
    },
    methods: {

    },
    mounted() {
    }

}
</script>
