<template>
    <div id="wrapper" class="clearfix">
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Available {{model}}s</h4>
                    </div>
                    <div class="panel-body">
                        <button class="btn btn-success text-uppercase" @click.prevent="$emit('create')">&plus; Add {{model}}</button>
                        <slot name="entity-list"/>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">{{action}} {{model}}</h4>
                    </div>
                    <div class="panel-body">
                        <slot name="entity-form"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props : {
            model : {},
            action : { default : 'Create' }
        }
    }
</script>
|-------------------------------------------------------------------------------
| DOCUMENTATION
|-------------------------------------------------------------------------------
| The purpose of this component is to remove some of the boilerplate code for
| the management tabs. As each tab keeps the same format of the list of models
| on the left and the form to create / updated / delete, so it makes sense to extract
| the panels and containers etc and just slot in the list and form.
|
| Expects 2 slots : Entity list and Entity form
|--------------------------------------------------------------------------------
