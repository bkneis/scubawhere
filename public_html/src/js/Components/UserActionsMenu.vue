<template>
    <div class="dropdown pull-right menu-container">

        <span class="dropbtn">
            <i class="fa fa-user user-icon"/>
            {{currentCompany.name}}
            <i class="fa fa-caret-down dropdown-arrow" aria-hidden="true"/>
        </span>

		<!--myDropdown -->
        <div class="dropdown-content">

			<span v-if="companies.length > 1">

				<a href="#">Switch to : </a>
				<a v-for="company in companies"
				   v-if="company.id !== currentCompany.id"
				   @click="switchCompany(company.id)">
					{{company.name}}
				</a>
				<hr>

			</span>

			<router-link to="/settings">Settings</router-link>
            <a @click="logout()">Logout</a>

        </div>
    </div>
</template>
<script type="text/babel">
    import Alert from 'Services/Alert.ts';
    export default {
        name : 'user-actions-menu',
        data() {
            return {
            	currentCompany : window.company,
            	companies      : window.companies
            }
        },
        methods : {
        	logout() {
        		this.$bus.$emit('logout');
        	},
        	switchCompany(id) {
        		this.$bus.$emit('switch-company', id);
        		this.$http.post('/api/user/switch-company', { company_id : id })
        		    .then(response => {
        		        Alert.info(response.body.status);
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
        		    });
        	}
        }
    }
</script>
<style scoped>
    .menu-container {
    	margin-top: 5px;
    }
    .user-icon {
    	padding-right: 5px;
    }
    .dropdown-arrow {
    	padding-left: 5px;
    	padding-right: 10px;
    }
    .dropdown-content {
    	margin-top: 10px;
    }
    /* Dropdown Button */
    .dropbtn {
        color: white;
        padding: 16px;
        font-size: 16px;
        border: none;
        cursor: pointer;
    }

    /* The container <div> - needed to position the dropdown content */
    .dropdown {
        position: relative;
        display: inline-block;
    }

    /* Dropdown Content (Hidden by Default) */
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        /* min-width: 160px; */
        width : 100%;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    }

    /* Links inside the dropdown */
    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    /* Change color of dropdown links on hover */
    .dropdown-content a:hover {background-color: #f1f1f1}

    /* Show the dropdown menu on hover */
    .dropdown:hover .dropdown-content {
        display: block;
    }

    /* Change the background color of the dropdown button when the dropdown content is shown */
    .dropdown:hover .dropbtn {
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>
