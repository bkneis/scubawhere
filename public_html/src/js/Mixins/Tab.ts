"use strict";

export default function() : any
{
    return {
        mounted() {
            this.$bus.$emit('tab-change', this.$route);
        }
    };
}
