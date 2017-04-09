<template>
	<div class="kickstarter-fieldTypeSelector">
		<multiselect v-model="field.type"
								 placeholder="Choose a type"
								 :custom-label="typeLabel"
								 :options="typeOptions"
								 :allow-empty="false"
								 deselect-label=""
								 :searchable="true"></multiselect>
	</div>
</template>

<script>
    export default{
        name: 'field-type-selector',
        props: ['field'],
        methods: {
            typeLabel: function (name) {
                return name.replace('FluidTYPO3\\Flux\\Form\\Field\\', '');
            }
        },
        data(){
            return {
                typeOptions: [
                    'FluidTYPO3\\Flux\\Form\\Field\\None',
                    'FluidTYPO3\\Flux\\Form\\Field\\Input',
                    'FluidTYPO3\\Flux\\Form\\Field\\Text',
                    'FluidTYPO3\\Flux\\Form\\Field\\File',
                    'FluidTYPO3\\Flux\\Form\\Field\\Radio',
                    'FluidTYPO3\\Flux\\Form\\Field\\Checkbox',
                    'FluidTYPO3\\Flux\\Form\\Field\\DateTime',
                    'FluidTYPO3\\Flux\\Form\\Field\\Select'
                ]
            }
        }
    }
</script>
