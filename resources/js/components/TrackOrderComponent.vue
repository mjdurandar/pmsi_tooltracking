<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Track Orders"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    :option2Switch="true"
                    option1Color="color: #0000FF;"
                    option1Icon="fa-solid fa-qrcode"
                    option1Name=""
                    option2Color="color: #FF0000;"
                    option2Icon="fa-solid fa-xmark"
                    option2Name=""
                    :option3Switch="true"
                    option3Icon="fa-solid fa-eye"
                    option3Name=""
                    :addButton="false"
                    @deleteClicked="cancelOrder"
                    @editClicked="generateQrCode"
                    @optionalClicked="viewOrder"
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
                        <div>QR not working?  <a :href="'/qr/' + this.globalId">Click Here...</a></div>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-dark" v-on:click="storeData">Close</button>
                </div>
            </template>
        </ModalComponent>

        <ModalComponent :id="modalIdView" :title="modalTitle" :size="modalSizeView" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>View your Product</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-6 text-center m-auto" v-if="dataValues.image">
                        <img :src="'/images/' + dataValues.image" alt="Current Image" class="img-fluid" style="height:300px;">
                    </div>
                    <div class="col-6">
                        <p>
                            <b>{{ this.dataValues.brand_name }} {{ this.dataValues.tool_name }}</b> with the voltage of {{ this.dataValues.voltage }}, dimension of {{ this.dataValues.dimensions }}, weight of {{ this.dataValues.weight }} and powerSources of {{ this.dataValues.powerSources }}.
                        </p>
                        <p>
                            You ordered it At : <b>{{this.dataValues.created_at}}</b>
                        </p>
                        <p>
                            Please wait for your Product usually it takes <b> 2-3 business days</b> to deliver. 
                        </p>
                        <p>
                            You can always track your order by clicking the <b>QR code</b>.
                        </p>
                    </div>
                </div>
            </template>
            <template #modalFooter>
            </template>
        </ModalComponent>
    </div>
</template>

<script>    
import FormComponent from "./partials/FormComponent.vue";   
import ModalComponent from "./partials/ModalComponent.vue";
import BreadCrumbComponent from "./partials/BreadCrumbComponent.vue";
import Swal from 'sweetalert2'
import axios from 'axios';
import QRCode from 'qrcode-generator';

export default{

    data(){
        return{
                data : [],
                qrCodeUrl: '',
                globalId : '',
                columns : ['brand_name', 'tool_name', 'serial_number' ,'created_at' ,'action'],
                errors: [],
                options : {
                    headings : {
                        brand : 'Brand',
                        tool : 'Tool',
                        serial_number : 'Serial Number',
                        created_at : 'Placed Order At',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-track-order',
                modalIdView : 'modal-view-order',
                modalSizeView : 'modal-lg',
                modalTitle : 'Track Order',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-sm',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent,
    },
    methods: {
        addClicked(props){
            $('#' + this.modalId).modal('show');
            this.clearInputs();
        },
        getData() {
            axios.get('/track-order/show').then(response => {
                this.data = response.data.data;
                this.data.forEach(item => {
                    item.created_at = new Date(item.created_at).toLocaleString(); // Format to the user's locale
                })
            })
        },
        viewOrder(props) {
            this.dataValues = props.data;
            $('#' + this.modalIdView).modal('show');
        },
        generateQrCode(props) {
            this.globalId = props.data.id;
            // Generate the URL of the route you want to redirect to
            const routeUrl = 'pmsitooltracking.com/qr/' + props.data.id;
                
                // Create QR code with the route URL
                const qr = QRCode(0, 'L');
                qr.addData(routeUrl);
                qr.make();
                const qrUrl = qr.createDataURL(4);
                
                // Store the QR code URL and show the modal
                this.qrCodeUrl = qrUrl;
                $('#' + this.modalId).modal('show');

        },
        cancelOrder(props) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You can always order again!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Cancel it!',
                cancelButtonText: 'No, keep it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/track-order/cancelOrder/', props.data)
                        .then(response => {
                            if (response.status === 200) {
                                if (response.data.status === 'success') {
                                    Swal.fire({
                                        title: "Success",
                                        text: response.data.message,
                                        icon: 'success',
                                        timer: 3000
                                    });
                                } else if (response.data.status === 'warning') {
                                    Swal.fire({
                                        title: "Warning",
                                        text: response.data.message,
                                        icon: 'warning',
                                        timer: 3000
                                    });
                                }
                                this.getData();
                            }
                        })
                        .catch(errors => {
                            if (errors.response.data.message.length > 0) {
                                Swal.fire({
                                    title: "Failed",
                                    text: errors.response.data.message,
                                    icon: 'error',
                                    timer: 3000
                                });
                            }
                        });
                }
            });
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        editClicked(props) {
            this.dataValues = props.data;
            this.modalTitle= 'Edit Data';

            axios.get('/unit/edit/' + this.dataValues.id).then(response => {
                this.dataValues = response.data.data;
                $('#' + this.modalId).modal('show');
            })
            .catch(errors => {
                if(errors.response.data.message.length > 0) {
                    Swal.fire({
                        title: "Failed",
                        text: errors.response.data.message,
                        icon: 'error',
                        timer: 3000
                    });
                    this.errors = errors.response.data.errors;
                }
            })
        },
        deleteClicked(props) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this data!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with deletion
                    axios.get('/unit/destroy/' + props.data.id).then(response => {
                        if(response.status === 200) {
                            Swal.fire({
                                title: "Success",
                                text: response.data.message,
                                icon: 'success',
                                timer: 3000
                            });
                        }
                        this.getData();
                    }).catch(errors => {
                        if(errors.response.data.message.length > 0) {
                            Swal.fire({
                                title: "Failed",
                                text: errors.response.data.message,
                                icon: 'error',
                                timer: 3000
                            });
                        }
                    });
                }
            });
        },
        storeData() {
                axios.post('/unit/store', this.dataValues).then(response => {
                    if(response.status === 200) {
                        Swal.fire({
                            title: "Success",
                            text: response.data.message,
                            icon: 'success',
                            timer: 3000
                        });
                    }
                    this.getData();
                    $('#' + this.modalId).modal('hide');
                })
                .catch(errors => {
                        this.errors = errors.response.data.errors;
                })
            },
        },
        mounted() {
            this.getData();
        }
        


}

</script>
