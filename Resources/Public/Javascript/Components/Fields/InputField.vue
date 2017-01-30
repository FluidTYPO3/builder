<template>
	<div class="kickstarter-inputField group">
		<div class="group-header">
			Input: {{field.name}}
		</div>
		<div class="group-rows">
			<tabs>
				<tab label="General" selected="1">
					<group-row label="Name" description="Name of the attribute, FlexForm XML-valid tag name string">
						<input v-model="field.name" />
					</group-row>

					<group-row label="Label">
						<input v-model="field.label" />
						<small>
							Label for the attribute, can be LLL: value.
							Optional - if not specified, Flux tries to detect an LLL
							label named "flux.fluxFormId.fields.foobar" based on field name, in scope of extension rendering the
							Flux form.<br />
							If field is in an object, use "flux.fluxFormId.objects.objectname.foobar" where "foobar" is
							the name of the field.
						</small>
					</group-row>

					<group-row label="Type">
						<field-type-selector :field="field">
					</group-row>

					<group-row label="Required" description="If Checked, this attribute must be filled when editing the FCE">
						<input type="checkbox" :id="'checkbox' + id" v-model="field.required">
					</group-row>

					<group-row label="Default" description="Default value for this attribute">
						<input v-model="field.default" />
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
				<tab label="Other">
					<group-row label="Exclude">
						<input type="checkbox" v-model="field.exclude">
						<small>If Checked, this field becomes an "exclude field" (see TYPO3 documentation about this)</small>
					</group-row>

					<group-row label="Enabled">
						<input type="checkbox" v-model="field.enabled">
						<small>If Unchecked, disables the field in the FlexForm</small>
					</group-row>

					<group-row label="Transform">
						<input v-model="field.transform" />
						<small>Set this to transform your value to this type - integer, array (for csv values), float, DateTime,
							Vendor\\MyExt\\Domain\\Model\\Object or ObjectStorage with type hint. </small>
					</group-row>

					<group-row label="RequestUpdate">
						<input type="checkbox" v-model="field.requestUpdate">
						<small>If TRUE, the form is force-saved and reloaded when field value changes</small>
					</group-row>

					<group-row label="Display Condition">
						<input v-model="field.displayCond" />
						<small>
							Optional "Display Condition" (TCA style) for this particular field. See:
							<a href="https://docs.typo3.org/typo3cms/TCAReference/Reference/Columns/Index.html#displaycond">https://docs.typo3.org/typo3cms/TCAReference/Reference/Columns/Index.html#displaycond</a>
						</small>
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
                id: Math.random().toString(36).substr(2, 10)
            }
        }
    }
</script>
