<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Delivery Status"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body d-flex justify-content-center align-items-center">
                <img v-if="status === 'Delay'" src="images/delay.jpg" alt="Delay Image" class="img-fluid">
                <img v-if="status === 'Preparing'" src="images/confirmed.jpg" alt="Preparing Image" class="img-fluid"> 
                <img v-if="status === 'Out For Delivery'" src="images/outdeliver.jpg" alt="Out For Delivery Image" class="img-fluid">
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

    data(){
        return{
            data : [],
            status: '', // Add a status property to bind to the input field
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent,
    },
    methods: {
        getData() {
            axios.get('/qr/show').then(response => {
                this.data = response.data.data;
                if (this.data.length > 0) {
                    this.status = this.data[0].status; // Assign status from the first data item
                } else {
                    this.status = 'No data available'; // Handle case when no data is returned
                }
            })
            .catch(error => {
                console.error(error);
                this.status = 'Error fetching data'; // Show error message if request fails
            });
        },
    },
    mounted() {
        this.getData();
    }

}
</script>
