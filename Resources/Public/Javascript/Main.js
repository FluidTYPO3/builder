import Vue from 'vue';
import Draggable from 'vuedraggable';
import Multiselect from 'vue-multiselect';
import FluxTemplateForm from './Components/FluxTemplateForm.vue';
import Tabs from './Components/Tabs.vue';
import Tab from './Components/Tab.vue';
import GroupRow from './Components/GroupRow.vue';
import FieldTypeSelector from './Components/FieldTypeSelector.vue';
import Attribute from './Components/Attribute.vue';
import Modal from './Components/Modal.vue';
import StringWidget from './Components/Widgets/StringWidget.vue';
import FieldTypeWidget from './Components/Widgets/FieldTypeWidget.vue';
import BooleanWidget from './Components/Widgets/BooleanWidget.vue';

Vue.component('draggable', Draggable);
Vue.component(Tabs.name, Tabs);
Vue.component(Tab.name, Tab);
Vue.component(GroupRow.name, GroupRow);
Vue.component(FieldTypeSelector.name, FieldTypeSelector);
Vue.component(Attribute.name, Attribute);
Vue.component(StringWidget.name, StringWidget);
Vue.component(BooleanWidget.name, BooleanWidget);
Vue.component('integer-widget', StringWidget);
Vue.component('array-widget', StringWidget);
Vue.component('mixed-widget', StringWidget);
Vue.component(FieldTypeWidget.name, FieldTypeWidget);
Vue.component(Modal.name, Modal);
Vue.component('multiselect', Multiselect);

new Vue({
    el: '.flux-template-form',
    components: {
        FluxTemplateForm
    }
});
