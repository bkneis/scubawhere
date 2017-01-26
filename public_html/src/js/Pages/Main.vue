<template>
    <div>
        <div id="nav">
            <div id="nav-wrapper">

                <h1 id="logo"><a href="/"><img src="/dashboard/common/img/Scubawhere_logo.png"></a></h1>
                <user-actions-menu></user-actions-menu>

            </div>
        </div>

        <!-- This is needed for pages that are shorter than the window height -->
        <div class="sidebar-background"></div>

        <div id="page">
            <!-- This is needed for pages that are longer than the window height -->
            <div class="sidebar-background"></div>

            <side-nav-bar></side-nav-bar>

            <div id="guts">
                <!-- @todo add timeline here for wizard-->
                <breadcrumb :title="breadcrumb"></breadcrumb>

                <div id="content">
                    <router-view></router-view>
                </div>

            </div>
        </div>
    </div>
</template>
<script type="text/babel">
    import * as SideNavBar from 'Components/SideNavBar.vue';
    import * as Breadcrumb from 'Components/Breadcrumb.vue';
    import * as UserActionsMenu from 'Components/UserActionsMenu.vue';

    export default {
        name : 'MainPage',
        data() {
            return {
                breadcrumb : this.$route.meta.breadcrumb || ''
            }
        },
        components : { SideNavBar, Breadcrumb, UserActionsMenu },
        mounted() {
            let tab = localStorage.getItem('navBridge.currentTab');
            if (tab !== null) {
                this.$router.push({ path: tab });
                localStorage.removeItem('navBridge.currentTab');
            }
            this.$http.get('/api/user/companies')
                .then(response => {
                    window.companies = response.body;
                });
            this.$bus.$on('tab-change', tab => {
                this.breadcrumb = tab.meta.breadcrumb;
            });
        }
    }
</script>
