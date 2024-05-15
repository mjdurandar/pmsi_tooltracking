<template>
    <div class="d-flex flex-column min-vh-100 justify-content-center align-items-center">
        <!-- Content -->
        <div class="text-center pt-5 flex-grow-1">
            <h1 class="pb-3">Welcome to PMSI</h1>
            <p class="text-center">PMSI is a comprehensive platform for selling and buying tools.</p>
            <p class="text-center mb-5">We connect tool sellers with buyers, providing a marketplace for all your tool trading needs.</p>
            <img class="logo-img" src="/images/pmsi_logo.png" alt="PMSI Logo">
            <div class="text-center reserved">
                <span>© {{ new Date().getFullYear() }} PMSI. All rights reserved.</span>
            </div>
        </div>

        <!-- MODAL -->
        <div v-if="showModalSupplier" class="modal" @click="showModalSupplier = false">
            <div class="modal-content text-center" @click.stop>
                <div class="modal-header m-auto">
                    <i class="fa-sharp fa-solid fa-circle-exclamation"></i> <h3>Notifications</h3>
                </div>
                <div class="row mt-3">
                    <div class="col-12" v-if="notifications.length > 0">
                        <h4>You got a total of <b>{{ notifications.length }}</b> Order(s)</h4>
                        <p>Please go to Orders Tab...</p>
                    </div>
                    <div class="col-12" v-if="returnedProducts.length > 0">
                        <h4><b>{{ returnedProducts.length }}</b> of your Products has been requested for Return</h4>
                        <p>Please go to Returned Products Tab...</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showModalAdmin" class="modal" @click="showModalAdmin = false">
            <div class="modal-content text-center" @click.stop>
                <div class="modal-header m-auto">
                    <i class="fa-sharp fa-solid fa-circle-exclamation"></i> <h3>Notifications</h3>
                </div>
                <div class="row mt-3">
                    <div class="col-12" v-if="notifyAdminforOrders.length > 0">
                        <div >
                            <h4>You got a total of <b>{{ notifyAdminforOrders.length }}</b> order(s)</h4>
                            <p>Please go to Orders Tab...</p>
                        </div>
                    </div>
                    <div class="col-12" v-if="completedOrder.length > 0">
                        <div>
                            <h4><b>{{ completedOrder.length }}</b> of your Order(s) has been Delivered</h4>
                            <p>Please go to Products Tab...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showModalUser" class="modal" @click="showModalUser = false">
            <div class="modal-content text-center" @click.stop>
                <div class="modal-header m-auto">
                    <i class="fa-sharp fa-solid fa-circle-exclamation"></i> <h3>Notifications</h3>
                </div>
                <div class="row mt-3">
                    <div class="col-12" >
                        <div>
                            <h4><b></b> of your Order(s) has been Delivered</h4>
                            <p>Please go to Products Tab...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                
    </div>
  </template>
  
  <script>
    export default {
        props: {
            role: Number,
            notifications: {
                type: Array,
                default: () => []
            },
            completedOrder: {
                type: Array,
                default: () => []
            },
            returnedProducts: {
                type: Array,
                default: () => []
            },
            notifyAdminforOrders: {
                type: Array,
                default: () => []
            }
        },
        data(){
            return{
                    data : [],
                    notifs : '',
                    showModalSupplier: false,
                    showModalAdmin: false,
                    showModalUser: false,
            }
        },
        methods: {
        },
        mounted()
        {   
            if(this.role === 2 && this.notifications.length > 0 || this.returnedProducts.length > 0)
            {   
                this.showModalSupplier = true;
            }

            if(this.role === 1 && this.notifyAdminforOrders.length > 0 || this.completedOrder.length > 0)
            {
                this.showModalAdmin = true;
            }

            // if(this.role === 0 && this.notifications.length > 0 || this.completedOrder.length > 0)
            // {
            //     this.showModalUser = true;
            // }
            // if (this.notifications.length > 0) {
            //     // Iterate over each notification in the notifications array
            //     this.notifications.forEach(notification => {
            //         // Log the description of each notification
            //         console.log(notification.description);
            //     });
            // }
            console.log(this.notifyAdminforOrders);
        }

    }
  </script>
  
<style scoped>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        max-width: 500px;
        width: 100%;
    }

    .footer {
        width: 100%;
        height: 60px;
        line-height: 60px;
        background-color: #f18f4e;
    }

    .min-vh-100 {
        min-height: 100vh;
    }

    .flex-grow-1 {
        flex-grow: 1;
    }

    .logo-img {
        width: 200px; /* Adjust the width as needed */
    }

    .reserved {
        margin-top: 120px;
    }
</style>
  