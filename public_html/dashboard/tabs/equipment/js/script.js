var equipmentCategoriesForm,
    equipmentCategoriesList,
    equipmentItemRow,
    equipmentItemList;

priceInputTemplate = Handlebars.compile($('#price-input-template').html());

Handlebars.registerHelper('price_inputs', function (data) {
    return new Handlebars.SafeString(priceInputTemplate({
        prices: data
    }));
});

Handlebars.registerHelper('currency', function () {
    return window.company.currency.symbol;
});

$(function () {

    // Render initial addon list
    equipmentCategoriesList = Handlebars.compile($("#equipment-category-list-template").html());
    equipmentCategoriesForm = Handlebars.compile($("#equipment-category-form-template").html());
    equipmentItemRow = Handlebars.compile($("#equipment-row-template").html());
    equipmentTable = Handlebars.compile($("#equipment-table-template").html());
    equipmentItemList = Handlebars.compile($("#equipment-items-list-template").html());
    loadEquipmentCategory(); // Automatically renders the views when data is loaded

    $('#equipment-category-list-container').on('click', 'li', function (event) {
        if ($(event.target).is('strong')) event.target = event.target.parentNode;
        renderEditForm(event.target.getAttribute('data-id'));
    });

    $("#equipment-category-list-container").on('click', '#change-to-add-equipment-category', function (event) {
        event.preventDefault();
        renderEditForm();
    });

    $('#add-equipment-item').on('click', function (event) {
        event.preventDefault();
        $('#equipment-items').append(equipmentItemRow());
        initPriceDatepickers();
    });

    $('#equipment-category-form-container').on('click', '#add-equipment-price', function (event) {
        event.preventDefault();
        $(event.target).before(priceInputTemplate());
    });

    $('#equipment-category-form-container').on('click', '.remove-price', function (event) {
        event.preventDefault();
        $(event.target).parent().parent().remove();
    });

    $('#equipment-category-form-container').on('click', '#manage-equipment', function (event) {
        event.preventDefault();
        var equipmentItems = createEquipment();
        $('#equipment-items').empty();
        $('#equipment-items').append(equipmentTable({
            equipment: equipmentItems
        }));
        initPriceDatepickers();
        $('#manage-equipment-modal').modal('show');
    });

    $("#equipment-category-form-container").on('submit', '#add-equipment-category-form', function (event) {

        event.preventDefault();

        // Show loading indicator
        $('#add-equipment-category').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

        var params = {
            _token: getToken(),
            name: $("#equipment-category-name").val(),
            description: $("#equipment-category-description").val(),
            equipment: createEquipment(),
            prices: createPrices()
        };
        Equipment.createCategory(params, function success(data) {

            pageMssg(data.status, true);
            $('#add-equipment-category').prop('disabled', false);
            $('#add-equipment-category-form').find('#save-loader').remove();
            $('form').data('hasChanged', false);
            loadEquipmentCategory(data.id);

        }, function error(xhr) {

            var data = JSON.parse(xhr.responseText);
            console.log(xhr);

            if (data.errors.length > 0) {

                var errorsHTML = Handlebars.compile($("#errors-template").html());
                errorsHTML = errorsHTML(data);

                // Render error messages
                $('.errors').remove();
                $('#add-equipment-category-form').prepend(errorsHTML);
            } else {
                alert(xhr.responseText);
            }

            pageMssg('Oops, something wasn\'t quite right');

            $('#add-equipment-category').prop('disabled', false);
            $('#add-equipment-category-form').find('#save-loader').remove();
        });
    });

    $("#equipment-category-form-container").on('submit', '#update-equipment-category-form', function (event) {

        event.preventDefault();

        // Show loading indicator
        $('#update-equipment-category').prop('disabled', true).after('<div id="save-loader" class="loader"></div>');
        console.log('id', $('#category-id').val());
        var params = {
            _token: getToken(),
            id: $('#category-id').val(),
            name: $("#equipment-category-name").val(),
            description: $("#equipment-category-description").val(),
            equipment: createEquipment(),
            prices: createPrices()
        };
        console.log(params);

        Equipment.updateCategory(params, function success(data) {

            pageMssg(data.status, true);
            $('#add-equipment-category').prop('disabled', false);
            $('#add-equipment-category-form').find('#save-loader').remove();
            $('form').data('hasChanged', false);
            loadEquipmentCategory(params.id);

        }, function error(xhr) {

            var data = JSON.parse(xhr.responseText);
            console.log(xhr);

            if (data.errors.length > 0) {

                var errorsHTML = Handlebars.compile($("#errors-template").html());
                errorsHTML = errorsHTML(data);

                // Render error messages
                $('.errors').remove();
                $('#update-equipment-category-form').prepend(errorsHTML);
            } else {
                alert(xhr.responseText);
            }

            pageMssg('Oops, something wasn\'t quite right');

            $('#update-equipment-category').prop('disabled', false);
            $('#save-loader').remove();
        });
    });

    $('#equipment-category-form-container').on('click', '.remove-equipment-category', function (event) {
        event.preventDefault();
        var check = confirm('Do you really want to remove this equipment category?');
        if (check) {
            // Show loading indicator
            $(this).prop('disabled', true).after('<div id="save-loader" class="loader"></div>');

            var id = $('#update-equipment-category-form input[name=id]').val();

            Equipment.deleteCategory({
                'id': id,
                '_token': $('[name=_token]').val()
            }, function success(data) {

                pageMssg(data.status, true);
                loadEquipmentCategory();

            }, function error(xhr) {

                pageMssg('Oops, something wasn\'t quite right');

                $('.remove-equipment-category').prop('disabled', false);
                $('#save-loader').remove();
            });
        }
    });

    $('#manage-equipment-modal').on('click', '#update-equipment', function (event) {
        event.preventDefault();

        var equipment = createEquipment();

        for (var k = 0; k < equipment.length; k++) {
            if (equipment[k].size == null || equipment[k].size == "" || equipment[k].size == undefined) {
                pageMssg('The size field is required');
                return;
            }
        }
        var aggregatedEquipment = [];

        // combine equipment that have the same size and add the quantity to display to user
        for (var i = 0; i < equipment.length; i++) {
            var equipmentItem = {};
            equipmentItem.quantity = 1;
            equipmentItem.size = equipment[i].size;
            for (var j = 0; j < aggregatedEquipment.length; j++) {
                if (equipmentItem.size == aggregatedEquipment[j].size) {
                    equipmentItem.quantity++;
                    aggregatedEquipment[j].quantity++;
                    break;
                }
            }
            if (equipmentItem.quantity < 2) aggregatedEquipment.push(equipmentItem);
        }

        $('#equipmentList').empty().append(equipmentItemList({
            equipment: aggregatedEquipment
        }));

        $('#manage-equipment-modal').modal('hide');

    });

    $('#manage-equipment-modal').on('click', '.delete-equipment', function (event) {
        if ($(event.target).attr('data-id') != undefined || $(event.target).attr('data-id') != null) {
            var params = {
                _token: getToken(),
                id: $(event.target).attr('data-id')
            };
            Equipment.delete(params,
                function success(data) {
                    console.log(data);
                    pageMssg(data.status, true);
                    $(event.target).closest('tr').remove();
                },
                function error(xhr) {
                    console.log(xhr);
                });

        } else {
            $(event.target).closest('tr').remove();
        }
    });

});

