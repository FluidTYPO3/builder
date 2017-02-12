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

<script>
    import Sheet from './Sheet.vue'

    export default{
        name: 'flux-template-form',
        props: ['json', 'form-field-name', 'grid-field-name', 'objects'],
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
