<template>
    <div class="checkbox">
        <label>
            <input type="checkbox"
                         ref="input"
                         v-model="checked"
                         v-on:change="updateValue($event.target.checked)"/>
             {{description}}
         </label>
     </div>
</template>

<script>
    export default{
        name: 'boolean-widget',
        props: ['value', 'description'],
        data: function () {
            return {
                checked: this.value
            }
        },
        methods: {
            updateValue: function (checked) {
                this.$emit('input', checked);
            }
        }
    }
</script>
