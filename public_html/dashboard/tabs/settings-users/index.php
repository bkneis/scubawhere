<div id="wrapper" class="clearfix">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default" id="search-customer-container">

                <div class="panel-heading">
                    <h4 class="panel-title">Manage Users</h4>
                </div>

                <div class="panel-body">

                    <div style="padding-bottom: 20px;">
                        <add-user-form></add-user-form>
                    </div>

                    <users-table></users-table>

                </div>
            </div>
        </div>
    </div>
</div>

<template id="template-frm-add-user">
    <form id="frm-add-user" @submit.prevent="addUser()">
        <input type="text"
               style="width: 30%"
               placeholder="Username"
               required
               v-model="username">

        <input type="password"
               style="width: 30%"
               placeholder="Password"
               required
               v-model="password">

        <input type="text"
               style="width: 30%"
               placeholder="Email"
               required
               v-model="email">
        
        <input type="submit" class="btn btn-success" value="Add User">
    </form>
</template>

<template id="tbl-users">
    <table id="tbl-users" class="bluethead" v-if="usersLoaded">
        <thead>
            <tr class="bg-primary">
                <th>Username</th>
                <th>Email</th>
                <th>Password</th>
                <th>Registration Date</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="user in users">
                <td>{{user.username}}</td>
                <td>{{user.email}}</td>
                <td>.................</td>
                <td>{{user.created_at}}</td>
            </tr>
        </tbody>
    </table>
</template>

<script src="/tabs/settings-users/js/users-table.js"></script>
<script src="/tabs/settings-users/js/add-user-form.js"></script>
<script src="/tabs/settings-users/js/main.js"></script>