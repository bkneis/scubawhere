<template>
    <li :class="{ 'tab-active' : active }" @click="activateTab">
        <router-link :to="url">
            <i :class="styleClasses" v-if="icon"/>
            {{name}}
        </router-link>
    </li>
</template>

<script type="text/babel">
    export default {
        props : {
            url : {},
            icon : {},
            name : {}
        },
        data() {
            return {
                iconStyles : {
                    'fa'    : true,
                    'fa-lg' : true,
                    'fa-fw' : true
                },
                active : false
            }
        },
        computed : {
            styleClasses() {
                this.iconStyles[this.icon] = true;
                return this.iconStyles;
            }
        },
        mounted() {
            this.$bus.$on('tab-click', () => {
                this.active = false;
            });
        },
        methods : {
            activateTab() {
                this.$bus.$emit('tab-click');
                this.active = true;
            }
        }
    }
</script>
