<template>
    <li>
        <div @click="show = !show">
            <i :class="styleClasses"/>
            <span>{{name}}</span>
            <span class="caret"/>
        </div>
        <transition enter-active-class="animated-x2 fadeIn"
                    leave-active-class="animated-x4 fadeOut">
            <ul v-show="show">
                <slot></slot>
            </ul>
        </transition>
    </li>
</template>
<script type="text/babel">
    export default {
        props : {
            icon : {},
            name : {},
            show : { default : false }
        },
        data() {
            return {
                iconStyles : {
                    'fa'    : true,
                    'fa-lg' : true,
                    'fa-fw' : true
                }
            }
        },
        computed : {
            styleClasses() {
                this.iconStyles[this.icon] = true;
                return this.iconStyles;
            }
        }
    }
</script>
