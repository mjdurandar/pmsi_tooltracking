<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Delivery"></BreadCrumbComponent>
        <div class="row mb-3">
            <div class="col-lg-2">
                <select v-model="selectedBrand" class="form-control">
                    <option value="" disabled selected>Select Brand</option>
                    <option value="Bosch">Bosch</option>
                    <option value="Dewalt">Dewalt</option>
                    <option value="Makita">Makita</option>
                    <option value="Milwaukee">Milwaukee</option>
                    <option value="Black+Decker">Black+Decker</option>
                    <option value="Craftsman">Craftsman</option>
                    <option value="Hitachi">Hitachi</option>
                    <option value="Ingersoll">Ingersoll</option>
                    <option value="Porter-Cable">Porter-Cable</option>
                    <option value="Snap-on">Snap-on</option>
                    <option value="Ridgid">Ridgid</option>
                    <option value="Metabo">Metabo</option> 
                    <option value="Ryobi">Ryobi</option> 
                </select>
            </div>
            <div class="col-lg-2">
                <select v-model="selectedTool" class="form-control">
                    <option value="" disabled selected>Select Tools</option> 
                    <option value="Drill">Drill</option>
                    <option value="Screwdriver">Screwdriver</option>
                    <option value="Wrench">Wrench</option>
                    <option value="Grinder">Grinder</option>
                    <option value="Jigsaw">Jigsaw</option>
                    <option value="Saw">Saw</option>
                </select>
            </div>
            <div>
                <button class="btn btn-primary" @click="filterData">Search</button>
                <button class="btn btn-success ml-1" @click="refresh"><i class="fas fa-sync-alt"></i></button>
            </div>
        </div>
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
                    <div class="col-12">
                        <div>QR not working? <a href=/qr>Click Here...</a></div>
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
                selectedBrand : '',
                selectedTool : '',
                columns : [ 'brand_name', 'tool_name', 'product_code' ,'price',  , 'status', 'action'],
                options : {
                    headings : {
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        product_code : 'Product Code',
                        price : 'Price',
                        status : 'Status',
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
        refresh(){
            window.location.reload();
        },
        filterData() {
            const searchData = {
                brand: this.selectedBrand,
                tool: this.selectedTool
            };

            axios.post('/delivery/filterData', searchData)
                .then(response => {
                    this.data = response.data.data;
                    if (this.data.length === 0) {
                        Swal.fire({
                            title: "No Products available!",
                            icon: 'warning',
                            timer: 3000
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to fetch data.",
                        icon: 'error',
                        timer: 3000
                    });
                    console.error(error);
                });
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
