
Vue.component('add-user-form', {

    template : '#template-frm-add-user',

    data : function() {
        return {
            username : '',
            password : '',
            email    : ''
        };
    },

    methods : {
        addUser : function () {
            let params = {
                username : this.username,
                password : this.password,
                email    : this.email
            };
            userRepo.add(params, function (res) {
                pageMssg(res.status, 'success');
                // @todo create generic function to empty all form inputs
                this.username = '';
                this.password = '';
                this.email = '';
                eventHub.$emit('add-user', res.data.user);
            },
            function (xhr) {
                let data = JSON.parse(xhr.responseText);
                pageMssg(data.errors[0], 'danger');
            });
        }
    }

});