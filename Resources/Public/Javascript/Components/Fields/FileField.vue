<script>
    import FieldMixin from './FieldMixin.vue';
    export default{
        name: 'FileField',
        mixins: [FieldMixin],
        computed: {
            meta: function () {
                return fieldTypes['FluidTYPO3\\Flux\\Form\\Field\\File'];
            },
            tabs: function () {
                var attributes = this.attributes;
                return [];
            }
        }
    }
</script>
