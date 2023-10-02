<template>
    <div class="modal" :id="id">
        <div :class="'modal-dialog ' + position + ' ' + size">
            <div class="modal-content">
                <div class="modal-header">
                    <slot name="modalHeader"></slot>
                </div>
                <div class="modal-body">
                    <slot name="modalBody"></slot>
                </div>
                <div class="modal-footer text">
                    <slot name="modalFooter"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {

    props: {
        id : String,
        title : String,
        size : String,
        position : String,
    }   
}
</script>
