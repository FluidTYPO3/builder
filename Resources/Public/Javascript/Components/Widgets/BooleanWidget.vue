<template>
	<input type="checkbox"
				 ref="input"
				 class="checkbox"
				 v-model="checked"
				 v-on:change="updateValue($event.target.checked)"/>
</template>

<script>
    export default{
        name: 'boolean-widget',
        props: ['value'],
        data: function () {
            return {
                checked: this.value
            }
        },
        methods: {
            updateValue: function (checked) {
                this.$emit('input', checked);
            }
        }
    }
</script>
