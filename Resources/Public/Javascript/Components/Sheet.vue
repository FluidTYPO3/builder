<template>
    <div class="kickstarter-sheet">
        <h3>Sheet</h3>
        <div class="field">
            <label>Label:</label>
            <input v-model="label" />
        </div>

        <div class="field">
            <label>Description:</label>
            <textarea v-model="description" />
        </div>

        <div class="field">
            <label>Short Description:</label>
            <input v-model="shortDescription" />
        </div>

        <template v-for="field in fields">
            <field :data="field" />
        </template>
    </div>
</template>

<style>
    .kickstarter-sheet {
        border: 1px solid #dedede;
        background: white;
        margin: 40px 20px;
    }
</style>

<script>
    import Field from './Field.vue'

    export default{
        name: 'sheet',
        props: ['data'],
        data(){
            return this.data
        },
        components:{
            Field
        }
    }
</script>
