<template>
    <div class="form-row">
        <label v-if="label" :for="name" class="field-label">
            {{label}}
        </label>
        <input type="number"
               :name="name"
               :min="min"
               :max="max"
               :placeholder="placeholder">
    </div>
</template>
<script type="text/babel">
    export default {
        props: {
            label: {},
            name: {},
            min: { default: 0 },
            max: {},
            placeholder: { default: '00.00' }
        }
    }
</script>
