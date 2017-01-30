<template>
	<div v-show="visible" class="tabs-item">
		<slot></slot>
	</div>
</template>

<script>
    export default{
        name: 'tab',
        props: ['label', 'selected'],
        data: function() {
            return {
                visible: false
            }
        },
        created: function() {
            var self = this;
            if (typeof this.selected !== 'undefined') {
                this.$parent.$data.activeTab = this.label;
                this.visible = true;
            }
            this.$parent.$data.tabs.push(this.label);
            this.$parent.$on('activateTab', function(tab) {
                if (self.label === tab) {
                    self.visible = true;
                } else {
                    self.visible = false;
                }
            })
        }
    }
</script>
