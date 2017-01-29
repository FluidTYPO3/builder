<template>
	<div class="kickstarter-fieldTypeSelector">
		<select v-model="field.type">
			<option v-for="option in typeOptions" v-bind:value="option.value">
				{{ option.label }}
			</option>
		</select>
	</div>
</template>

<script>
    export default{
        name: 'field-type-selector',
        props: ['field'],
        data(){
            return {
                typeOptions: [
                    {
                        label: '',
                        value: ''
                    },
                    {
                        label: 'Input',
                        value: 'FluidTYPO3\\Flux\\Form\\Field\\Input'
                    },
                    {
                        label: 'Select',
                        value: 'FluidTYPO3\\Flux\\Form\\Field\\Select'
                    }
                ]
            }
        }
    }
</script>