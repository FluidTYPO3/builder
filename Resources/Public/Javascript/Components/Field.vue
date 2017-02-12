<template>
    <div class="kickstarter-field">
        <div :is="fieldType" :data="field" />
    </div>
</template>

<script>
    import NoneField from './Fields/NoneField.vue'
    import InputField from './Fields/InputField.vue'
    import SelectField from './Fields/SelectField.vue'
    import Vue from 'vue';

    export default{
        name: 'field',
        props: ['data'],
        data(){
            var field = this.data;
            Vue.set(field, 'showModal', false);
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
        components:{
            NoneField,
            InputField,
            SelectField
        }
    }
</script>
