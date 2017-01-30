<template>
    <div class="form-row">
        <label>
            <input type="checkbox" :value="value" @input="updateValue($event.target.value)">
            {{ label }}
        </label>
    </div>
</template>
<script>
    export default{
        name: 'in-check',
        props: {
            value: {},
            label: { default: '' }
        },
        methods: {
            updateValue(val) {
                this.$emit('input', val);
            }
        }
    }
</script>
