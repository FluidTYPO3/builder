<template>
    <tr :is="fieldType" :data="field" />
</template>

<script>
    import NoneField from './Fields/NoneField.vue'
    import InputField from './Fields/InputField.vue'
    import TextField from './Fields/TextField.vue'
    import RadioField from './Fields/RadioField.vue'
    import CheckboxField from './Fields/CheckboxField.vue'
    import DateTimeField from './Fields/DateTimeField.vue'
    import FileField from './Fields/FileField.vue'
    import RelationField from './Fields/RelationField.vue'
    import SelectField from './Fields/SelectField.vue'
    import Vue from 'vue';

    export default{
        name: 'field',
        props: ['data', 'index'],
        data(){
            var field = this.data;
            return {
                field: this.data
            }
        },
        computed: {
            'fieldType': function() {
                if (this.field.type.match(/FluidTYPO3\\Flux\\Form\\Field/) == null) {
                    return 'NoneField';
                }
                return this.field.type.replace('FluidTYPO3\\Flux\\Form\\Field', '').replace(/\\/g, '') + 'Field';
            }
        },
        components: {
            NoneField,
            InputField,
            TextField,
            RadioField,
            CheckboxField,
            DateTimeField,
            FileField,
            RelationField,
            SelectField
        }
    }
</script>
