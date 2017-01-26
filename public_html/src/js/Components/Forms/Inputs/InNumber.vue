<template>
    <div class="form-row">
        <label v-if="label"
               :for="name"
               class="form-label">
            {{label}}
        </label>
        <input type="number"
               :name="name"
               :min="min"
               :max="max"
               :placeholder="0.00">
    </div>
</template>
<script type="text/babel">
    export default {
        props : ['name', 'min', 'max', 'placeholder'],
        data() {
            return {
            }
        },
        computed : {
            min() {
                return this.min ? this.min : '0';
            },
            placeholder() {
                return this.placeholder ? this.placeholder : '0.00';
            }
        }
    }
</script>
