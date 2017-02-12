<template>
	<div class="kickstarter-fieldTypeSelector">
		<multiselect :value="value"
								 placeholder="Choose a type"
								 :custom-label="typeLabel"
								 :options="typeOptions"
								 :allow-empty="false"
								 deselect-label=""
								 :searchable="true"
								 v-on:input="updateValue($event)"></multiselect>
	</div>
</template>

<script>
    export default{
        name: 'field-type-widget',
        props: ['field', 'value'],
        methods: {
            typeLabel: function (name) {
                return name.replace('FluidTYPO3\\Flux\\Form\\Field\\', '');
            },
            updateValue: function (value) {
                this.$emit('input', value);
            },
            updateValue: function (value) {
                this.$emit('input', value);
            }
        },
        data(){
            return {
                typeOptions: [
                    'FluidTYPO3\\Flux\\Form\\Field\\Input',
                    'FluidTYPO3\\Flux\\Form\\Field\\Select'
                ]
            }
        }
    }
</script>