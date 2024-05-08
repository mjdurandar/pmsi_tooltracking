<template>
    <div class="d-flex flex-column min-vh-100 justify-content-center align-items-center">
        <div class="text-center pt-5 flex-grow-1">
            <h1 class="pb-3">Welcome to PMSI</h1>
            <p class="text-center">PMSI is a comprehensive platform for selling and buying tools.</p>
            <p class="text-center mb-5">We connect tool sellers with buyers, providing a marketplace for all your tool trading needs.</p>
            <img class="logo-img" src="/images/pmsi_logo.png" alt="PMSI Logo">
            <div class="text-center reserved">
                <span>© {{ new Date().getFullYear() }} PMSI. All rights reserved.</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
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
