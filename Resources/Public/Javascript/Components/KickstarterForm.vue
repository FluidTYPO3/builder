<template>
    <div class="kickstarter-form">
        <h3>Form</h3>
        <div class="field">
            <label>Label:</label>
            <input v-model="form.label" />
        </div>

        <div class="kickstarter-sheets">
            <template v-for="sheet in form.children">
                <sheet :data="sheet" />
            </template>
        </div>
    </div>
</template>

<style>

    .group {
        border: #dddddd solid 1px;
        background: white;
    }

    .group-rows {
        border-top: #dddddd solid 1px;
    }

    .group-row {
        display: flex;
        border-bottom: #dddddd solid 1px;
    }

    .group-header {
        padding: 20px 10px;
        background: #ededed;
    }

    .group-row-label {
        background: #F9F9F9;
        width: 24%;
        padding: 15px 12px;
    }

    .group-row-label small {
        display: block;
    }

    .group-row-content {
        width: 76%;
        padding: 15px 12px;
    }

    .group-row-content input,
    .group-row-content textarea {
        width: 100%;
        padding: 3px 5px;
        resize: none;
        margin: 0;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        font-size: 14px;
        line-height: 1.4;
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
