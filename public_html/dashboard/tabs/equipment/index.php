<div id="wrapper" class="clearfix">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default" id="equipment-category-list-div">
                <div class="panel-heading">
                    <h4 class="panel-title">Available Equipment Categories</h4>
                </div>
                <div class="panel-body" id="equipment-category-list-container">
                    <button id="change-to-add-equipment-category" class="btn btn-success text-uppercase">&plus; Add Equipment Category</button>
                    <script type="text/x-handlebars-template" id="equipment-category-list-template">
                        <ul id="equipment-category-list" class="entity-list">
                            {{#each equipmentCategories}}
                            <li class="" data-id="{{id}}"><strong>{{{name}}}</strong></li>
                            {{else}}
                            <p id="no-equipment-category">No equipment categories available.</p>
                            {{/each}}
                        </ul>
                    </script>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="panel panel-default" id="equipment-category-form-container">
                <script type="text/x-handlebars-template" id="equipment-category-form-template">
                    <div class="panel-heading">
                        <h4 class="panel-title">{{category.task}} Equipment Category</h4>
                    </div>
                    <div class="panel-body">
                        <form id="{{category.task}}-equipment-category-form">
                            <div class="form-row">
                                {{#if category.update}}
                                    <span class="btn btn-danger pull-right remove-equipment-category">Remove</span> 
                                {{/if}}
                                <label class="field-label">Equipment Category Name</label>
                                <input id="equipment-category-name" type="text" name="name" value="{{{category.name}}}">
                            </div>

                            <div class="form-row">
                                <label class="field-label">Equipment Category Description</label>
                                <textarea id="equipment-category-description" name="description" style="height: 243px;">{{{category.description}}}</textarea>
                            </div>

                            <div class="form-row">
                                <button id="manage-equipment" data-id="{{category.id}}" class="btn btn-md btn-success">Manage Equipment</button>
                            </div>

                            <div class="form-row" id="equipmentList">
                                {{#if category.update}}
                                <h4>Equipment items within this category:</h4> {{#each equipment}}
                                <label class="location">
                                    <strong>Size : {{size}}</strong>
                                    <br> Quantity : {{quantity}}
                                </label>
                                {{else}}
                                <div class="alert alert-danger clearfix">
                                    <i class="fa fa-exclamation-triangle fa-3x fa-fw pull-left"></i>
                                    <p class="pull-left">
                                        <strong>There are no equipment in this category!</strong>
                                        <br>
                                    </p>
                                </div>
                                {{/each}} {{/if}}
                            </div>

                            <div class="form-row" style="float:left" id="equipmentPrices">
                                <p><strong>Set rental prices for this equipment category:</strong></p>
                                {{price_inputs category.prices}}
                                <button id="add-equipment-price" type="button" class="btn btn-default btn-sm"> &plus; Add another rental price</button>
                            </div>

                            {{#if category.update}}
                            <input id="category-id" type="hidden" name="id" value="{{category.id}}"> {{/if}}

                            <input type="hidden" name="_token">

                            <input type="submit" class="btn btn-lg btn-primary text-uppercase pull-right" id="{{category.task}}-equipment-category" value="SAVE">

                        </form>
                    </div>
                </script>
            </div>
        </div>
    </div>
    <!-- .row -->

    <div class="modal fade" id="manage-equipment-modal">
        <div class="modal-dialog" style="width:60%">
            <div class="modal-content">
                <div class="modal-header">
                    <!--<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>-->
                    <h4 class="modal-title">Manage Equipment</h4>
                </div>
                <div class="modal-body">
                    <button id="add-equipment-item" style="margin-bottom:20px" class="btn btn-success">Add equipment item</button>
                    <form id="equipment-form" accept-charset=utf-8>
                        <table id="equipment-table">
                            <thead>
                                <tr>
                                    <th style="padding-left: 10px;">ID</th>
                                    <th style="padding-left: 10px;">Size</th>
                                    <th style="padding-left: 10px;">Serviced Expiry Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="equipment-items" class="sw-blue-table">
                            </tbody>
                        </table>
                        <button type="button" style="margin-top:10px" class="btn btn-primary pull-right" id="update-equipment">SAVE</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/x-handlebars-template" id="equipment-table-template">
        {{#each equipment}}
        <tr class="equipment-item-details">
            <td>
                <input type="text" data-id="{{id}}" class="uuid" name="uuid" value="{{uuid}}">
            </td>
            <td>
                <input type="text" name="size" class="size" value="{{size}}">
            </td>
            <td>
                <input type="text" name="service_date" class="datepicker" data-date-format="YYYY-MM-DD" value="{{service_date}}">
            </td>
            <td>
                <button type="button" class="btn btn-danger delete-equipment" data-id="{{id}}">Delete</button>
            </td>
        </tr>
        {{/each}}
    </script>

    <script type="text/x-handlebars-template" id="equipment-items-list-template">
        <h4>Equipment items within this category:</h4> {{#each equipment}}
        <label class="location">
            <strong>Size : {{size}}</strong>
            <br> Quantity : {{quantity}}
        </label>
        {{else}}
        <div class="alert alert-danger clearfix">
            <i class="fa fa-exclamation-triangle fa-3x fa-fw pull-left"></i>
            <p class="pull-left">
                <strong>No equipment selected!</strong>
                <br>
            </p>
        </div>
        {{/each}}
    </script>

    <script type="text/x-handlebars-template" id="equipment-row-template">
        <tr class="equipment-item-details">
            <td>
                <input type="text" class="uuid" name="uuid">
            </td>
            <td>
                <input type="text" class="size" name="size">
            </td>
            <td>
                <input type="text" name="last_serviced" class="datepicker" data-date-format="YYYY-MM-DD">
            </td>
            <td>
                <button type="button" class="btn btn-danger delete-equipment">Delete</button>
            </td>
        </tr>
    </script>

    <script type="text/x-handlebars-template" id="price-input-template">
        {{#if prices}} {{#each prices}}
        <fieldset class="equipment-item-prices" style="margin-bottom:10px">
            <p>
                <span class="currency">{{currency}}</span>
                <input type="number" class="equipment-price" value="{{price}}" placeholder="00.00" min="0" step="0.01" style="width: 100px;">
                <span class="currency"> rental period</span>
                <input type="number" class="equipment-price-length" value="{{duration}}" placeholder="0" min="0" step="0.5" style="width: 100px;">
                <span class="currency"> hours</span>
                <button type="button" class="btn btn-danger remove-price">&#215;</button>
            </p>
        </fieldset>
        {{/each}} {{else}}
        <fieldset class="equipment-item-prices" style="margin-bottom:10px">
            <p>
                <span class="currency">{{currency}}</span>
                <input type="number" class="equipment-price" placeholder="00.00" min="0" step="0.01" style="width: 100px;">
                <span class="currency"> rental period</span>
                <input type="number" class="equipment-price-length" placeholder="0" min="0" step="0.5" style="width: 100px;">
                <span class="currency"> hours</span>
                <button type="button" class="btn btn-danger remove-price">&#215;</button>
            </p>
        </fieldset>
        {{/if}}
    </script>

    <script type="text/x-handlebars-template" id="errors-template">
        <div class="yellow-helper errors" style="color: #E82C0C;">
            <strong>There are a few problems with the form:</strong>
            <ul>
                {{#each errors}}
                <li>{{this}}</li>
                {{/each}}
            </ul>
        </div>
    </script>

    <script src="/tabs/equipment/js/script.js"></script>
</div>