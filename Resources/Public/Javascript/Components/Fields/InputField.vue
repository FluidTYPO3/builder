<template>
	<div class="kickstarter-inputField group">
		<div class="group-header">
			Input: {{field.name}}
		</div>
		<div class="group-rows">
			<tabs>
				<tab label="General" selected="1">
					<group-row label="Name" description="lorem ipsum dolor set amet">
						<input v-model="field.name" />
					</group-row>

					<group-row label="Type">
						<field-type-selector :field="field">
					</group-row>

					<group-row label="Placeholder">
						<input v-model="field.placeholder" />
					</group-row>
				</tab>
				<tab label="Constraints">
					<group-row label="Max Characters">
						<input v-model="field.maxCharacters" />
					</group-row>

					<group-row label="Minimum">
						<input v-model="field.minimum" />
					</group-row>

					<group-row label="Maximum">
						<input v-model="field.maximum" />
					</group-row>

					<group-row label="Eval" description="FlexForm-type validation configuration for this input">
						<input v-model="field.eval" />
					</group-row>
				</tab>
			</tabs>

			<!--<div class="group-row">-->
				<!--<div class="group-row-label">-->
					<!--<label>Wizards:</label>-->
				<!--</div>-->
				<!--<div class="group-row-content">-->
					<!--<div class="kickstarter-wizards">-->
						<!--<template v-for="wizard in field.wizards">-->
							<!--<wizard :data="wizard" />-->
						<!--</template>-->
					<!--</div>-->
				<!--</div>-->
			<!--</div>-->
		</div>
	</div>
</template>

<script>
    export default{
        name: 'InputField',
        props: ['data'],
        data(){
            return {
                field: this.data,
            }
        }
    }
</script>
