
Vue.component('users-table', {

    template : '#tbl-users',

    data : function () {
        return {
            users       : [],
            usersLoaded : false
        };
    },

    created : function () {
        // Subscribe to the add user event (emitted from the add user form component)
        eventHub.$on('add-user', this.addUser);

        let vm = this;
        Company.getUsers(function (res) {
            vm.users       = res.data.users;
            vm.usersLoaded = true;
        });
    },

    methods : {

        addUser : function (user) {
            this.users.push(user);
        },

        resetPassword : function () {
            userRepo.resetPassword(function (res) {
                pageMssg('Success. Please check your email and follow the link to reset your password', 'success');
            },
            function (xhr) {
                pageMssg('Oh oh, there seems to have been a mistake. We cannot complete your operation, please try again soon.', 'danger');
            });
        },

        updateInfo : function (user) {
            var editUserTemplate = Handlebars.compile($('#edit-user-info-template').html());
            $('#edit-user-fields').empty().append(editUserTemplate(user));
            $('#modal-update-user').modal('show');
        }

    }

});