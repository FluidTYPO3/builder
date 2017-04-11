<template>
    <div>
        <div class="alert">
            <input type="hidden" v-model="formJson" :name="formFieldName" />
            <fieldset>
                <attribute v-for="attribute in attributes" :attribute="attribute" v-model="attributes[attribute.name]" v-bind:value="form[attribute.name]" />
            </fieldset>
        </div>
    </div>
</template>

<script>
    export default{
        name: 'flux-template-form',
        props: ['json', 'form-field-name', 'objects'],
        data() {
            var data = JSON.parse(this.json);
            return {
                form: data.form,
                activeTab: 0
            };
        },
        computed: {
            formJson: function(){
                return JSON.stringify(this.form);
            },
            attributes: function () {
                return fieldTypes['FluidTYPO3\\Flux\\Form'].attributes;
            }
        }
    }
</script>
