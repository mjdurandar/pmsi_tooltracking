<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Buying History"></BreadCrumbComponent>
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
                    :addButton="false"
                    :option2Switch="true"
                    option2Color = "color: #000000"
                    option2Icon="fa fa-print mr-2"
                    option2Name="Print Receipt"
                    option1Color = "color: #089000"
                    option1Icon="fa fa-eye mr-2"
                    option1Name="View"
                    @editClicked="editClicked"
                    @deleteClicked="printReceipt"
                >
                </FormComponent>
            </div>
        </div>

        <ModalComponent :id="modalId" :title="modalTitle" :size="modalSize" :position="modalPosition">
            <template #modalHeader>
                <div class="m-auto">
                    <h4>View History</h4>
                </div>
            </template>
            <template #modalBody>
                <div class="col-12 pb-2">
                    <label for="">Brand</label>
                        <input class="form-control" v-model="dataValues.brand_name" disabled>
                </div> 
                <div class="col-12 pb-2">
                    <label for="">Tools</label>
                        <input class="form-control" v-model="dataValues.tool_name" disabled>
                </div> 
                <div class="col-12 pb-2">
                    <label for="">Product Code</label>
                        <input class="form-control" v-model="dataValues.product_code" disabled>
                </div> 
                <div class="col-12 pb-2">
                    <label for="">Purchased At</label>
                        <input class="form-control" v-model="dataValues.created_at" disabled>
                </div> 
                <div class="col-12 pb-2">
                    <label for="">Price</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="dataValues.price" disabled>
                        </div>
                </div> 
                
            </template>
            <template #modalFooter>
                <div class="text-right">
                    <button class="btn btn-dark" v-on:click="closeModal">Close</button>
                </div>
            </template>
        </ModalComponent>

    </div>
</template>

<script>    
import BreadCrumbComponent from "./partials/BreadCrumbComponent.vue";   
import FormComponent from "./partials/FormComponent.vue";   
import ModalComponent from "./partials/ModalComponent.vue";
import Swal from 'sweetalert2'
import axios from 'axios';

export default{

    data(){
        return{
                data : [],
                errors: [],
                selectedBrand : '',
                selectedTool : '',
                columns : ['brand_name', 'tool_name', 'created_at','action'],
                options : {
                    headings : {
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        created_at : 'Purchased At',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-buyinghistory',
                modalTitle : 'Buying History',
                modalPosition: 'modal-dialog-centered',
                modalSize : 'modal-md',
        }
    },
    components: {
        FormComponent,
        ModalComponent,
        BreadCrumbComponent
    },
    methods: {
        addClicked(props){
            $('#' + this.modalId).modal('show');
            this.clearInputs();
        },
        closeModal(){
            $('#' + this.modalId).modal('hide');
        },
        getData() {
            axios.get('/buyinghistory/show').then(response => {
                this.data = response.data.data;
                this.data.forEach(item => {
                    item.created_at = new Date(item.created_at).toLocaleString(); // Format to the user's locale
                })
            })
        },
        filterData() {
            const searchData = {
                brand: this.selectedBrand,
                tool: this.selectedTool
            };

            axios.post('/buyinghistory/filterData', searchData)
                .then(response => {
                    this.data = response.data.data;
                    this.data.forEach(item => {
                        item.created_at = new Date(item.created_at).toLocaleString(); // Format to the user's locale
                    })
                    if (this.data.length === 0) {
                        Swal.fire({
                            title: "No Data available!",
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
        printReceipt(props) {
                // Create a new window
                let printWindow = window.open('', '_blank');

                // Write the details to the new window
                printWindow.document.write('<html><head><title>Print Receipt</title></head><body>');
                printWindow.document.write('<h1>Receipt</h1>');
                printWindow.document.write('<p>Product: ' + props.data.brand_name + ' - ' + props.data.tool_name + '</p>');
                printWindow.document.write('<p>Price: ' + props.data.price + '</p>');
                printWindow.document.write('<p>Date: ' + props.data.created_at + '</p>');
                printWindow.document.write('</body></html>');

                // Call print on the new window
                printWindow.print();
            },
        editClicked(props) {
            this.dataValues = props.data;
            this.modalTitle= 'View Data';
            $('#' + this.modalId).modal('show');
        },
        },
        mounted() {
            this.getData();
        }
}

</script>
