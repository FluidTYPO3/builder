<template>
    <div>
        <div class="alert">
            <input type="hidden" v-model="gridJson" :name="gridFieldName" />

            <h2>Grids</h2>

        </div>
    </div>
</template>

<script>
    export default{
        name: 'flux-template-grids',
        props: ['json', 'grid-field-name'],
        data() {
            var data = JSON.parse(this.json);
            return {
                form: data.form,
                activeTab: 0
            };
        },
        computed: {
            gridJson: function() {
                return JSON.stringify(this.grids);
            }
        }
    }
</script>
