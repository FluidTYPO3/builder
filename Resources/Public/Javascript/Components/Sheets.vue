<template>
    <div>
        <div class="alert">
            <ul class="nav nav-tabs" role="tablist" id="sheets">
                <template v-for="(sheet, index) in form.children">
                    <li role="presentation" :class="tabNavClass(index)"><a @click="activate(index)" href="" :aria-controls="tabIndex(index)" role="tab" data-toggle="tab">{{sheet.name}}</a></li>
                </template>
                <li id="createSheet" role="presentation"><a @click="createSheet" href="" role="tab" data-toggle="tab"><i class="fa fa-plus"></i></a></li>
            </ul>
            <div class="tab-content">
                <template v-for="(sheet, index) in form.children">
                    <div role="tabpanel" :class="tabClass(index)" :id="tabIndex(index)">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <sheet :data="sheet" />
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    import Sheet from './Sheet.vue'

    export default{
        name: 'flux-template-sheets',
        props: ['json', 'form-field-name', 'grid-field-name', 'objects'],
        data() {
            var data = JSON.parse(this.json);
            return {
                form: data.form,
                activeTab: 0
            };
        },
        methods: {
            activate: function (index) {
                this.activeTab = index;
                $('#' + this.tabIndex(index)).tab('show');
            },
            tabIndex: function (index) {
                return 'sheet' + index.toString();
            },
            tabClass: function(index) {
                return index == this.activeTab ? 'tab-pane fade in active' : 'tab-pane fade';
            },
            tabNavClass: function(index) {
                return index == this.activeTab ? 'active' : '';
            },
            createSheet: function() {
                this.form.children.push({
                    name: 'new' + this.form.children.length.toString()
                });
                //$('#createSheet').removeClass('active');
                this.activate(this.form.children.length - 1);
            }
        },
        computed: {
            formJson: function(){
                return JSON.stringify(this.form);
            },
            gridJson: function() {
                return JSON.stringify(this.grids);
            },
            attributes: function () {
                return fieldTypes['FluidTYPO3\\Flux\\Form'].attributes;
            }
        },
        components:{
            Sheet
        }
    }
</script>
