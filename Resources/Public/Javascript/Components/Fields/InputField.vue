<template>
	<div class="kickstarter-inputField group">
		<div class="group-header">
			Input: {{field.name}}
		</div>
		<div class="group-rows">
			<div class="group-row">
				<div class="group-row-label">
					<label>Name:</label>
				</div>
				<div class="group-row-content">
					<input v-model="field.name" />
				</div>
			</div>

			<div class="group-row">
				<div class="group-row-label">
					<label>Type:</label>
				</div>
				<div class="group-row-content">
					<field-type-selector :field="field">
				</div>
			</div>

			<div class="group-row">
				<div class="group-row-label">
					<label>Wizards:</label>
				</div>
				<div class="group-row-content">
					<div class="kickstarter-wizards">
						<template v-for="wizard in field.wizards">
							<wizard :data="wizard" />
						</template>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
    import FieldTypeSelector from '../FieldTypeSelector.vue'

    export default{
        name: 'InputField',
        props: ['data'],
        data(){
            return {
                field: this.data,
            }
        },
        components: {
        	FieldTypeSelector
        }
    }
</script>
