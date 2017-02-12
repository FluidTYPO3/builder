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
                        label: 'General',
                        selected: true,
                        attributes: [
                            {
                                name: 'type',
                                type: 'field-type'
                            },
                            attributes['name'],
                            attributes['label'],
                            attributes['default'],
                            attributes['required']
                        ]
                    }
                ];
            }
        }
    }
</script>
