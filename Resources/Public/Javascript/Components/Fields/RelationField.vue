<script>
    import FieldMixin from './FieldMixin.vue';
    export default {
        name: 'RelationField',
        mixins: [FieldMixin],
        computed: {
            meta: function () {
                return fieldTypes['FluidTYPO3\\Flux\\Form\\Field\\Relation'];
            },
            tabs: function () {
                var attributes = this.attributes;
                return [
                    {
                        label: 'Display',
                        attributes: [
                            attributes['size']
                        ]
                    },
                    {
                        label: 'Relation',
                        attributes: [
                            attributes['table'],
                            attributes['condition'],
                            attributes['foreignField'],
                            attributes['foreignSelector'],
                            attributes['foreignSortby']
                        ]
                    }
                ];
            }
        }
    }
</script>
