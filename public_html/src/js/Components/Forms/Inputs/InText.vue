<template>
    <div class="form-row">
        <label v-if="label" class="field-label">{{label}}</label>
        <input type="text"
               :placeholder="placeholder"
               :value="value"
               @input="updateValue($event.target.value)"
               :class="{ 'requiredTextInput' : required }"/>
    </div>
</template>
<script type="text/babel">
    export default {
        props : {
            label       : {},
            placeholder : {},
            value       : {},
            required    : {}
        },
        mounted() {
            if (this.required) {
                let classes = document.getElementsByClassName('requiredTextInput');
                for (let i = 0; i < classes.length; i++) {
                    classes[i].required = true;
                }
            }
        },
        methods : {
            updateValue(val) { this.$emit('input', val); }
        }
    }
</script>
