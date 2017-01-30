<template>
    <div>
        <div class="group-row">
            <div class="group-row-label">
                <label>Name:</label>
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
