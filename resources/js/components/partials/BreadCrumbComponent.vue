<template>
    <div class="pb-1">
        <div class="card">
            <div class="title-design">{{ tab_title }}</div>
        </div>
    </div>
</template>

<style scoped>
    .title-design{
        padding: 25px ;
        color: #2D2D2D;
        font-size: 20px;
        font-style: normal;
        font-weight: 600;
        background: #fff;
        line-height: normal;
    }
    

</style>

<script>
export default{
    props:{
            tab_title: String,
        },
    data(){
            return{
                
            }
        },
        components: {
            
        },
        methods:{

        },
        mounted() {

        }
    
}
</script>