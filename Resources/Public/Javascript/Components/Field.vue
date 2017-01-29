<template>
    <div class="kickstarter-field">
        <h3>Field</h3>
        <div class="field">
            <label>Label:</label>
            <input v-model="field.label" />
        </div>

        <div class="field">
            <label>Default:</label>
            <input v-model="field.default" />
        </div>

        <div class="field">
            <label>Placeholder:</label>
            <input v-model="field.placeholder" />
        </div>

        <div class="field">
            <label>Type:</label>
            <input v-model="field.type" />
        </div>

        <template v-for="wizard in field.wizards">
            <wizard :data="wizard" />
        </template>
    </div>
</template>

<style>
    .kickstarter-field {
        border: 1px solid #dedede;
        background: white;
        margin: 40px 20px;
    }
</style>

<script>
    import Wizard from './Wizard.vue'

    export default{
        name: 'field',
        props: ['data'],
        data(){
            return {
                field: this.data
            }
        },
        components:{
            Wizard
        }
    }
</script>