function loadEquipmentCategory(id) {

    $('#equipment-category-list-container').append('<div id="save-loader" class="loader" style="margin: auto; display: block;"></div>');

    Equipment.getAll(function success(data) {
        window.equipmentCategories = _.indexBy(data, 'id');

        $('#equipment-category-list').remove();
        $('#equipment-category-list-container .loader').remove();

        $("#equipment-category-list-container").append(equipmentCategoriesList({
            equipmentCategories: window.equipmentCategories
        }));

        // (Re)Assign eventListener for clicks
        $('#equipment-categories-list').on('click', 'li, strong', function (event) {
            if ($(event.target).is('strong'))
                event.target = event.target.parentNode;

            renderEditForm(event.target.getAttribute('data-id'));
        });

        renderEditForm(id);
    });
}

function renderEditForm(id) {

    if (unsavedChanges()) {
        var question = confirm("ATTENTION: All unsaved changes will be lost!");
        if (!question) {
            return false;
        }
    }

    var equipmentCategory;
    var equipmentItems = [];

    if (id) {
        equipmentCategory = window.equipmentCategories[id];
        equipmentCategory.task = 'update';
        equipmentCategory.update = true;
        for (var i = 0; i < equipmentCategory.equipment.length; i++) {
            var equipment_item = {};
            equipment_item.quantity = 1;
            equipment_item.size = equipmentCategory.equipment[i].size;
            for (var j = 0; j < equipmentItems.length; j++) {
                if (equipment_item.size == equipmentItems[j].size) {
                    equipmentItems[j].quantity++;
                    equipment_item.quantity++;
                }
            }
            if (equipment_item.quantity < 2) equipmentItems.push(equipment_item);
        }
        $('#equipment-items').empty();
        console.log('place them', equipmentCategory.equipment);
        $('#equipment-items').append(equipmentTable({
            equipment: equipmentCategory.equipment
        }));

    } else {
        equipmentCategory = {
            task: 'add',
            update: false
        };
        $('#equipment-items').empty();
    }

    $('#equipment-category-form-container').empty().append(equipmentCategoriesForm({
        category: equipmentCategory,
        equipment: equipmentItems
    }));

    if (!id) $('input[name=name]').focus();

    CKEDITOR.replace('description');

    setToken('[name=_token]');

    // Set up change monitoring
    $('form').on('change', 'input, select, textarea', function () {
        $('form').data('hasChanged', true);
    });

}

function createEquipment() {

    var equipment = [];

    $('.equipment-item-details').each(function (value) {
        var equipment_item = {};
        equipment_item.uuid = $(this).find(".uuid").val();
        equipment_item.size = $(this).find(".size").val();
        if ($(this).find(".datepicker").val() != "0000-00-00") equipment_item.service_date = $(this).find(".datepicker").val();
        if ($(this).find(".uuid").attr('data-id') != undefined) equipment_item.id = $(this).find(".uuid").attr('data-id'); // change to hidden input
        equipment.push(equipment_item);
    });

    return equipment; // maybe get array value and return that? change conteollr to only use INput::only('equipment)
}

function createPrices() {

    var prices = [];

    $('.equipment-item-prices').each(function (value) {
        var price = {};
        price.price = parseFloat($(this).find('.equipment-price').val());
        price.duration = parseFloat($(this).find('.equipment-price-length').val());
        prices.push(price);
    });

    return prices;

}

function unsavedChanges() {
    return $('form').data('hasChanged');
}

function clearForm() {

    var equipmentCategory;

    equipmentCategory = {
        task: 'add',
        update: false
    };

    $('#equipment-category-form-container').empty().append(equipmentCategoriesForm(equipmentCategory));

    $('input[name=name]').focus();

    CKEDITOR.replace('description');

    setToken('[name=_token]');

    // Set up change monitoring
    $('form').on('change', 'input, select, textarea', function () {
        $('form').data('hasChanged', true);
    });
}