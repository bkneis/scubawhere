<template>
    <div class="form-row">
        <label v-if="label" class="field-label">{{label}}</label>
        <select title="" v-for="selectedEntities as selected" @input="updateValue">
            <option value="0">Select a {{model}}</option>
            <option v-for="entity in entities" :value="entity.id" v-model="selected">{{entity.name}}</option>
        </select>
        <button type="button" class="btn btn-default">Add another {{model}}</button>
    </div>
</template>
<script type="text/babel">
    export default {
        props : {
            model : {},
            entities : { default: () => [] }
        },
        data() {
            return {
                selectedEntities : [ {} ]
            }
        },
        methods : {
            addEntity() {
                this.selectedEntities.push({});
            },
            updateValue(val) {
                this.$emit('input', this.selectedEntities);
            }
        }
    }
</script>
