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
                var attributes = this.meta.attributes;
                var tabs = this.tabs;

                for (var tabIndex in tabs) {
                    for (var attributeIndex in tabs[tabIndex].attributes) {
                        var attributeName = tabs[tabIndex].attributes[attributeIndex];
                        tabs[tabIndex][attributeIndex] = attributes[attributeName];
                        delete attributes[attributeName];
                    }
                }

                var otherTab = {
                    label: 'Other',
                    attributes: []
                };
                for (var attributeIndex in attributes) {
                    otherTab.attributes.push(attributes[attributeIndex]);
                }
                tabs.push(otherTab);
                return tabs;
            },
            attributes: function () {
                return this.meta.attributes
            },
            meta: function () {
                return fieldTypes['FluidTYPO3\\Flux\\Form\\Field\\Input'];
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
