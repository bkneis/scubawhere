<div id="wrapper" class="clearfix">
    <div id="tour-div" style="width:0px; height:0px; margin-left:50%;"></div>

    <div class="row">

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Availability</h4>
                </div>
                <div class="panel-body">
                    <availability-table></availability-table>
                </div>
            </div>
        </div>

    </div><!-- .row -->

</div>

<template id="availability-table">

    <table class="ExcelTable">
        <thead>
            <tr>
                <th class="ExcelTable_Row ExcelTable_Date">Date</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="accomm in accommodations">
                <tr class="ExcelTable_Row">{{acomm</tr>
            </tr>
        </tbody>
    </table>

</template>

<link rel="stylesheet" type="text/css" href="/css/components/availability-table.css">
<script src="/tabs/availability/js/main.js"></script>