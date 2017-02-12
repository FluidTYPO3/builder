<template>
	<input ref="input"
				 v-bind:value="value"
				 v-on:input="updateValue($event.target.value)" />
</template>

<script>
    export default{
        name: 'string-widget',
        props: ['value'],
        methods: {
            updateValue: function (value) {
                this.$emit('input', value);
            }
        }
    }
</script>