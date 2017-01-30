<template>
    <div class="form-row">
        <label v-if="label" class="field-label">{{label}}</label>
        <select>
            <option>Please select</option>
            <option v-for="option in options">
                {{ option[display] }}
            </option>
        </select>
    </div>
</template>
<script>
    export default {
        name: 'in-select',
        props: {
            label: {},
            options: { default: () => [] },
            display: { default: 'name' }
        }
    }
</script>
