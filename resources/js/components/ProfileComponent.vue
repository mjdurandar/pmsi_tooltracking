<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Profile"></BreadCrumbComponent>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-3">
                        <label for="">Name</label>
                        <input type="text" class="form-control" v-model="data.name" :disabled="!isEditing">
                    </div>
                    <div class="col-lg-3">
                        <label for="">Email</label>
                        <input type="text" class="form-control" v-model="data.email" :disabled="!isEditing">
                    </div>
                    <div class="col-lg-3">
                        <label for="">Contact Number</label>
                        <input type="text" class="form-control" v-model="data.contact_address" :disabled="!isEditing">
                    </div>
                    <div class="col-lg-3">
                        <label for="">Contact Person</label>
                        <input type="text" class="form-control" v-model="data.contact_person" :disabled="!isEditing">
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-lg-6">
                        <label for="">Location</label>
                        <input type="text" class="form-control" v-model="data.location" :disabled="!isEditing">
                    </div>
                    <div class="col-lg-3">
                        <label for="">Balance</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">₱</span>
                            </div>
                            <input type="text" class="form-control" v-model="data.balance" disabled>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-lg-12">
                        <label for="">Company Description</label>
                        <textarea class="form-control" v-model="data.company_description" :disabled="!isEditing" style="height: 400px;"></textarea>
                    </div>
                </div>
                <div class="text-center mt-5">
                    <div>
                        <button class="btn btn-primary" @click="toggleEditMode()" v-if="!isEditing">Edit</button>
                        <button class="btn btn-success" @click="saveData()" v-if="isEditing">Save</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>    

import BreadCrumbComponent from "./partials/BreadCrumbComponent.vue";
import Swal from 'sweetalert2'
import axios from 'axios';

export default{

    data(){
        return{
                data : [],
                isEditing: false,
                originalData: {}
        }
    },
    components: {
        BreadCrumbComponent
    },
    methods: {

        toggleEditMode() {
            this.isEditing = !this.isEditing;
            if (this.isEditing) {
                this.data = Object.assign({}, this.originalData);
            } else {
                this.originalData = Object.assign({}, this.data);
            }
        },
        saveData() {
            this.isEditing = false;
            // Logic to save the edited data
            axios.post('/profile/store', this.data).then(response => {
                // Handle response if needed
                Swal.fire({
                    icon: 'success',
                    title: 'Data updated successfully!',
                    showConfirmButton: false,
                    timer: 1500
                });
            }).catch(error => {
                // Handle error if needed
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                });
            });
        },
        getData() {
            axios.get('/profile/show').then(response => {
                this.data = response.data.data;
                this.originalData = Object.assign({}, this.data);
            })
        },
       
        },
        mounted() {
            this.getData();
        }
        


}

</script>
