var Equipment = {

    get: function (params, handleData) {
        $.get("/api/equipment-category", params, function (data) {
            handleData(data);
        });
    },

    getAll: function (handleData) {
        $.get("/api/equipment-category/all", function (data) {
            handleData(data);
        });
    },
    create: function (params, handleData, errorFn) {
        $.ajax({
            type: "POST",
            url: "/api/equipment/add",
            data: params,
            success: handleData,
            error: errorFn
        });
    },
    delete: function (params, handleData, errorFn) {
        $.ajax({
            type: "POST",
            url: "/api/equipment/delete",
            data: params,
            success: handleData,
            error: errorFn
        });
    },
    createCategory: function (params, handleData, errorFn) {
        $.ajax({
            type: "POST",
            url: "/api/equipment-category/add",
            data: params,
            success: handleData,
            error: errorFn
        });
    },

    updateCategory: function (params, handleData, errorFn) {
        $.ajax({
            type: "POST",
            url: "/api/equipment-category/edit",
            data: params,
            success: handleData,
            error: errorFn
        });
    },

    deleteCategory: function (params, handleData, errorFn) {
        $.ajax({
            type: "POST",
            url: "/api/equipment-category/delete",
            data: params,
            success: handleData,
            error: errorFn
        });
    }

};