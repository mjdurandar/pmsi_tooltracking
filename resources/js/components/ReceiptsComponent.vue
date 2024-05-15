<template>
    <div class="p-3">
        <BreadCrumbComponent tab_title="Receipts"></BreadCrumbComponent>
        <div class="row mb-3">
            <div class="col-lg-2">
                <input type="text" class="form-control" placeholder="Search Receipt Number" v-model="receipt_number">
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
                datas : [],
                adminLocation: [],
                receipt_number: '',
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
                this.adminLocation = response.data.adminLocation;
            })
        },
        refresh() {
            window.location.reload();
        },
        printReceipt(props) {
            // Parse the entries JSON string to an array
            const entries = JSON.parse(props.data.entries);
            // Calculate subtotal by summing up the prices of all items
            // const subtotal = entries.reduce((acc, entry) => acc + (parseFloat(entry.quantity) * parseFloat(entry.price)), 0);
            const firstEntryPrice = entries[0].price;
            // Calculate VAT amount (12% of the total amount)
            const vatRate = 0.12;
            const vat = firstEntryPrice * vatRate;
            const subtotal = firstEntryPrice - vat;

            // Calculate grand total by adding VAT to subtotal
            const grandTotal = firstEntryPrice + vat;

            // Open a new window for printing
            const printWindow = window.open('', '_blank');

            // Pass data to print template
            printWindow.document.write(`
                <html>
                <head>
                    <title>Receipt</title>
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
                    <h1 style="text-align:center;">RECEIPT</h1>
                    <div class="padding">
                        <strong>DATE:</strong> ${props.data && props.data.order_date ? props.data.order_date : ''}
                    </div>
                    <div class="padding">
                        <strong>RECEIPT #:</strong> ${props.data && props.data.receipt_number ? props.data.receipt_number : ''}
                    </div>
                    <hr> 
                    <div class="padding">
                        <strong>ITEMS:</strong>
                        <li>
                            ${entries.map(entry => `${entry.brand_name} - ${entry.tool_name}`).join(' ')}
                        </li>
                    </div>
                    <div class="padding">
                        <strong>SERIAL NUMBERS:</strong>
                        <ul>
                            ${entries.map(entry => `<li>${entry.serial_numbers}</li>`).join('')}
                        </ul>
                    </div>
                    <div class="padding">
                        <strong>SUBTOTAL:</strong> ₱${subtotal}
                    </div>
                    <div class="padding">
                        <strong>VAT (${vatRate * 100}%):</strong> ₱${vat}
                    </div>
                    <div class="padding">
                        <strong>GRAND TOTAL:</strong> ₱${firstEntryPrice}
                    </div>
                    <hr> 
                    <div class="padding" style="text-align:center;">
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
        filterData() 
        {
            axios.post('/receipts/filterData', { receiptNumber: this.receipt_number })
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
