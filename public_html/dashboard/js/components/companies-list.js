let userRepo = new UserRepo();

Vue.component('companies-list', {

    template : '#companies-list',

    data : function () {
        return {
            companies       : [],
            selectedCompany : null,
			companiesLoaded : false
        }
    },
    
    methods : {
        switchCompany : function (id) {
            this.selectedCompany = id;
            userRepo.switchCompany(this.selectedCompany,  function (res) {
                pageMssg(res.status, 'info');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            });
        },
        logout : function() {
            $.ajax({
                url: "/api/logout",
                type: "GET",
                dataType: "json",
                success: function(log) {
                    window.location.href = '/';
                }
            });
        }
    },

    created : function () {
        let vm = this;
        userRepo.getCompanies(function (data) {
            vm.companies = data;
            vm.selectedCompany = _.findWhere(vm.companies, {active : true}).id;
            vm.companiesLoaded = true;
        });
    },

    computed : {
        currentCompany : function() {
            for(let i in this.companies) {
                if(this.companies[i].id === this.selectedCompany) {
                    return this.companies[i];
                }
            }
        }
    }

});
