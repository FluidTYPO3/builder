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
                    'FluidTYPO3\\Flux\\Form\\Field\\None',
                    'FluidTYPO3\\Flux\\Form\\Field\\Input',
                    'FluidTYPO3\\Flux\\Form\\Field\\Text',
                    'FluidTYPO3\\Flux\\Form\\Field\\Radio',
                    'FluidTYPO3\\Flux\\Form\\Field\\Checkbox',
                    'FluidTYPO3\\Flux\\Form\\Field\\Select',
                    'FluidTYPO3\\Flux\\Form\\Field\\DateTime',
                    'FluidTYPO3\\Flux\\Form\\Field\\UserFunction',
                    'FluidTYPO3\\Flux\\Form\\Field\\File',
                    'FluidTYPO3\\Flux\\Form\\Field\\Inline',
                    'FluidTYPO3\\Flux\\Form\\Field\\Inline\\Fal',
                    'FluidTYPO3\\Flux\\Form\\Field\\Relation',
                    'FluidTYPO3\\Flux\\Form\\Field\\MultiRelation',
                    'FluidTYPO3\\Flux\\Form\\Field\\Passthrough',
                    'FluidTYPO3\\Flux\\Form\\Field\\Tree'
                ]
            }
        }
    }
</script>
