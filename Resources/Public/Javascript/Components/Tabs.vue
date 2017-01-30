<template>
	<div class="tabs">
		<div class="tabs-navigation">
			<div v-for="tab in tabs" @click="activate(tab)" :class="{ 'tabs-navigation-item--active': (activeTab === tab) }" class="tabs-navigation-item">
				{{ tab }}
			</div>
		</div>
		<div class="tabs-items">
			<slot></slot>
		</div>
	</div>
</template>

<script>
    export default{
        name: 'tabs',
        data: function() {
            return {
                activeTab: '',
                tabs: []
            }
        },
        methods: {
            activate: function(tab) {
                this.activeTab = tab;
                this.$emit('activateTab', tab);
            }
        }
    }
</script>