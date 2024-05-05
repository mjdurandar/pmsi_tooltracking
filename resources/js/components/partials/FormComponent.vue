<style> 
    .table-responsive{
        border-radius: 10px;
        border: 0.5px solid #CDCDCD;
        background: #FFF;
    }
    .table-striped tbody tr:nth-of-type(odd){
        background-color: #fff !important;
    }
    tr{
        color: #2d2d2d !important;
    }
    thead, td{
        text-align: center !important;
        vertical-align: middle !important;
        color: #6c757d;
    }

    .btn-position{
        text-align: right;
        padding-bottom: 10px;
    }
</style>

<template>
    <div>
        <slot name="filter-control"></slot>
        <div>
            <div class="d-flex justify-content-end">
                <div class="btn-position" v-if="addButton">
                    <button :class="addButtonColor" @click="addClicked(props)">{{ btnName }}</button>
                </div>
                <div class="ml-2 btn-position" v-if="otherButton">
                    <button :class="buttonOptionColor" @click="otherClicked(props)">{{ otherBtnName }}</button>
                </div>
            </div>
            <v-client-table :data="data" :columns="columns" :options="options" v-if="tableSwitch">
                <template #action="props">
                    <div class="text-center m-auto">
                        <button class="btn" @click="editClicked(props)" :style="option1Color" v-if="option1Switch">
                            <div class="d-flex">
                                <div>
                                    <i :class="option1Icon" style="padding-left: 10px;" aria-hidden="true"></i>
                                </div>
                                <div class="text-weight" style="padding-left: 5px;">
                                    {{option1Name}}
                                </div>
                            </div>
                        </button>
                        <button class="btn" @click="deleteClicked(props)" :style="option2Color" v-if="option2Switch">
                            <div class="d-flex">
                                <div>
                                    <i :class="option2Icon" style="padding-left: 10px;" aria-hidden="true"></i>
                                </div>
                                <div class="text-weight" style="padding-left: 5px;">
                                    {{option2Name}}
                                </div>
                            </div>
                        </button>
                        <button class="btn" @click="optionalClicked(props)" :style="option3Color" v-if="option3Switch">
                            <div class="d-flex">
                                <div>
                                    <i :class="option3Icon" style="padding-left: 10px;" aria-hidden="true"></i>
                                </div>
                                <div class="text-weight" style="padding-left: 5px;">
                                    {{option3Name}}
                                </div>
                            </div>
                        </button>
                    </div>
                </template>
            </v-client-table>    
        </div>
            
    </div>
</template>

<script>

        export default{
        props:{
            data: {
                type: Array,
                default : ()=>[]
            },
            columns: {
                type: Array,
                default : ()=>[]
            },
            options: {
                type: Object,
                default : ()=>{
                    return {message:'Hello'}
                }
            },
            //TABLE Switch
            tableSwitch: {
                type: Boolean,
                default: true
            },
            //OPTION 1 NAME ICON AND SWICTH
            option1Name:{
                type: String,
                default : "Edit"
            },
            option1Icon: {
                type: String,
                default: "fa fa-edit mr-2"
            },
            option1Color: {
                type: String,
                default: "color: #034F89"
            },
            option1Switch: {
                type: Boolean,
                default: true
            },
            //OPTION 2 NAME ICON AND SWICTH
            option2Name:{
                type: String,
                default : "Delete"
            },
            option2Icon: {
                type: String,
                default: "fa fa-trash mr-2"
            },
            option2Color: {
                type: String,
                default: "color: red"
            },
            option2Switch: {
                type: Boolean,
                default: true
            },
            //OPTION 3 NAME ICON AND SWICTH
             option3Name:{
                type: String,
                default : "Purchased/Borrowed"
            },
            option3Icon: {
                type: String,
                default: "fa-solid fa-money-bill mr-2" 
            },
            option3Color: {
                type: String,
                default: "color: #168118"
            },
            option3Switch: {
                type: Boolean,
                default: false
            },
            btnName: {
                type: String,
                default: "Add"
            },
            addButton: {
                type: Boolean,
                default: true
            },
            otherButton: {
                type: Boolean,
                default: false
            },
            otherBtnName: {
                type: String,
                default: "Other"
            },
            addButtonColor: {
                type: String,
                default: "btn btn-primary"
            },
            buttonOptionColor: {
                type: String,
                default: "btn btn-danger"
            }
        },
        data(){ 
            return {

            }
            
        },
        methods: {
            editClicked(props){
                this.$emit('editClicked', { data: props.row, index: props.index});
            },
            deleteClicked(props){
                this.$emit('deleteClicked', { data: props.row, index: props.index});
            },
            optionalClicked(props){
                this.$emit('optionalClicked', { data: props.row, index: props.index});
            },
            addClicked(){
                this.$emit('addClicked');
            },
            otherClicked(){
                this.$emit('otherClicked');
            }
        },
    };
</script>