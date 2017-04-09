<template>
	<div class="kickstarter-inputField group">
		<div class="group-header">
			<span v-on:click="expanded=!expanded"><span class="label label-primary">{{shortType}}</span> <strong>{{field.name}}</strong></span>
			<div class="group-actions">
				<i class="fa fa-trash field-action" aria-hidden="true" @click="deleteField"></i>
				<i class="fa fa-cog field-action" aria-hidden="true" @click="showModal"></i>
			</div>
		</div>
		<modal :show.sync="field.showModal" :on-close="closeModal">
			<div class="modal-header">
				<div class="modal-actions">
					<i class="fa fa-times modal-close" aria-hidden="true" @click="closeModal"></i>
				</div>
				<h3>{{field.name}}</h3>
			</div>

			<div class="modal-body">
				<tabs>
					<tab v-for="tab in tabsGrouped" :label="tab.label" :selected="tab.selected">
						<attribute v-for="attribute in tab.attributes"
											 :attribute="attribute"
											 v-model="field[attribute.name]"/>
					</tab>
				</tabs>
			</div>
		</modal>
	</div>
</template>

<script>
    export default {
        props: ['data', 'modalVisible'],
        methods: {
            closeModal: function () {
                this.field.showModal = false;
            },
            showModal: function () {
                this.field.showModal = true;
            },
            deleteField: function () {
                this.$parent.$parent.sheet.children.splice(this.$parent.index, 1);
            }
        },
        computed: {
            shortType: function () {
                return this.field.type.substring(this.field.type.lastIndexOf('\\') + 1);
            },
            tabsGrouped: function () {
                var encountered = [];
                var tabs = this.tabs;
                var attributes = this.meta.attributes;
                var defaultTab = {
                    label: 'General',
                    selected: true,
                    attributes: [
                        {
                            name: 'type',
                            type: 'field-type'
                        },
                        attributes['name'],
                        attributes['label'],
                        attributes['default'],
                        attributes['required']
                    ]
                };
                var accessTab = {
                    label: 'Access',
                    attributes: [
                        attributes['enabled'],
                        attributes['displayCondition'],
                        attributes['exclude']
                    ]
                };
                var behaviorTab = {
                    label: 'Behavior',
                    attributes: [
                        attributes['extensionName'],
                        attributes['clear'],
                        attributes['inherit'],
                        attributes['inheritEmpty'],
                        attributes['requestUpdate'],
                        attributes['transform']
                    ]
                };
                if (typeof attributes['validate'] != 'undefined') {
                    behaviorTab.attributes.push(attributes['validate']);
                }
                var optionsTab = {
                    label: 'Options',
                    attributes: [
                        attributes['variables']
                    ]
                };
                var otherTab = {
                    label: 'Component',
                    attributes: []
                };

                tabs.unshift(defaultTab, accessTab, behaviorTab, optionsTab, otherTab);

                for (var tabIndex in this.tabs) {
                    for (var attributeIndex in tabs[tabIndex].attributes) {
                        if (typeof tabs[tabIndex].attributes[attributeIndex] != 'undefined') {
                            encountered.push(tabs[tabIndex].attributes[attributeIndex].name);
                        }
                    }
                }

                for (var attributeIndex in attributes) {
                    if (encountered.indexOf(attributeIndex) < 0) {
                        otherTab.attributes.push(attributes[attributeIndex]);
                    }
                }

                if (otherTab.attributes.length == 0) {
                    tabs.splice(tabs.indexOf(otherTab), 1);
                }

                return tabs;
            },
            attributes: function () {
                return this.meta.attributes
            },
            meta: function () {
                return fieldTypes['FluidTYPO3\\Flux\\Form\\Field\\None'];
            }
        },
        data: function () {
            return {
                field: this.data,
                id: Math.random().toString(36).substr(2, 10)
            }
        }
    }
</script>
