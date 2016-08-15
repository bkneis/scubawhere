var LogRepo = function()
{
    this.get = function( id, successFn, errorFn )
    {
        $.get( "/api/log", id, successFn, errorFn);
    };

    this.getAll = function( successFn, errorFn )
    {
        $.ajax( {
            url: "/api/log/all", 
            success : function success( data ) {
                window.logs = _.indexBy(data, "id");
                successFn(data);
            },
            error: errorFn
        });
    };

    this.delete = function( id, successFn, errorFn )
    {
        if(typeof errorFn === 'undefined') {
            var errorFn = function(xhr) {
                console.log(xhr);
                pageMsg(xhr.responseText);
            }    
        }
        $.ajax({
            url: '/api/log/delete',
            type: 'POST',
            data: {
                _token : window.token,
                id: id
            },
            success : function success(data) {
                delete window[id];
                successFn(data);
            },
            error: errorFn
        });
    };

};
