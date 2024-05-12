<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Completed Orders"></BreadCrumbComponent>

        <div class="row mb-3">
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
                    btnName="Add Unit"
                    :addButton="false"
                    :option1Switch="false"
                    :option2Switch="false"
                    :option3Switch="true"
                    @optionalClicked="viewClicked" 
                    option3Name=""
                    option3Icon="fa fa-eye mr-2"
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
                            Serial Numbers: <b>{{ this.dataValues.serial_numbers }}</b>
                        </p>
                        <p>
                            With a total of: <b>₱{{ this.dataValues.total_price }}</b>
                        </p>
                        <p>
                            This Product is ordered by: <b>{{ this.dataValues.user_name }}</b>
                        </p>
                        <p>
                            This Product is Delivered at: <b>{{ this.dataValues.location }}</b>
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

export default{

    data(){
        return{
                data : [],
                selectedBrand: '',
                selectedTool: '',
                columns : ['brand_name', 'tool_name', 'completed_at' ,'action'],
                errors: [],
                options : {
                    headings : {
                        brand_name : 'Brand',
                        tool_name : 'Tool',
                        completed_at : 'Completed At',
                        action : 'Action',
                    },
                    filterable: false,
                    sortable: []
                },
                dataValues: {
                    name: '',
                },
                modalId : 'modal-unit',
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
    methods: {
        getData() {
            axios.get('/completed-order-user/completedOrderUserShow').then(response => {
                this.data = response.data.data;
            })
        },
        refresh() {
            window.location.reload();
        },
        viewClicked(props){
            this.dataValues = props.data;
            $('#' + this.modalId).modal('show');
        },
        filterData() {
            const searchData = {
                brand: this.selectedBrand,
                tool: this.selectedTool,
            };

            axios.post('/completed-order-user/completedFilterData', searchData)
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
    },
        mounted() {
            this.getData();
        }
        


}

</script>
