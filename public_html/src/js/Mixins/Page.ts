"use strict";

export default function() : any
{
    return {
        mounted() {
            this.$bus.$emit('page-change', this.$route);
        }
    };
}
