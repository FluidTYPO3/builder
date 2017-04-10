import Vue from 'vue';
import Draggable from 'vuedraggable';
import Multiselect from 'vue-multiselect';
import FluxTemplateForm from './Components/FluxTemplateForm.vue';
import FieldTypeSelector from './Components/FieldTypeSelector.vue';
import Attribute from './Components/Attribute.vue';
import StringWidget from './Components/Widgets/StringWidget.vue';
import IntegerWidget from './Components/Widgets/IntegerWidget.vue';
import FieldTypeWidget from './Components/Widgets/FieldTypeWidget.vue';
import BooleanWidget from './Components/Widgets/BooleanWidget.vue';

Vue.component('draggable', Draggable);
Vue.component(FieldTypeSelector.name, FieldTypeSelector);
Vue.component(Attribute.name, Attribute);
Vue.component(StringWidget.name, StringWidget);
Vue.component(BooleanWidget.name, BooleanWidget);
Vue.component(IntegerWidget.name, IntegerWidget);
Vue.component('integer-widget', IntegerWidget);
Vue.component('int-widget', IntegerWidget);
Vue.component('array-widget', StringWidget);
Vue.component('mixed-widget', StringWidget);
Vue.component(FieldTypeWidget.name, FieldTypeWidget);
Vue.component('multiselect', Multiselect);

new Vue({
    el: '.flux-template-form',
    components: {
        FluxTemplateForm
    }
});
