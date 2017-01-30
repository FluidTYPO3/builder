<template>
    <div class="flux-template-form">
        <div class="field">
            <label>Label:</label>
            <input v-model="form.label" />
        </div>

        <input type="hidden" v-model="formJson" :name="formFieldName" />
        <input type="hidden" v-model="gridJson" :name="gridFieldName" />

        <div class="flux-template-sheets">
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
        padding: 10px 10px;
        background: #ededed;
    }

    .group-row-label {
        background: #F9F9F9;
        width: 24%;
        padding: 10px 10px;
    }

    .group-row-label small {
        display: block;
    }

    .group-row-content {
        width: 76%;
        padding: 10px 10px;
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

    .tabs-navigation {
        overflow: hidden;
    }

    .tabs-navigation-item {
        float: left;
        padding: 12px 8px;
    }

    .tabs-navigation-item--active {
        background: #ededed;
    }

    .flux-template-fields {
        margin: -11px -11px 0;
    }

    .sheet-addField {
        color: #292b2c;
        background-color: #fff;
        border-color: #ccc;
        display: inline-block;
        font-weight: 400;
        line-height: 1.25;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        border: 1px solid transparent;
        padding: .5rem 1rem;
        font-size: 1rem;
        border-radius: .25rem;
        -webkit-transition: all .2s ease-in-out;
        -o-transition: all .2s ease-in-out;
        transition: all .2s ease-in-out;
    }
</style>

<script>
    import Sheet from './Sheet.vue'

    export default{
        name: 'flux-template-form',
        props: ['json', 'form-field-name', 'grid-field-name'],
        data() {
            return JSON.parse(this.json)
        },
        computed: {
            formJson: function(){
                return JSON.stringify(this.form);
            },
            gridJson: function() {
                return JSON.stringify(this.grid);
            }
        },
        components:{
            Sheet
        }
    }
</script>
