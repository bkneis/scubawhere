@extends('emails.layouts.main')

@section('title')
    <title>ScubawhereRMS Booking Summary</title>
@stop

@section('content')
    <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
    <td align="center" valign="top" width="100%" style="background-color: #f7f7f7;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;padding: 20px 0 5px;" class="content-padding">
      <center style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
        <table cellspacing="0" cellpadding="0" width="600" class="w320" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;border-collapse: collapse !important;">
          <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
            <td class="header-lg" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 32px;color: #4d4d4d;text-align: center;line-height: normal;border-collapse: collapse;font-weight: 700;padding: 35px 0 0;">
              Your scubawhere booking summary
            </td>
          </tr>
          <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
            <td class="free-text" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;padding: 10px 60px 0 60px;width: 100% !important;">
              The details for your order with reference is below.
            </td>
          </tr>
          <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
            <td class="button" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;padding: 30px 0;">
              <div style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;"><!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{$siteUrl}}/#manage-bookings" style="height:45px;v-text-anchor:middle;width:155px;" arcsize="15%" strokecolor="#ffffff" fillcolor="#4a89dc">
                  <w:anchorlock/>
                  <center style="color:#ffffff;font-family:Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;">My Account</center>
                </v:roundrect>
                <![endif]--><a class="button-mobile" href="{{$siteUrl}}/#manage-bookings" style="background-color:#4a89dc;border-radius:5px;color:#ffffff;display:inline-block;font-family:'Cabin', Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;line-height:45px;text-align:center;text-decoration:none;width:155px;-webkit-text-size-adjust:none;mso-hide:all;">View Booking</a>
              </div>
            </td>
          </tr>
          <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
            <td class="w320" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;">
              <table cellpadding="0" cellspacing="0" width="100%" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;border-collapse: collapse !important;">
                <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                  <td class="mini-container-left" style="width: 278px;padding: 10px 0 10px 15px;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;">
                    <table cellpadding="0" cellspacing="0" width="100%" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;border-collapse: collapse !important;">
                      <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                        <td class="mini-block-padding" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;">
                          <table cellspacing="0" cellpadding="0" width="100%" style="border-collapse: separate !important;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                            <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                              <td class="mini-block" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;border: 1px solid #e5e5e5;border-radius: 5px;background-color: #ffffff;padding: 12px 15px 15px;width: 253px;">
                                <span class="header-sm" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 18px;font-weight: 700;line-height: 1.3;padding: 5px 0;color: #4d4d4d;">Lead Customer</span><br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                                {{{$booking->lead_customer->firstname}}} {{{$booking->lead_customer->lastname}}} <br>
                                @if ($booking->lead_customer->address_1)
                                  {{{$booking->lead_customer->address_1}}} <br>
                                @endif
                                @if ($booking->lead_customer->city)
                                  {{{$booking->lead_customer->city}}},
                                @endif
                                @if ($booking->lead_customer->county)
                                  {{{$booking->lead_customer->county}}},
                                @endif
                                @if ($booking->lead_customer->postcode)
                                  {{{$booking->lead_customer->postcode}}}
                                @endif
                                <br>{{{$booking->lead_customer->country->name}}}
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                  <td class="mini-container-right" style="width: 278px;padding: 10px 14px 10px 15px;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;">
                    <table cellpadding="0" cellspacing="0" width="100%" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;border-collapse: collapse !important;">
                      <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                        <td class="mini-block-padding" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;">
                          <table cellspacing="0" cellpadding="0" width="100%" style="border-collapse: separate !important;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                            <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                              <td class="mini-block" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;border: 1px solid #e5e5e5;border-radius: 5px;background-color: #ffffff;padding: 12px 15px 15px;width: 253px;">
                                <span class="header-sm" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 18px;font-weight: 700;line-height: 1.3;padding: 5px 0;color: #4d4d4d;">Booking Date</span><br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                                {{$booking->created_at}} <br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                                <br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                                <span class="header-sm" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 18px;font-weight: 700;line-height: 1.3;padding: 5px 0;color: #4d4d4d;">Booking Reference</span> <br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                                {{$booking->reference}}
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </center>
    </td>
  </tr>
  <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
    <td align="center" valign="top" width="100%" style="background-color: #ffffff;border-top: 1px solid #e5e5e5;border-bottom: 1px solid #e5e5e5;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;">
      <center style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
        <table cellpadding="0" cellspacing="0" width="600" class="w320" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;border-collapse: collapse !important;">
            <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
              <td class="item-table" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;padding: 50px 20px;width: 560px;">
                <table cellspacing="0" cellpadding="0" width="100%" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;border-collapse: collapse !important;">
                  <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                    <td class="title-dark" width="120" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #4d4d4d;text-align: left;line-height: 21px;border-collapse: collapse;border-bottom: 1px solid #cccccc;font-weight: 700;padding-bottom: 5px;">
                       Trip
                    </td>
                    <td class="title-dark" width="340" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #4d4d4d;text-align: left;line-height: 21px;border-collapse: collapse;border-bottom: 1px solid #cccccc;font-weight: 700;padding-bottom: 5px;">
                      
                    </td>
                    <td class="title-dark" width="100" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #4d4d4d;text-align: left;line-height: 21px;border-collapse: collapse;border-bottom: 1px solid #cccccc;font-weight: 700;padding-bottom: 5px;">
                      Total
                    </td>
                  </tr>

                  @foreach ($booking->bookingdetails as $bookingdetail)
                    <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                      <td class="item-col" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;padding-top: 20px;vertical-align: top;border-bottom: 1px solid #e7e7e7;padding-bottom: 20px;">
                        <table cellspacing="0" cellpadding="0" width="100%" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;border-collapse: collapse !important;">
                          <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                            <td class="item-col-inner title" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;padding-bottom: 10px;width: 300px;vertical-align: top;">
                              <span style="color: #4d4d4d;font-weight: bold;font-size: 17px;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">{{$bookingdetail->session->trip->name}}</span>
                            </td>
                          </tr>
                          <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                            <td class="item-col-inner item" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;width: 300px;padding-bottom: 5px;vertical-align: top;">
                              <span style="color: #4d4d4d;font-weight: bold;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">Customer:</span> {{{$bookingdetail->customer->firstname}}} {{{$bookingdetail->customer->lastname}}} <br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                            </td>
                          </tr>
                          <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                            <td class="item-col-inner item" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;width: 300px;padding-bottom: 5px;vertical-align: top;">
                              <span style="color: #4d4d4d;font-weight: bold;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">Ticket/Package</span> <br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;"> 
                              @if (count($bookingdetail->package) > 0)
                                {{{$bookingdetail->package->name}}}  | {{$company->currency->symbol}}{{{$bookingdetail->package->decimal_price}}}<br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                              @else
                                {{{$bookingdetail->ticket->name}}}  | {{$company->currency->symbol}}{{{$bookingdetail->ticket->decimal_price}}}<br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                              @endif
                            </td>
                          </tr>
                        </table>
                      </td>
                      <td class="item-col" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;padding-top: 20px;vertical-align: top;border-bottom: 1px solid #e7e7e7;padding-bottom: 20px;">
                        <table cellspacing="0" cellpadding="0" width="100%" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;border-collapse: collapse !important;">
                          <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                            <td class="item-col-inner title" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;padding-bottom: 10px;width: 300px;vertical-align: top;">
                              &nbsp;
                            </td>
                          </tr>
                          @if ($bookingdetail->addons)
                            <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                              <td class="item-col-inner item" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;width: 300px;padding-bottom: 5px;vertical-align: top;">
                                <span style="color: #4d4d4d;font-weight: bold;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">Addons</span> <br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                                @foreach ($bookingdetail->addons as $addon)
                                  {{$addon->name}} | {{$company->currency->symbol}}{{$addon->decimal_price}}<br>
                                @endforeach
                              </td>
                            </tr>
                          @endif
                          @if ($bookingdetail->course)
                            <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                              <td class="item-col-inner item" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;width: 300px;padding-bottom: 5px;vertical-align: top;">
                                <span style="color: #4d4d4d;font-weight: bold;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">Course</span> <br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                                {{$bookingdetail->course->name}} | {{$company->currency->symbol}}{{$bookingdetail->course->decimal_price}}
                              </td>
                            </tr>
                          @endif
                        </table>
                      </td>
                      <td class="item-col" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;padding-top: 20px;vertical-align: top;border-bottom: 1px solid #e7e7e7;padding-bottom: 20px;">
                        N/A
                      </td>
                    </tr>
                  @endforeach

                  @foreach ($booking->accommodations as $accommodation)
                  <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                    <td class="item-col-last item" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;width: 300px;padding-bottom: 5px;padding-top: 20px;vertical-align: top;">
                      <span style="color: #4d4d4d;font-weight: bold;font-size: 17px;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">{{$accommodation->name}}</span>
                    </td>
                    <td class="item-col-last quantity" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;padding-top: 20px;vertical-align: top;">
                      {{$accommodation->pivot->start}} - {{$accommodation->pivot->end}}
                    </td>
                    <td class="item-col-last" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;padding-top: 20px;vertical-align: top;">
                      {{$company->currency->symbol}}{{$accommodation->decimal_price}}
                    </td>
                  </tr>
                  @endforeach


                  <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                    <td class="item-col item mobile-row-padding" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;width: 300px;padding-bottom: 20px;padding-top: 20px;vertical-align: top;border-bottom: 1px solid #e7e7e7;"></td>
                    <td class="item-col quantity" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;padding-top: 20px;vertical-align: top;border-bottom: 1px solid #e7e7e7;padding-bottom: 20px;"></td>
                    <td class="item-col price" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;padding-top: 20px;vertical-align: top;border-bottom: 1px solid #e7e7e7;padding-bottom: 20px;"></td>
                  </tr>


                  <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                    <td class="item-col item" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;width: 300px;padding-bottom: 20px;padding-top: 20px;vertical-align: top;border-bottom: 1px solid #e7e7e7;">
                    </td>
                    <td class="item-col quantity" style="text-align: right;padding-right: 10px;border-top: 1px solid #cccccc;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;line-height: 21px;border-collapse: collapse;padding-top: 20px;vertical-align: top;border-bottom: 1px solid #e7e7e7;padding-bottom: 20px;">
                      <span class="total-space" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;padding-bottom: 8px;display: inline-block;">Subtotal</span> <br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                      <span class="total-space" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;padding-bottom: 8px;display: inline-block;">Discount</span> <br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                      <span class="total-space" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;padding-bottom: 8px;display: inline-block;">Tax</span>  <br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                      <span class="total-space" style="font-weight: bold;color: #4d4d4d;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;padding-bottom: 8px;display: inline-block;">Total</span>
                    </td>
                    <td class="item-col price" style="text-align: left;border-top: 1px solid #cccccc;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;line-height: 21px;border-collapse: collapse;padding-top: 20px;vertical-align: top;border-bottom: 1px solid #e7e7e7;padding-bottom: 20px;">
                    <span class="total-space" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;padding-bottom: 8px;display: inline-block;">{{$company->currency->symbol}}{{$booking->decimal_price + $booking->discount}}</span> <br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                      <span class="total-space" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;padding-bottom: 8px;display: inline-block;">{{$company->currency->symbol}}{{$booking->discount}}</span> <br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                      <span class="total-space" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;padding-bottom: 8px;display: inline-block;">N/A</span>  <br style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                      <span class="total-space" style="font-weight: bold;color: #4d4d4d;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;padding-bottom: 8px;display: inline-block;">{{$company->currency->symbol}}{{$booking->decimal_price}}</span>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

        </table>
      </center>
    </td>
  </tr>
  <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
    <td align="center" valign="top" width="100%" style="background-color: #f7f7f7;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;padding: 20px 0 5px;" class="content-padding">
      <center style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
        <table cellspacing="0" cellpadding="0" width="600" class="w320" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;border-collapse: collapse !important;">
          <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
            <td class="button" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;padding: 30px 0;">
              <div style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;"><!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{$siteUrl}}/#manage-bookings" style="height:45px;v-text-anchor:middle;width:155px;" arcsize="15%" strokecolor="#ffffff" fillcolor="#4a89dc">
                  <w:anchorlock/>
                  <center style="color:#ffffff;font-family:Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;">More Details</center>
                </v:roundrect>
              <![endif]--><a href="{{$siteUrl}}/#manage-bookings" style="background-color:#4a89dc;border-radius:5px;color:#ffffff;display:inline-block;font-family:'Cabin', Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;line-height:45px;text-align:center;text-decoration:none;width:155px;-webkit-text-size-adjust:none;mso-hide:all;">View Booking</a></div>
            </td>
          </tr>
        </table>
      </center>
    </td>
  </tr>
@stop