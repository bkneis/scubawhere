<template>
    <form accept-charset="utf8" @submit.prevent="submit(form.action)">
        <button v-if="form.action !== 'Create'"
              class="btn btn-danger pull-right"
              @click.prevent="submit('Delete')">
            Remove
        </button>
		<div class="yellow-helper errors" v-if="form.errors.all().length > 0">
			<strong>There are a few problems with the form:</strong>
			<ul>
				<li v-for="error in form.errors.all()">{{error}}</li>
			</ul>
		</div>
        <slot/>
        <input type="submit" class="btn btn-primary btn-lg pull-right" value="SAVE"/>
    </form>
</template>

<style scoped>
	.errors {
		color: #E82C0C;
	}
</style>

<script type="text/babel">
    export default {
    	props : {
    		form : {}
    	},
        methods : {
            submit(action) {
            	let id = this.form.id;
            	this.$emit(action.toLowerCase(), id);
            },
        }
    }
/**
	|-------------------------------------------------------------------------------
	| DOCUMENTATION
	|-------------------------------------------------------------------------------
    | This component is used as a self contained form that does not rely on any js
    | to perform basic resourceful tasks, such as creating, updating and deleting.
    | When the form is submitted, it will emit an event using the format
    | <action>-model, i.e. update-model or create-model. So that parent components can
    | then add on any functionality.
    |--------------------------------------------------------------------------------
*/
</script>
