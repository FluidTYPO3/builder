<script>
    import FieldMixin from './FieldMixin.vue';
    export default{
        name: 'NoneField',
        mixins: [FieldMixin],
        computed: {
            meta: function () {
                return fieldTypes['FluidTYPO3\\Flux\\Form\\Field\\None'];
            },
            tabs: function () {
                var attributes = this.attributes;
                return [];
            }
        }
    }
</script>
