<template>
	<div class="kickstarter-sheet group">

        <div class="form-group">
            <attribute v-for="attribute in sheet.attributes" :attribute="attribute" v-model="sheet[attribute.name]" />
        </div>

        <attribute v-for="attribute in attributes" :attribute="attribute" v-model="attributes[attribute.name]" v-bind:value="data[attribute.name]" />

        <table class="table table-bordered table-striped table-hover">
            <tbody>
                <template v-for="(field, index) in sheet.children">
                    <field :data="field" :index="index" />
                </template>
            </tbody>
        </table>

        <div v-on:click="addField" class="btn btn-default">add Field</div>

	</div>
</template>

<script>
    import Field from './Field.vue'

    export default{
        name: 'sheet',
        props: ['data'],
        data() {
            return {
                sheet: this.data
            }
        },
        components: {
            Field
        },
        computed: {
            attributes: function () {
                return fieldTypes['FluidTYPO3\\Flux\\Form\\Container\\Sheet'].attributes;
            }
        },
        methods: {
            updateValue: function (value) {
                this.$emit('input', value);
            },
            addField: function () {
                this.sheet.children.push({
                    type: 'FluidTYPO3\\Flux\\Form\\Field\\Input',
                    name: 'newfield' + this.sheet.children.length.toString()
                });
            }
        }
    }
</script>
