<script>
    import FieldMixin from './FieldMixin.vue';
    export default{
        name: 'DateTimeField',
        mixins: [FieldMixin],
        computed: {
            meta: function () {
                return fieldTypes['FluidTYPO3\\Flux\\Form\\Field\\DateTime'];
            },
            tabs: function () {
                var attributes = this.attributes;
                return [];
            }
        }
    }
</script>
