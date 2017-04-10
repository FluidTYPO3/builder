<template>
    <div class="form-group">
        <div class="col-sm-2 control-label">
            <label :for="id">{{label}}</label>
        </div>
        <div class="col-sm-10" rel="tooltip" :title="attribute.description">
            <div :is="renderType" v-bind:value="value" v-bind:label="label" v-bind:description="attribute.description" v-on:input="updateValue($event)"></div>
        </div>
    </div>
</template>

<script>
    export default{
        name: 'attribute',
        props: ['attribute', 'value'],
        computed: {
            label: function () {
                return this.attribute.name.replace(/([A-Z])/g, ' $1')
                // uppercase the first character
                .replace(/^./, function (str) {
                    return str.toUpperCase();
                });
            },
            renderType: function () {
                return this.attribute.type + '-widget';
            }
        },
        methods: {
            updateValue: function (value) {
                this.$emit('input', value);
            }
        }
    }
</script>
