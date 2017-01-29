<template>
    <div class="kickstarter-field">
        <div :is="fieldType" :data="field" />
    </div>
</template>

<script>
    import NoneField from './Fields/NoneField.vue'
    import InputField from './Fields/InputField.vue'
    import SelectField from './Fields/SelectField.vue'

    export default{
        name: 'field',
        props: ['data'],
        data(){
            return {
                field: this.data
            }
        },
        computed: {
            'fieldType': function() {
                // FluidTYPO3\Flux\Form\Field
                if (this.field.type.match(/FluidTYPO3\\Flux\\Form\\Field/) == null) {
                    return 'NoneField';
                }
                return this.field.type.replace('FluidTYPO3\\Flux\\Form\\Field', '').replace(/\\/g, '') + 'Field';
            }
        },
        components:{
            NoneField,
            InputField,
            SelectField
        }
    }
</script>
