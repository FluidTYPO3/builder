<template>
	<div>
		<group-row :label="label">
			<div :is="renderType"
					 v-bind:value="value"
					 v-on:input="updateValue($event)"></div>

			<small v-if="attribute">{{value}} - {{attribute.description}}</small>
		</group-row>
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
