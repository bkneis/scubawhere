Vue.component('modal', {

    template : '#modal-template',

    methods : {
        closeCustomerModal : function () {
            eventHub.$emit('closeCustomerModal');
        }
    }

});