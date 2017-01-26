<template>
    <div id="app">
        <login-page v-if="userAuthenticated === false"/>
        <main-page v-if="userAuthenticated === true"/>
        <alert :type="alert.type"
               :width="alert.width"
               :show="alert.show"
               :placement="alert.placement">
            {{alert.text}}
        </alert>
    </div>
</template>

<script>
    import * as LoginPage from 'Pages/Login.vue';
    import * as MainPage from 'Pages/Main.vue';

    export default {
        name: 'app',
        components: { LoginPage, MainPage },
        data() {
            return {
                userAuthenticated : null,
                alert: {
                    type      : '',
                    width     : '',
                    show      : false,
                    placement : 'top',
                    text      : ''
                }
            }
        },
        methods : {
            /**
             * Update the global alert component in the application
             *
             * Instead of the component itself containing the logic to control
             * its visibility, use this service to timeout the show attribute
             * and toggle its visibility.
             */
            showAlert(type, text, width) {
                this.alert.type  = type;
                this.alert.text  = text;
                this.alert.width = width;
                this.alert.show = true;
                setTimeout(() => {
                    this.alert.show = false;
                }, 3000);
            },
            /**
             * Check if the current session is authenticated
             * Request from the API the user's company, If success, then the user
             * must be logged in so load the application. On error, show the login page
             */
            checkAuth() {
                this.$http.get('/api/company')
                    .then(response => {
                        window.company = response.body;
                        this.$http.get('/api/token')
                            .then(response => {
                                window.token = response.body;
                                this.userAuthenticated = true;
                            });
                    })
                    .catch(response => {
                        this.userAuthenticated = false;
                    });
            },
            logout() {
                this.$http.get('/api/logout')
                    .then(response => {
                        this.userAuthenticated = false;
                    })
                    .catch(response => {
                        console.log(response);
                    })
            }
        },
        mounted() {
            window.companies = [];
            this.checkAuth();
            this.$bus.$on('loginSuccess', this.checkAuth);
            this.$bus.$on('alert', this.showAlert);
            this.$bus.$on('logout', this.logout);
        }
    }
</script>
