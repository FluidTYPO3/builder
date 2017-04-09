<template>
	<div class="kickstarter-sheet group">
		<div class="group-header" v-on:click="isExpanded = !isExpanded">
			Sheet: {{sheet.label}}
		</div>
		<div class="group-rows" v-bind:class="{'group-rows--expanded': isExpanded }">
			<div class="group-row">
				<div class="group-row-label">
					<label>Label:</label>
					<small>This label will be shown in the Backend.</small>
				</div>
				<div class="group-row-content">
					<input v-model="sheet.label"/>
				</div>
			</div>
			<div class="group-row">
				<div class="group-row-label">
					<label>Name:</label>
					<small>Internal name.</small>
				</div>
				<div class="group-row-content">
					<input v-model="sheet.name"/>
				</div>
			</div>

			<div class="group-row">
				<div class="group-row-label">
					<label>Fields:</label>
				</div>
				<div class="group-row-content">
					<div class="kickstarter-field">
						<template v-for="(field, index) in sheet.children">
							<field :data="field" :index="index" />
						</template>
					</div>
					<div v-on:click="addField" class="btn btn-default">add Field</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
    import Field from './Field.vue'

    export default{
        name: 'sheet',
        props: ['data'],
        data(){
            return {
                sheet: this.data,
                isExpanded: true
            }
        },
        components: {
            Field
        },
        methods: {
            addField: function () {
                this.sheet.children.push({
                    type: 'FluidTYPO3\\Flux\\Form\\Field\\Input',
                    name: 'newfield' + this.sheet.children.length.toString()
                });
            }
        }
    }
</script>
