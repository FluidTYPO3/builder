<template>
	<tbody>
	    <tr>
            <td class="col-xs-1" nowrap="nowrap">
                <span v-bind:class="['label', { 'label-default' : isDisabled }, { 'label-success' : !isDisabled }]">{{shortType}}</span>
            </td>
            <td>
                <strong>{{field.name}}</strong>
            </td>
            <td class="col-xs-1 align-right" nowrap="nowrap">
                <div class="btn-group" role="group">
                    <button type="button" @click="toggleField" class="btn btn-default"><i v-bind:class="['fa field-action', { 'fa-toggle-on' : !isDisabled }, { 'fa-toggle-off' : isDisabled }]"></i></button>
                    <button type="button" @click="deleteField" class="btn btn-default"><i class="fa fa-trash field-action" aria-hidden="true"></i></button>
                    <button type="button" @click="toggleEdit" class="btn btn-default"><i class="fa fa-pencil field-action" aria-hidden="true"></i></button>
                </div>
            </td>
        </tr>
        <tr :id="id" v-if="showEdit">
            <td colspan="3">
                <ul class="nav nav-tabs" role="tablist">
                    <template v-for="(tab, index) in tabsGrouped">
                        <li role="presentation" :class="tabNavClass(index)"><a @click="activate(index)" href="" :aria-controls="tabIndex(index)" role="tab" data-toggle="tab">{{tab.label}}</a></li>
                    </template>
                </ul>
                <div class="tab-content">
                    <template v-for="(tab, index) in tabsGrouped">
                        <div role="tabpanel" :class="tabClass(index)" :id="tabIndex(index)">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <attribute v-for="attribute in tab.attributes" :attribute="attribute" v-model="field[attribute.name]" />
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </td>
        </tr>
	</tbody>
</template>

<script>
    export default {
        props: ['data'],

        methods: {
            deleteField: function () {
                this.$parent.$parent.sheet.children.splice(this.$parent.index, 1);
            },
            toggleField: function () {
                this.field.enabled = !this.field.enabled;
            },
            activate: function (index) {
                this.activeTab = index;
                $('#' + this.tabIndex(index)).tab('show');
            },
            tabIndex: function (index) {
                //console.log(this.data.name);
                return 'tab' + this.data.name.replace('.', '') + index.toString();
            },
            tabClass: function (index) {
                return index == this.activeTab ? 'tab-pane fade in active' : 'tab-pane fade';
            },
            tabNavClass: function (index) {
                return index == this.activeTab ? 'active' : '';
            },
            toggleEdit: function (status) {
                this.showEdit = typeof status == 'boolean' ? status : !this.showEdit;
            }
        },
        computed: {
            shortType: function () {
                return this.field.type.substring(this.field.type.lastIndexOf('\\') + 1);
            },
            isDisabled: function() {
                return !this.field.enabled;
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
                id: 'a' + Math.random().toString(36).substr(2, 10),
                activeTab: 0,
                showEdit: this.showEdit
            }
        }
    }
</script>
