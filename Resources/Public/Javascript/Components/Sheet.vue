<template>
    <div class="kickstarter-sheet group">
        <div class="group-header" v-on:click="isExpanded = !isExpanded">
            Sheet: {{data.label}}
        </div>
        <div class="group-rows" v-bind:class="{'group-rows--expanded': isExpanded }">
            <div class="group-row">
                <div class="group-row-label">
                    <label>Label:</label>
                    <small>This label will be shown in the Backend.</small>
                </div>
                <div class="group-row-content">
                    <input v-model="data.label" />
                </div>
            </div>
            <div class="group-row">
                <div class="group-row-label">
                    <label>Name:</label>
                    <small>Internal name.</small>
                </div>
                <div class="group-row-content">
                    <input v-model="data.name" />
                </div>
            </div>

            <div class="group-row">
                <div class="group-row-label">
                    <label>Fields:</label>
                </div>
                <div class="group-row-content">
                    <div class="kickstarter-fields">
                        <template v-for="field in data.children">
                            <field :data="field" />
                        </template>
                    </div>
                    <button v-on:click="addField">add Field</button>
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
                data: this.data,
                isExpanded: true
            }
        },
        components:{
            Field
        },
        methods: {
            addField: function(){
                this.data.fields.push({});
            }
        }
    }
</script>
