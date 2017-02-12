<template>
	<div v-show="show" @keyup.esc="close">
		<div class="modal-container" transition="modal">
			<slot></slot>
		</div>
		<div class="modal-mask" @click="close"></div>
	</div>
</template>
<script>
    export default {
        name: 'modal',
        props: ['show', 'onClose'],
        methods: {
            close: function () {
                this.onClose();
            }
        },
        mounted: function () {
            document.addEventListener("keydown", (e) => {
                if (this.show && e.keyCode == 27) {
                    this.onClose();
                }
            });
        }
    }
</script>
