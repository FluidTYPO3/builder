<template>
    <input :id="id" class="form-control" ref="input" v-bind:value="value" v-on:input="updateValue($event.target.value)" />
</template>

<script>
    export default{
        name: 'string-widget',
        props: ['value', 'label', 'name'],
        methods: {
            updateValue: function (value) {
                this.$emit('input', value);
            }
        },
        computed: {
            id: function () {
            return 'test';
                //return this.name.replace('.', '');
            }
        }
    }
</script>
