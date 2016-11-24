
new Vue({
    
    el : '#wrapper',

    data : function () {
        return {
            show : false
        };
    },

    components : {
        modal : VueStrap.modal
    }

});

// @todo move this to vue once bootstrap modals are added as components
$('#email-customer-form').on('submit', function (event) {
    event.preventDefault();
    var params = $(this).serializeArray();
    userRepo.update(params, function (res) {
        pageMssg(res.status, 'success');
    },
    function (xhr) {
        console.log(xhr);
        let res = JSON.parse(xhr.responseText);
        pageMssg(res.errors[0], 'danger');
    });
});
