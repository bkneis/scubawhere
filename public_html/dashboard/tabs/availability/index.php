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
                    <availability-table :filter-date="date"></availability-table>
                </div>
            </div>
        </div>

    </div><!-- .row -->

    <div id="modalWindows" style="height: 0;"></div>

</div>

<template type="text/x-template" id="availability-table">
    <div class="inner">
        <div class="text-center">
            <div class="btn-group" role="group">
                <button type="button"
                        class="btn btn-default btn-primary"
                        @click="goPrev()">
                    <<
                </button>
                <button type="button"
                        class="btn btn-default btn-primary"
                        @click="goNext()">
                    >>
                </button>
            </div>
        </div>
        <table id="tbl-availability" class="ExcelTable" v-if="accommodations.length > 0">
            <thead>
            <tr class="ExcelTable_Header">
                <th class="ExcelTable_Row">Accommodation</th>
                <th v-for="date in dates"
                    class="ExcelTable_Row">
                    {{date.key}}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr height="50" v-for="accomm in accommodations">
                <td class="ExcelTable_Row"><strong>{{accomm.name}}</strong></td>
                <td v-for="date in dates"
                    class="ExcelTable_Row"
                    :style="calcStyle(accomm.id, date.key)"
                    @click="showBookingInfo(accomm.id, date.key)">
                    {{getCustomer(accomm.id, date.key)}}
                </td>
            </tr>
            </tbody>
        </table>
        <div style="text-align: center"
             v-if="!(accommodations.length > 0) && promises.accommodations">
            <h1 style="vertical-align: middle;">
                No accommodation available
            </h1>
        </div>
        <modal v-if="showCustomerInfo" :class="{ in : showCustomerInfo }">
            <template slot="header">Customer info for booking {{selectedBooking.reference}}</template>
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
                        <td>{{selectedBooking.lead_customer.firstname}} {{selectedBooking.lead_customer.lastname}}</td>
                    </tr>
                    <tr class="InfoTable_Row">
                        <td class="InfoTable_Title">Booking source : </td>
                        <td>{{bookingSource}}</td>
                    </tr>
                    <tr class="InfoTable_Row">
                        <td class="InfoTable_Title">Amount Paid : </td>
                        <td>{{currencySymbol}} {{amountPaid}}</td>
                    </tr>
                    <tr class="InfoTable_Row">
                        <td class="InfoTable_Title">Total booking Price : </td>
                        <td>{{currencySymbol}} {{bookingPrice}}</td>
                    </tr>
                    <tr class="InfoTable_Row">
                        <td class="InfoTable_Title">Outstanding : </td>
                        <td>{{currencySymbol}} {{amountOutstanding}}</td>
                    </tr>
                    <tr class="InfoTable_Row">
                        <td class="InfoTable_Title">Booking ref : </td>
                        <td>
                            <a @click="viewBooking()">{{selectedBooking.reference}}</a>
                        </td>
                    </tr>
                    <tr class="InfoTable_Row"
                        v-for="pickup in selectedBooking.pickups">
                            <td class="InfoTable_Title">Pickup : </td>
                            <td>{{pickup.location}} : {{pickup.time}}</td>
                    </tr>
                </tbody>
            </table>
        </modal>
    </div>
</template>

<template id="modal-template">
    <div id="modal" class="modal fade" tabindex="-1" role="dialog" style="display : block !important">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" @click="closeCustomerModal"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <slot name="header"></slot>
                    </h4>
                </div>
                <div class="modal-body">
                    <slot></slot>
                </div>
                <div class="modal-footer">
                    <slot name="footer">
                        <button type="button" class="btn btn-default" @click="closeCustomerModal">Close</button>
                    </slot>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</template>

<link rel="stylesheet" type="text/css" href="/dashboard/css/components/modal.css">
<link rel="stylesheet" type="text/css" href="/dashboard/css/components/availability-table.css">
<script src="/dashboard/tabs/availability/js/DateService.js"></script>
<script src="/dashboard/tabs/availability/js/AvailabilityService.js"></script>
<script src="/dashboard/tabs/availability/js/availability-table.js"></script>
<script src="/dashboard/tabs/availability/js/main.js"></script>