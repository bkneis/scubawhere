function UserRepo() {

    this.getCompanies = function (successFn, errorFn) {
        $.ajax({
            url  : '/api/user/companies',
            type : 'GET',
            success : successFn,
            error  : function (xhr) {
                console.log(xhr);
                if (typeof errorFn === 'function') {
                    errorFn();
                }
            }
        });
    };

    this.getActiveCompany = function (successFn, errorFn) {
        $.ajax({
            url  : '/api/user/active-company',
            type : 'GET',
            success : successFn,
            error  : function (xhr) {
                console.log(xhr);
                if (typeof errorFn === 'function') {
                    errorFn();
                }
            }
        });
    };

    this.switchCompany = function (id, successFn, errorFn) {
        $.ajax({
            url  : '/api/user/switch-company',
            type : 'POST',
            data : {
                _token     : window.token,
                company_id : id
            },
            success : successFn,
            error  : function (xhr) {
                console.log(xhr);
                if (typeof errorFn === 'function') {
                    errorFn();
                }
            }
        });
    };

    this.add = function (params, successFn, errorFn) {
        params._token = window.token;
        $.ajax({
            type    : 'POST',
            url     : '/api/user',
            data    : params,
            success : successFn,
            error   : errorFn
        });
    };

    this.resetPassword = function (successFn, errorFn) {
        var params = {
            _token : window.token
        };
        $.ajax({
            type    : 'POST',
            url     : '/api/password/remind',
            data    : params,
            success : successFn,
            error   : errorFn
        });
    };

    this.update = function (params, successFn, errorFn) {
        params._token = window.token;

        $.ajax({
            type    : 'PUT',
            url     : '/api/user/1',
            data    : params,
            success : successFn,
            error   : errorFn
        });
    };

}