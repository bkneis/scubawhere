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

<div class="modal fade" id="modal-update-user">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Update User Info</h4>
            </div>
            <form id="email-customer-form" class="form-horizontal" role="form">
                <div class="modal-body" id="edit-user-fields"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">UPDATE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/x-handlebars" id="edit-user-info-template">
    <div class="form-row">
        <label>Username : </label>
        <input type="text" name="username" value="{{username}}">
    </div>
    <div class="form-row">
        <label>Email : </label>
        <input type="email" name="email" value="{{email}}">
    </div>
</script>

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

        <input type="email"
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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="user in users">
                <td>{{user.username}}</td>
                <td>{{user.email}}</td>
                <td>.................</td>
                <td>{{user.created_at}}</td>
                <td>
                    <button class="btn btn-info btn-sm"
                            v-if="user.active"
                            @click="resetPassword()">
                        Reset Password
                    </button>
                    <button class="btn btn-primary btn-sm"
                            v-if="user.active"
                            @click="updateInfo(user)">
                        Update Info
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script src="/dashboard/tabs/settings-users/js/users-table.js"></script>
<script src="/dashboard/tabs/settings-users/js/add-user-form.js"></script>
<script src="/dashboard/tabs/settings-users/js/main.js"></script>