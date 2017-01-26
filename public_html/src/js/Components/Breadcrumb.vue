<template>
    <div id="breadcrumbs">
        <a href="/dashboard/#dashboard" class="breadcrumbs-home">
            <i class="fa fa-home fa-lg fa-fw"/>
        </a>
        <small><i class="fa fa-chevron-right fa-fw text-muted"/></small>
        {{ title }}
    </div>
</template>
<script type="text/babel">
    export default {
        name : 'breadcrumb',
        props : {
            title : {
                type : String,
                required : true
            }
        }
    }
</script>
