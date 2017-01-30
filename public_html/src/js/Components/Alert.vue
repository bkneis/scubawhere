<template>
    <transition enter-active-class="animated-x2 fadeIn"
                leave-active-class="animated-x4 fadeOut">
        <div v-show="show"
             :class="{
        'alert':		true,
        'alert-success':(type == 'success'),
        'alert-warning':(type == 'warning'),
        'alert-info':	(type == 'info'),
        'alert-danger':	(type == 'danger'),
        'top': 			(placement === 'top'),
        'top-right': 	(placement === 'top-right')
      }"
                :style="{width:width}"
                role="alert"
        >
            <button v-show="dismissable" type="button" class="close" @click="show = false">
                <span>&times;</span>
            </button>
            <slot/>
        </div>
    </transition>
</template>

<script type="text/babel">
    export default {
        name : 'alert',
        props: {
            type: {
                type: String
            },
            dismissable: {
                type: Boolean,
                default: false
            },
            show: {
                type: Boolean,
                default: false
            },
            width: {
                type: String
            },
            placement: {
                type: String
            }
        }
    }
</script>
