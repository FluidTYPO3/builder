<template>
	<div class="kickstarter-noneField">
	</div>
</template>

<script>
    export default{
        name: 'NoneField',
        props: ['data'],
        data(){
            return {
                field: this.data,
            }
        }
    }
</script>
