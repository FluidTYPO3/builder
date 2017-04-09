<script>
    import FieldMixin from './FieldMixin.vue';
    export default {
        name: 'SelectField',
        mixins: [FieldMixin],
        computed: {
            meta: function () {
                return fieldTypes['FluidTYPO3\\Flux\\Form\\Field\\Select'];
            },
            tabs: function () {
                var attributes = this.attributes;
                return [
                    {
                        label: 'Display',
                        attributes: [
                            attributes['size'],
                            attributes['renderType'],
                            attributes['itemListStyle'],
                            attributes['selectedListStyle'],
                            attributes['showIconTable']
                        ]
                    },
                    {
                        label: 'Items',
                        attributes: [
                            attributes['minItems'],
                            attributes['maxItems'],
                            attributes['multiple'],
                            attributes['emptyOption'],
                            attributes['items'],
                            attributes['translateCsvItems'],
                            attributes['itemsProcFunc']
                        ]
                    }
                ];
            }
        }
    }
</script>
