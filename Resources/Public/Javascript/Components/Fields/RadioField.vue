<script>
    import FieldMixin from './FieldMixin.vue';
    export default{
        name: 'RadioField',
        mixins: [FieldMixin],
        computed: {
            meta: function () {
                return fieldTypes['FluidTYPO3\\Flux\\Form\\Field\\Radio'];
            },
            tabs: function () {
                var attributes = this.attributes;
                return [];
            }
        }
    }
</script>
