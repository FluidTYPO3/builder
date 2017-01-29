<template>
    <div class="kickstarter-wizard">
        <h3>Wizard</h3>

        <div class="field">
            <label>Type:</label>
            <input v-model="field.type" />
        </div>
    </div>
</template>

<style>
    .kickstarter-wizard {
        border: 1px solid #dedede;
        background: white;
        margin: 40px 20px;
    }
</style>

<script>
    export default{
        name: 'wizard',
        props: ['data'],
        data(){
            return {
                field: this.data
            }
        }
    }
</script>
