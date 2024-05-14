<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Track Orders"></BreadCrumbComponent>
        <div class="row mb-3">
            <div class="col-lg-2">
                <input type="text" class="form-control" v-model="trackingNumber" placeholder="Tracking Number">
            </div>
            <div class="col-lg-2">
                <select v-model="selectedBrand" class="form-control">
                    <option value="" disabled selected>Brand</option>
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
                    <option value="" disabled selected>Tools</option> 
                    <option value="Drill">Drill</option>
                    <option value="Screwdriver">Screwdriver</option>
                    <option value="Wrench">Wrench</option>
                    <option value="Grinder">Grinder</option>
                    <option value="Jigsaw">Jigsaw</option>
                    <option value="Saw">Saw</option>
                </select>
            </div>
            <div class="col-lg-2">
                <select v-model="selectedStatus" class="form-control">
                    <option value="" disabled selected>Status</option> 
                    <option value="Preparing">Preparing</option>
                    <option value="Out for Delivery">Out for Delivery</option>
                    <option value="Delay">Delay</option>
                    <option value="Completed">Completed</option>
                    <option value="Pending">Pending</option>
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
                    :addButton="true"
                    addButtonColor="btn btn-success"
                    btnName="Completed"
                    :otherButton=true
                    otherBtnName="Canceled"
                    @addClicked="completedOrder"
                    @deleteClicked="cancelOrderReason"
                    @editClicked="generateQrCode"
                    @optionalClicked="viewOrder"
                    @otherClicked="canceledOrder"
                >
                </FormComponent>
            </div>
        </div>

        <!-- QR CODE -->
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
                    <button class="btn btn-dark" v-on:click="closeModal">Close</button>
                </div>
            </template>
        </ModalComponent>

        <!-- VIEW ORDER -->
        <ModalComponent :id="modalIdView" :title="modalTitle" :size="modalSizeView" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
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
                            With a total of: <b>₱{{ this.dataValues.total_price }}</b> for the same Tracking Number.
                        </p>
                        <p>
                            Serial Numbers:
                            <ul>
                                <li v-for="serialNumber in this.dataValues.serial_numbers" :key="serialNumber"><b>{{ serialNumber }}</b></li>
                            </ul>
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

        <!-- CANCEL ORDER -->
        <ModalComponent :id="modalIdCancel" :title="modalTitle" :size="modalSizeView" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <div class="col-6 text-center m-auto" v-if="dataValues.image">
                        <img :src="'/images/' + dataValues.image" alt="Current Image" class="img-fluid" style="height:300px;">
                    </div>
                    <div class="col-6">
                        <form>
                            <div>
                                <p><strong>Disclaimer:</strong> Please read the following terms and conditions carefully before proceeding with your request.</p>
                                <p>This service is provided on an "as is" and "as available" basis without warranties of any kind, either express or implied.</p>
                                <p>We reserve the right to modify or discontinue the service at any time without prior notice.</p>
                                <p>By proceeding with your request, you agree to abide by our terms and conditions.</p>
                            </div>
                            <div class="form-group">
                                <label for="cancelReason">Reason for Cancelation:</label>
                                <select class="form-control" id="cancelReason" v-model="dataValues.reason">
                                    <option value="" selected disabled>Select Reason</option>
                                    <option value="Change of Plans">Change of Plans</option>
                                    <option value="Item Unavailable">Item Unavailable</option>
                                    <option value="Delayed Delivery">Delayed Delivery</option>
                                    <option value="Quality Concerns">Quality Concerns</option>
                                    <option value="Found a Better Deal">Found a Better Deal</option>
                                </select>
                            </div>
                            <div class="text-danger" v-if="errors.reason">{{ errors.reason[0] }}</div>
                        </form>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <button class="btn btn-success" v-on:click="cancelOrder">Submit</button>
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
import refresh from "v-tables-3/compiled/methods/refresh";

export default{

    data(){
        return{
                data : [],
                qrCodeUrl: '',
                globalId : '',
                trackingNumber : '',
                selectedBrand : '',
                selectedTool : '',
                selectedStatus : '',
                columns : ['tracking_number', 'brand_name', 'tool_name', 'supplier_name' ,'status' ,'created_at' ,'action'],
                errors: [],
                options : {
                    headings : {
                        tracking_number : 'Tracking Number',
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        supplier_name : 'Supplier',
                        status : 'Status',
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
                modalIdCancel : 'modal-cancel-order',
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
        completedOrder(){
            window.location.href = '/completed-order-admin';
        },
        getData() {
            axios.get('/track-order/show').then(response => {
                this.data = response.data.data;
                this.data.forEach(item => {
                    item.created_at = new Date(item.created_at).toLocaleString(); // Format to the user's locale
                })
            })
        },
        validateForm() {
            this.errors = [];
            if (!this.dataValues.reason) {
                this.errors.reason = ['Reason is required'];
            }

              // Check if any errors are present
              if (Object.keys(this.errors).length > 0) {
                return false; // Validation failed
            }

            return true; // Validation passed
        },
        filterData() {
            const searchData = {
                brand: this.selectedBrand,
                tool: this.selectedTool,
                status: this.selectedStatus,
                trackingNumber: this.trackingNumber
            };

            axios.post('/track-order/filterData', searchData)
                .then(response => {
                    this.data = response.data.data;
                    this.data.forEach(item => {
                        item.created_at = new Date(item.created_at).toLocaleString(); // Format to the user's locale
                    })
                    if (this.data.length === 0) {
                        Swal.fire({
                            title: "No Orders available!",
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
        refresh() {
            window.location.reload();
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
        canceledOrder(){
            window.location.href = '/canceled-order';
        },
        cancelOrderReason(props) {
            this.dataValues = props.data;
            $('#' + this.modalIdCancel).modal('show');
        },
        cancelOrder(props) {
            if (!this.validateForm()) {
                return;
            }
            Swal.fire({
                title: 'Are you sure?',
                text: 'You can always order again!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Cancel it!',
                cancelButtonText: 'No, keep it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/track-order/cancelOrder/', this.dataValues)
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
                                $('#' + this.modalIdCancel).modal('hide');
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
        closeModal() {
            $('#' + this.modalId).modal('hide');
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
