<template>
    <div class="kickstart-form">
        <h3>Form</h3>
        <div class="field">
            <label>Label:</label>
            <input v-model="label" />
        </div>

        <template v-for="sheet in sheets">
            <sheet :data="sheet" />
        </template>
    </div>
</template>

<style>
    .kickstart-form {
        border: 1px solid #dedede;
        background: white;
        margin: 40px 20px;
    }
</style>

<script>
    import Sheet from './Sheet.vue'

    export default{
        name: 'kickstarter-form',
        props: ['json'],
        data() {
            return JSON.parse(this.json)
        },
        components:{
            Sheet
        }
    }
</script>
