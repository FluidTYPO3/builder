<template>
    <div class="kickstarter-wizard group">
        <div class="group-header">
            wizard: {{field.label}}
        </div>
        <div class="group-rows">
            <div class="group-row">
                <div class="group-row-label">
                    <label>Type:</label>
                </div>
                <div class="group-row-content">
                    <input v-model="field.type" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default{
        name: 'wizard',
        props: ['data'],
        data(){
            return {
                field: this.data
            }
        }
    }
</script>
