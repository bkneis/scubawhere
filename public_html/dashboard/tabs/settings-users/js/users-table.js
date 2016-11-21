
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
            console.log(user);
            this.users.push(user);
        }

    }

});