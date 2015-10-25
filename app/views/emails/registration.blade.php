@extends('emails.layouts.main')

@section('title')
    <title>ScubawhereRMS Registration Confirmation</title>
@stop

@section('content')
    <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
    <td align="center" valign="top" width="100%" style="background-color: #f7f7f7;font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;padding: 20px 0 5px;" class="content-padding">
      <center style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
        <table cellspacing="0" cellpadding="0" width="600" class="w320" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;border-collapse: collapse !important;">
          <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
            <td class="header-lg" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 32px;color: #4d4d4d;text-align: center;line-height: normal;border-collapse: collapse;font-weight: 700;padding: 35px 0 0;">
              Your scubawhere registration confirmation
            </td>
          </tr>
          <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
            <td class="free-text" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;padding: 10px 60px 0 60px;width: 100% !important;">
              Thank you for registering with ScubawhereRMS! Click the button below to login and get started.
            </td>
          </tr>
          <tr style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;">
            <td class="button" style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;font-size: 14px;color: #777777;text-align: center;line-height: 21px;border-collapse: collapse;padding: 30px 0;">
              <div style="font-family: 'Open Sans', 'Helvetica Neue', 'Arial', 'sans-serif' !important;"><!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{$siteUrl}}/login" style="height:45px;v-text-anchor:middle;width:155px;" arcsize="15%" strokecolor="#ffffff" fillcolor="#4a89dc">
                  <w:anchorlock/>
                  <center style="color:#ffffff;font-family:Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;">Get Started</center>
                </v:roundrect>
                <![endif]--><a class="button-mobile" href="{{$siteUrl}}/login" style="background-color:#4a89dc;border-radius:5px;color:#ffffff;display:inline-block;font-family:'Cabin', Helvetica, Arial, sans-serif;font-size:14px;font-weight:regular;line-height:45px;text-align:center;text-decoration:none;width:155px;-webkit-text-size-adjust:none;mso-hide:all;">Get Started</a>
              </div>
            </td>
          </tr>
        </table>
      </center>
    </td>
  </tr>
@stop
