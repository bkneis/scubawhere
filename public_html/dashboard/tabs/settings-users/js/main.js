
new Vue({
    
    el : '#wrapper',

    data : function () {
        return {
            show : false
        };
    }

});

// @todo move this to vue once bootstrap modals are added as components
$('#email-customer-form').on('submit', function (event) {
    event.preventDefault();
    var params = $(this).serializeArray();
    userRepo.update(params, function (res) {
        pageMssg(res.status, 'success');
        $('#modal-update-user').modal('hide');
        location.reload();
    },
    function (xhr) {
        console.log(xhr);
        let res = JSON.parse(xhr.responseText);
        pageMssg(res.errors[0], 'danger');
    });
});
