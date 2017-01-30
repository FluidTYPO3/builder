import Vue from 'vue';
import KickstarterForm from './Components/KickstarterForm.vue';
import Tabs from './Components/Tabs.vue';
import Tab from './Components/Tab.vue';
import GroupRow from './Components/GroupRow.vue';
import FieldTypeSelector from './Components/FieldTypeSelector.vue';

Vue.component('tabs', Tabs);
Vue.component('tab', Tab);
Vue.component('group-row', GroupRow);
Vue.component('field-type-selector', FieldTypeSelector);

new Vue({
  el: '#kickstarter',
  components: {
    KickstarterForm
  }
})
