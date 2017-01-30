<template>
    <div>
        <div class="group-row">
            <div class="group-row-label">
                <label>{{label}}:</label>
                <small v-if="description">{{description}}</small>
            </div>
            <div class="group-row-content">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
    export default{
        name: 'group-row',
        props: ['label', 'description']
    }
</script>
