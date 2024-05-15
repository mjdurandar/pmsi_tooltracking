<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Returned Products"></BreadCrumbComponent>
        <div class="row mb-3">
            <div class="col-lg-2">
                <input type="text" class="form-control" v-model="serialNumber" placeholder="Serial Number">
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
                    <option value="" disabled selected>Tool</option> 
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
                    :addButton="true"
                    :option1Switch="false"
                    :option2Switch="false"
                    :option3Switch="true"
                    btnName="Completed"
                    :addButtonColor="'btn btn-success'"
                    @optionalClicked="viewProduct"
                    @addClicked="completeWindow"
                    option3Name=""
                    option3Icon="fa fa-eye"
                    option2Name=""
                >
                </FormComponent>
            </div>
        </div>

        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
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
                            User request to return the product : <b>{{this.dataValues.returned_at}}</b>
                        </p>
                        <p>
                            Reason : <b>{{this.dataValues.reason}}</b>
                        </p>
                        <p>
                            Please Contact the user with this information.
                            <ul>
                                <li><b>{{ this.dataValues.user_name }}</b></li>
                                <li><b>{{ this.dataValues.user_email }}</b></li>
                                <li><b>{{ this.dataValues.user_phone }}</b></li>
                                <li><b>{{ this.dataValues.location }}</b></li>
                            </ul>
                        </p>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <button type="button" class="btn btn-warning" @click="reviewApprove">Approve</button>
            </template>
        </ModalComponent>

        <!-- REVIEW APPROVE MODAL -->
        <ModalComponent :id="modalIdReview" :title="modalTitle" :size="modalSizeReview" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                </div>
            </template>
            <template #modalBody>
                <div class="row">
                    <label for="">Please document what happened between you and the user after you contact them.</label>
                    <textarea type="text" class="form-control" v-model="dataValues.description"></textarea>
                    <div class="text-danger" v-if="errors.description">{{ errors.description[0] }}</div>
                </div>
                <div class="row">
                    <label for="">Status</label>
                    <select v-model="dataValues.selectedStatus" class="form-control">
                        <option value="Completed">Completed</option>
                        <option value="Rejected">Rejected</option>
                    </select>   
                    <div class="text-danger" v-if="errors.selectedStatus">{{ errors.selectedStatus[0] }}</div>
                </div>
            </template>
            <template #modalFooter>
                <button type="button" class="btn btn-success" @click="submit">Submit</button>
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

export default{

    data(){
        return{
                data : [],
                serialNumber : '',
                selectedBrand : '',
                selectedTool : '',
                description : '',
                columns : ['brand_name', 'tool_name', 'serial_number', 'returned_at' ,'action'],
                errors: [],
                options : {
                    headings : {
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        serial_number : 'Serial Number',
                        returned_at : 'Returned At',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                    selectedStatus: '',
                },
                modalId : 'modal-unit',
                modalIdReview : 'modal-review',
                modalTitle : 'Unit',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-lg',
                modalSizeReview : 'modal-md',
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
            axios.get('returned-products-supplier/show').then(response => {
                this.data = response.data.data;
            })
        },
        refresh()
        {
            window.location.reload();   
        },
        validateForm()
        {
            this.errors = [];
            if (!this.dataValues.description) {
                this.errors.description = ['Description is required'];
            }

            if (!this.dataValues.selectedStatus) {
                this.errors.selectedStatus = ['Status is required'];
            }

              // Check if any errors are present
              if (Object.keys(this.errors).length > 0) {
                return false; // Validation failed
            }

            return true; // Validation passed
        },
        submit(){
            if(!this.validateForm())
            {
                return;
            }
            axios.post('/returned-products-supplier/submit', this.dataValues)
                .then(response => {
                    this.data = response.data.data;
                        Swal.fire({
                            title: "Request Approved!",
                            icon: 'success',
                            timer: 3000
                        });
                        this.getData();
                        $('#' + this.modalIdReview).modal('hide');
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
        completeWindow()
        {
            window.location.href = '/returned-products-supplier-completed';
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        viewProduct(props){
            this.dataValues = props.data;
            $('#' + this.modalId).modal('show');
        },
        reviewApprove() {
            Swal.fire({
                title: 'Have you already received the returned product and checked it?',
                text: '',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#' + this.modalId).modal('hide');
                    $('#' + this.modalIdReview).modal('show');
                }
            });
        },
        filterData() {
            const searchData = {
                serialNumber: this.serialNumber,
                brand: this.selectedBrand,
                tool: this.selectedTool,
            };

            axios.post('/returned-products-supplier/filterData', searchData)
                .then(response => {
                    this.data = response.data.data;
                    this.data.forEach(item => {
                        item.created_at = new Date(item.created_at).toLocaleString(); // Format to the user's locale
                    })
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
        },
        mounted() {
            this.getData();
        }
        


}

</script>
