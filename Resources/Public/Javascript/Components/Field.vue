<template>
    <div class="kickstarter-field group">
        <div class="group-header">
            Field: {{field.label}}
        </div>
        <div class="group-rows">
            <div class="group-row">
                <div class="group-row-label">
                    <label>Label:</label>
                    <small>This label will be shown in the Backend.</small>
                </div>
                <div class="group-row-content">
                    <input v-model="label" />
                </div>
            </div>

            <div class="group-row">
                <div class="group-row-label">
                    <label>Default:</label>
                </div>
                <div class="group-row-content">
                    <input v-model="field.default" />
                </div>
            </div>

            <div class="group-row">
                <div class="group-row-label">
                    <label>Placeholder:</label>
                </div>
                <div class="group-row-content">
                    <input v-model="field.placeholder" />
                </div>
            </div>

            <div class="group-row">
                <div class="group-row-label">
                    <label>Type:</label>
                </div>
                <div class="group-row-content">
                    <select v-model="field.type">
                        <option v-for="option in typeOptions" v-bind:value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
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
    import Wizard from './Wizard.vue'

    export default{
        name: 'field',
        props: ['data'],
        data(){
            return {
                field: this.data,
                typeOptions: [
                    {
                        label: 'Foo',
                        value: 'Foo'
                    },
                    {
                        label: 'Input',
                        value: 'Input'
                    }
                ]
            }
        },
        components:{
            Wizard
        }
    }
</script>
