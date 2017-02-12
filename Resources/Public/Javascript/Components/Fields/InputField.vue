<script>
    import FieldMixin from './FieldMixin.vue';
    export default {
        name: 'InputField',
        mixins: [FieldMixin],
        computed: {
            meta: function () {
                return fieldTypes['FluidTYPO3\\Flux\\Form\\Field\\Input'];
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
                            attributes['placeholder'],
                            attributes['required']
                        ]
                    },
                    {
                        label: 'Constraints',
                        attributes: [
                            attributes['maxCharacters'],
                            attributes['minimum'],
                            attributes['maximum'],
                            attributes['size']
                        ]
                    }
                ];
            }
        }
    }
</script>
