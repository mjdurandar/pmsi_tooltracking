<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Receipts"></BreadCrumbComponent>
        <div class="card">
            <div class="card-body">
                <FormComponent 
                    :data="data"
                    :columns="columns"
                    :options="options"
                    :addButton="false"
                    :option1Switch="false"
                    :option2Switch="false"
                    :option3Switch="true"
                    option3Name="Print"
                    option3Icon="fa fa-print mr-2"
                    @optionalClicked="printReceipt"
                >
                </FormComponent>
            </div>
        </div>

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
                columns : ['receipt_number', 'order_date' ,'action'],
                errors: [],
                options : {
                    headings : {
                        receipt_number : 'Receipt Number',
                        order_date : 'Issued Date',
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
                modalSize : 'modal-md',
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
            axios.get('/receipts/show').then(response => {
                this.data = response.data.data;
            })
        },
        printReceipt() {
    // Open a new window for printing
    const printWindow = window.open('', '_blank');
    // Pass data to print template
    printWindow.document.write(`
        <html>
        <head>
            <title>Print Receipt</title>
            <style>
            body {
                font-family: Arial, sans-serif;
                text-align: center;
            }
            .receipt {
                display: inline-block;
                text-align: left;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }
            .padding {
                padding-bottom: 20px;
            }
            .margin {
                margin: 70px;
            }
            </style>
        </head>
        <body class="margin">
            <div class="receipt">
            <h1 style="text-align:center;">PMSI RECEIPT</h1>
            <div class="padding">
                <strong>COMPANY NAME:</strong> PROJECT MANAGEMENT STRATEGIES INTERNATIONAL Inc.
            </div>
            <div class="padding">
                <strong>ADDRESS:</strong> ${this.data.map(item => item.location).join('')}
            </div>
            <div class="padding">
                <strong>PHONE:</strong> ${this.data.map(item => item.contact_address).join('')}
            </div>
            <div class="padding">
                <strong>WEBSITE:</strong> https://pmsitooltracking.com/
            </div>
            <hr> 
            <div class="padding">
                <strong>DATE:</strong> ${this.data.map(item => item.order_date).join('')}
            </div>
            <div class="padding">
                <strong>RECEIPT #:</strong> ${this.data.map(item => item.receipt_number).join('')}
            </div>
            <hr> 
            <div class="padding">
                <strong>ITEMS:</strong>
                <ul>
                    ${this.data.map(item => `
                        <li>
                            ${item.brand_name} - ${item.tool_name} - ${item.serial_numbers.join(', ')}
                        </li>
                    `).join('')}
                </ul>
            </div>
            <div class="padding">
                <strong>TOTAL:</strong> ${this.data.map(item => item.total_price).join('')}
            </div>
            <hr> 
            <div class="padding">
                <strong>Thank you for your purchase!</strong>
            </div>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    // Print the content
    printWindow.print();
},

        clearInputs() {
            this.dataValues = {
                name: '',
            }
            this.errors = [];
        },
        },
        mounted() {
            this.getData();
        }
        


}

</script>
