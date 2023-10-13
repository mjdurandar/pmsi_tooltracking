<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Delivery"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    :option2Switch="false"
                    option1Color="color: #0000FF;"
                    option1Icon="fa-solid fa-qrcode"
                    option1Name="QR Code"
                    :addButton="false"
                    @editClicked="generateQrCode"
                >
                </FormComponent>
            </div>
        </div>

        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>QR Code</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-12">
                        <img :src="qrCodeUrl" alt="QR Code" class="qr-code">
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-dark" v-on:click="storeData">Close</button>
                </div>
            </template>
        </ModalComponent>

    </div>
</template>

<style>
    .qr-code {
        width: 100%;
        height: 100%;
    }
</style>

<script>    
import BreadCrumbComponent from "./partials/BreadCrumbComponent.vue";   
import FormComponent from "./partials/FormComponent.vue";   
import ModalComponent from "./partials/ModalComponent.vue";
import Swal from 'sweetalert2'
import axios from 'axios';
import QRCode from 'qrcode-generator';

export default{

    data(){
        return{
                data : [],
                errors: [],
                qrCodeUrl: '',
                columns : [ 'user_name' , 'power_tools_name' , 'quantity', 'total' , 'action'],
                options : {
                    headings : {
                        user_name : 'Name',
                        power_tools_name : 'Power Tools',
                        quantity : 'Quantity',
                        total : 'Total',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-delivery',
                modalTitle : 'Delivery',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-sm',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent
    },
    methods: {
        storeData(props){
            $('#' + this.modalId).modal('hide');
            this.clearInputs();
        },
        getData() {
            axios.get('/delivery/show').then(response => {
                this.data = response.data.data;
            })
        },
        generateQrCode(props) {
            const qr = QRCode(0, 'L'); // Create a QRCode object
            qr.addData(props); // Add data to encode
            qr.make(); // Calculate QRCode data
            const qrUrl = qr.createDataURL(4); // Generate a base64 encoded PNG
            
            this.qrCodeUrl = qrUrl; // Store the URL in data
            $('#' + this.modalId).modal('show');

            },
        },
        mounted() {
            this.getData();
        }
        


}

</script>
