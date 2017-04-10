<template>
	<input ref="input"
				 v-bind:value="value"
				 class="form-control"
				 v-on:input="updateValue($event.target.value)" />
</template>

<script>
    export default{
        name: 'int-widget',
        props: ['value'],
        methods: {
            updateValue: function (value) {
                this.$emit('input', value);
            }
        }
    }
</script>
