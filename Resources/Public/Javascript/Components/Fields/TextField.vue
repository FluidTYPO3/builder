<script>
    import FieldMixin from './FieldMixin.vue';
    export default {
        name: 'TextField',
        mixins: [FieldMixin],
        computed: {
            meta: function () {
                return fieldTypes['FluidTYPO3\\Flux\\Form\\Field\\Text'];
            },
            tabs: function () {
                var attributes = this.attributes;
                return [];
            }
        }
    }
</script>
