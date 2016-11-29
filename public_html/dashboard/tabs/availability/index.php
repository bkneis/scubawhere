<div id="wrapper" class="clearfix">
    <div id="tour-div" style="width:0px; height:0px; margin-left:50%;"></div>

    <div class="row">

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading" data-toggle="collapse" data-target="#filters-container">
                    <h4 class="panel-title">Availability Filters</h4>
                </div>
                <div id="filters-container" class="panel-body collapse in row">
                    <div id="jump-to-date" class="form-row col-sm-6">
                        <div class="input-group">
                            <label class="input-group-addon">Jump to : </label>
                            <input style="width:200px; float:left;"
                                   type="text"
                                   id="filter-date"
                                   class="form-control datepicker"
                                   data-date-format="YYYY-MM-DD"
                                   v-model="date"
                                   :value="date">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Availability</h4>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <availability-table :filter-date="date"></availability-table>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- .row -->

    <div id="modalWindows" style="height: 0;"></div>

</div>

<template type="text/x-template" id="availability-table">
    <div class="inner">
        <table class="ExcelTable" v-if="accommodations.length > 0">
            <thead>
                <tr class="ExcelTable_Header">
                    <th class="ExcelTable_Row ExcelTable_headcol">Date</th>
                    <th v-for="accomm in accommodations"
                        class="ExcelTable_Row">
                        {{accomm.name}}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="date in dates">
                    <td class="ExcelTable_Row ExcelTable_headcol">{{date.string}}</td>
                    <td v-for="accomm in accommodations"
                        class="ExcelTable_Row"
                        :style="calcStyle(accomm.id, date.key)"
                        @click="showBookingInfo(accomm.id, date.key)">
                        {{getCustomer(accomm.id, date.key)}}
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="text-align: center"
             v-if="! (accommodations.length > 0) && promises.accommodations">
            <h1 style="vertical-align: middle;">
                No accommodation available
            </h1>
        </div>
    </div>
</template>

<script type="text/x-handlebars" id="modal-booking-info-template">
    <div class="reveal-modal" id="modal-booking-info">
        <div class="modal-header">
            <a class="close-reveal-modal close-modal" title="Abort">&#215;</a>
            <h4 class="modal-title">Customer info for booking {{reference}}</h4>
        </div>
        <div class="modal-body">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="InfoTable_Row">
                        <td class="InfoTable_Title">Lead customer name : </td>
                        <td>{{lead_customer.firstname}} {{lead_customer.lastname}}</td>
                    </tr>
                    <tr class="InfoTable_Row">
                        <td class="InfoTable_Title">Booking source : </td>
                        <td>{{sourceString source}}</td>
                    </tr>
                    <tr class="InfoTable_Row">
                        <td class="InfoTable_Title">Amount Paid : </td>
                        <td>{{currencySymbol}} {{total payments 'amount'}}</td>
                    </tr>
                    <tr class="InfoTable_Row">
                        <td class="InfoTable_Title">Total booking Price : </td>
                        <td>{{currencySymbol}} {{getPrice this}}</td>
                    </tr>
                    <tr class="InfoTable_Row">
                        <td class="InfoTable_Title">Outstanding : </td>
                        <td>{{currencySymbol}} {{getOutstanding this}}</td>
                    </tr>
                    <tr class="InfoTable_Row">
                        <td class="InfoTable_Title">Booking ref : </td>
                        <td><a class="view-booking">{{reference}}</a></td>
                    </tr>
                    {{#each pickups}}
                        <tr class="InfoTable_Row">
                            <td class="InfoTable_Title">Pickup : </td>
                            <td>{{location}} : {{time}}</td>
                        </tr>
                    {{/each}}
                </tbody>
            </table>
        </div>
    </div>
</script>

<link rel="stylesheet" type="text/css" href="/css/components/modal.css">
<link rel="stylesheet" type="text/css" href="/css/components/availability-table.css">
<script src="/tabs/availability/js/DateService.js"></script>
<script src="/tabs/availability/js/AvailabilityService.js"></script>
<script src="/tabs/availability/js/availability-table.js"></script>
<script src="/tabs/availability/js/main.js"></script>