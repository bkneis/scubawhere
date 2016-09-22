@extends('emails.layouts.main')

@section('title')
    <title>Refund Confirmation</title>
@stop

@section('content')
    <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
    <td align="center" valign="top" width="100%" style="background-color: #f7f7f7;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;padding: 20px 0 5px;" class="content-padding">
      <center style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
        <table cellspacing="0" cellpadding="0" width="600" class="w320" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;border-collapse: collapse !important;">
          <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
            <td class="header-lg" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 32px;color: #4d4d4d;text-align: center;line-height: normal;border-collapse: collapse;font-weight: 700;padding: 35px 0 0;">
              Your refund confirmation
            </td>
          </tr>
          <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
            <td class="free-text" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;padding: 10px 60px 0 60px;width: 100% !important;">
              A refund has been added to your booking. The details of the refund are below. 
            </td>
          </tr>
          <tr style="font-family: 'Oxygen', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
            <td class="mini-block-container" style="font-family: 'Oxygen', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;padding: 30px 50px;width: 500px;">
              <table cellspacing="0" cellpadding="0" width="100%" style="border-collapse: separate !important;font-family: 'Oxygen', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                <tbody style="font-family: 'Oxygen', 'Helvetica Neue', 'Arial', 'sans-serif' !important;"><tr style="font-family: 'Oxygen', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                  <td class="mini-block" style="font-family: 'Oxygen', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;background-color: #ffffff;width: 498px;border: 1px solid #e1e1e1;border-radius: 5px;padding: 35px 70px;">
                    <table cellpadding="0" cellspacing="0" width="100%" style="font-family: 'Oxygen', 'Helvetica Neue', 'Arial', 'sans-serif' !important;border-collapse: collapse !important;">
                      <tbody style="font-family: 'Oxygen', 'Helvetica Neue', 'Arial', 'sans-serif' !important;"><tr style="font-family: 'Oxygen', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                        <td style="padding-bottom: 30px;font-family: 'Oxygen', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: left;line-height: 21px;border-collapse: collapse;">
                          Booking Reference: {{$refund->booking->reference}}<br>
                          Received At: {{$refund->received_at}}<br>
                          Amount: {{$refund->currency->symbol}}{{$refund->amount}}<br>
                          Type: {{$refund->paymentgateway->name}}<br>
                        </td>
                      </tr>
                      <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
                        <td class="button" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;padding: 30px 0;">
                          <div style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;"></div>
                        </td>
                      </tr>
                    </tbody></table>
                  </td>
                </tr>
              </tbody></table>
            </td>
          </tr>
        </table>
      </center>
    </td>
  </tr>
@stop
