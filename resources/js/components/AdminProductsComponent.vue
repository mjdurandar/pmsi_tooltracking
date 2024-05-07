<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Products"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    :addButton="false"
                    btnName="Completed Order"
                    :option1Switch="true"
                    :option2Switch="true"
                    :option3Switch="true"
                    option2Color="color: #039487;"
                    option2Name=""
                    option2Icon="fa-solid fa-check mr-2"
                    addButtonColor="btn btn-success"
                    option1Name=""
                    option1Color="#FF0000"
                    option3Name=""
                    option1Icon="fa-solid fa-right-left mr-2" 
                    option3Icon="fa fa-eye mr-2"
                    @editClicked="returnProduct"
                    @optionalClicked="viewClicked"
                    :otherButton=true
                    otherBtnName="Returned Products"
                    @otherClicked="returnedProductPage"
                    @deleteClicked="approvedProduct"
                >
                </FormComponent>
            </div>
        </div>

        <!-- VIEW CLICKED -->
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
                            Serial Numbers: <b>{{ this.dataValues.serial_numbers }}</b>
                        </p>
                        <p>
                            With a total of: <b>₱{{ this.dataValues.total_price }}</b>
                        </p>
                        <p>
                            Delivered At: <b>{{ this.dataValues.location }}</b>
                        </p>
                    </div>
                </div>
            </template>
            <template #modalFooter>
            </template>
        </ModalComponent>

        <!-- RETURNED PRODUCTS -->
        <ModalComponent :id="modalIdReturn" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h3>Return a Product</h3>
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
                        <div class="col-12">
                            <label for="serialNumber">Please select Serial Number(s) to return:</label>
                            <div class="form-check" v-for="(serialNumber, index) in this.dataValues.serial_numbers" :key="index">
                                <input class="form-check-input" type="checkbox" :id="'serialNumber_' + index" :value="serialNumber" @change="updateCheckedValues($event.target.value)">
                                <label class="form-check-label" :for="'serialNumber_' + index">{{ serialNumber }}</label>
                            </div>
                            <div class="text-danger" v-if="errors.serial_numbers">{{ errors.serial_numbers[0] }}</div>
                        </div>
                        <div class="col-12 mt-3">
                            <textarea class="form-control" rows="3" v-model="dataValues.reason" placeholder="Please describe the reason why you are returning the product..."></textarea>
                            <div class="text-danger" v-if="errors.reason">{{ errors.reason[0] }}</div>
                        </div>
                    </div>
                </div>
            </template>
            <template #modalFooter>
                <button type="button" class="btn btn-primary" @click="submitReturn">Submit</button>
            </template>
        </ModalComponent>

        <!-- APPROVED MODAL -->
        <ModalComponent :id="modalIdApprove" :title="modalTitle" :size="modalSize" :position="modalPosition">
        <template #modalHeader>
            <div class="m-auto">
                <h3>Approve a Product</h3>
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
                        Serial Numbers: <b>{{ this.dataValues.serial_numbers }}</b>
                    </p>
                    <p>
                        <b>Before approving, please ensure:</b>
                    </p>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check1" v-model="isChecked1">
                        <label class="form-check-label" for="check1">The Product has no damage.</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check2" v-model="isChecked2">
                        <label class="form-check-label" for="check2">The Product is correct.</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check3" v-model="isChecked3">
                        <label class="form-check-label" for="check3">The Product is working.</label>
                    </div>
                </div>
            </div>
        </template>
        <template #modalFooter>
            <button type="button" class="btn btn-success" @click="submitApprove" :disabled="!allCheckboxesChecked">Approve</button>
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
                checkedSerialNumbers: [],
                columns : ['brand_name', 'tool_name', 'supplier_name', 'stocks' ,'action'],
                errors: [],
                isChecked1: false,
                isChecked2: false,
                isChecked3: false,
                serialNumbers: [], 
                options : {
                    headings : {
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        supplier_name : 'Supplier',
                        stocks : 'Stocks',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-unit',
                modalIdApprove : 'modal-approve',
                modalIdReturn : 'modal-return',
                modalTitle : 'Unit',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-lg',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent,
    },
    computed: {
        allCheckboxesChecked() {
            return this.isChecked1 && this.isChecked2 && this.isChecked3;
        }
    },
    methods: {
        addClicked(props){
            $('#' + this.modalId).modal('show');
            this.clearInputs();
        },
        getData() {
            axios.get('/admin-products/show').then(response => {
                this.data = response.data.data;
            })
        },
        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        viewClicked(props) {
            this.dataValues = props.data;
            this.modalTitle= 'Return Product';
            $('#' + this.modalId).modal('show');
        },
        returnProduct(props) {
            this.dataValues = props.data;
            this.modalTitle= 'Return Product';
            $('#' + this.modalIdReturn).modal('show');
        },
        updateCheckedValues(serialNumber) {
            if (this.checkedSerialNumbers.includes(serialNumber)) {
                this.checkedSerialNumbers = this.checkedSerialNumbers.filter(num => num !== serialNumber);
            } else {
                this.checkedSerialNumbers.push(serialNumber);
            }
        },
        approvedProduct(props) {
            this.dataValues = props.data;
            this.modalTitle= 'Approved Product';
            $('#' + this.modalIdApprove).modal('show');
        },
        returnedProductPage() {
            window.location.href = '/admin-returned-product';
        },
        submitApprove(props){
            console.log(props);
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to return this Product once approved. Also once approved you will see the Product in Tools and Equipment Page.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, approved it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with deletion
                    axios.post('/admin-products/approvedProduct' , this.dataValues).then(response => {
                        if(response.status === 200) {
                            Swal.fire({
                                title: "Success",
                                text: response.data.message,
                                icon: 'success',
                                timer: 3000
                            });
                        }
                        $('#' + this.modalIdApprove).modal('hide');
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
        validateForm() {
            this.errors = [];

            if (this.checkedSerialNumbers.length === 0) {
                this.errors.serial_numbers = ['Please select at least one serial number to return'];
            }
 
            if (!this.dataValues.reason) {
                this.errors.reason = ['Reason is required'];
            }

            // Check if any errors are present
            if (Object.keys(this.errors).length > 0) {
                return false; // Validation failed
            }

            return true; // Validation passed
        },
        submitReturn() {
                if (!this.validateForm()) {
                    return;
                }

                const requestData = {
                    serial_numbers: this.checkedSerialNumbers, // Include the selected serial numbers
                    reason: this.dataValues.reason, // Include the reason for return
                    dataValues: this.dataValues,
                    // Include other form data as needed
                };

                axios.post('/admin-products/returnProduct', requestData).then(response => {
                    if (response.data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.data.message,
                        });
                        $('#' + this.modalIdReturn).modal('hide');
                        this.getData();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.data.message,
                        });
                    }
                });
            },
        },
        mounted() {
            this.getData();
        }
        


}

</script>
