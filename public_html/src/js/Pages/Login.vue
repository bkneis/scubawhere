<template>
    <div id="login-wrapper">
        <div id="login-form">
            <form accept-charset="utf-8" @submit.prevent="login">
                <img src="/dashboard/common/img/scubawhere_logo.svg">
                <h1>RMS Operator Login</h1>

                <span v-if="error" class="form-error">{{error}}</span>

                <input type="text" v-model="username" placeholder="Username">
                <input type="password" v-model="password" placeholder="Password"><br>

                <input type="hidden" name="_token" value="">
                <input type="submit" class="btn btn-primary" value="Log in"/>

                <a href="/register/" class="btn btn-success">Register Your Dive Centre</a>

            </form>
        </div>

        <a href="/api/password/remind" class="forgot-password">Forgot your password?</a>
    </div>
</template>
<style src="../../sass/pages/login.css" scoped/>
<script type="text/babel">
    export default {
        name : 'LoginPage',
        data() {
            return {
                username : '',
                password : '',
                error    : ''
            }
        },
        methods : {
            login() {
                this.$http.post('/api/login', this.loginData()).then((response) => {
                    this.$bus.$emit('loginSuccess');
                }, (response) => {
                    this.error = response.body.errors[0];
                });
            },
            loginData() {
                return {
                    username : this.username,
                    password : this.password,
                    _token   : window.token
                };
            }
        }
    }
</script>
