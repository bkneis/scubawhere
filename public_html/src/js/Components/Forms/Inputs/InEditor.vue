<template>
    <div class="form-row">
        <label v-if="label" class="field-label">{{label}}</label>
        <div class="editor-container">
            <quill-editor ref="editor" v-model="value" :config="config" @change="updateValue($event)"/>
        </div>
    </div>
</template>
<style scoped>
.editor-container {
    height: 300px;
    position: relative;
    clear: both;
    margin-bottom: 80px;
}
</style>
<script type="text/babel">
    import { quillEditor } from 'vue-quill-editor';

    export default {
        props : {
            label  : {},
            value  : {},
            config : { default() { return { theme : 'snow' }}}
        },
        components : { quillEditor },
        methods : {
            updateValue({editor, html, text }) { this.$emit('input', html); }
        }
    }
</script>
