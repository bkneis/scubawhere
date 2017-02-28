<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @yield('title')

  <style type="text/css">

    @import  url(http://fonts.googleapis.com/css?family=Open+Sans:400,700);
    /* Take care of image borders and formatting, client hacks */
    img { max-width: 600px; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;}
    a img { border: none; }
    table { border-collapse: collapse !important;}
    table td { border-collapse: collapse; }
    .container-for-gmail-android { min-width: 600px; }

    html {
      top: 0px;
      left: 0px;
      margin: 0px;
      padding: 0px;
      -ms-text-size-adjust: 100%;
      -webkit-text-size-adjust: 100%;
      -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
    }

    body {
      -webkit-font-smoothing: antialiased;
      -webkit-text-size-adjust: none;
      width: 100% !important;
      margin: 0 !important;
      height: 100%;
      color: #333333;
      font-family: "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
      font-size: 14px;
      line-height: 21px;
      line-height: 1.42857143;
      background-color: #eeeeee;
      top: 0px;
      left: 0px;
      padding: 0px;
    }

    a {
      color: #676767;
      text-decoration: none !important;
    }

    .pull-left {
      text-align: left;
    }

    .pull-right {
      text-align: right;
    }

    .header-sm {
      font-size: 32px;
      /*font-weight: 700;*/
      line-height: normal;
      padding: 35px 0 0;
      color: #4d4d4d;
    }

    .header-sm {
      padding: 5px 0;
      font-size: 18px;
      line-height: 1.3;
    }

    .content-padding {
      padding: 20px 0 5px;
    }

    .mobile-header-padding-right {
      width: 290px;
      text-align: right;
      padding-left: 10px;
    }

    .mobile-header-padding-left {
      width: 290px;
      text-align: left;
      padding-left: 10px;
      padding-bottom: 8px;
    }

    .mini-block {
      border: 1px solid #e5e5e5;
      border-radius: 5px;
      background-color: #ffffff;
      padding: 12px 15px 15px;
      text-align: left;
      width: 253px;
    }

    .mini-container-left {
      width: 278px;
      padding: 10px 0 10px 15px;
    }

    .mini-container-right {
      width: 278px;
      padding: 10px 14px 10px 15px;
    }

    .item-table {
      width: 560px;
    }

    .item {
      width: 300px;
      padding-bottom: 5px;
    }

    .title {
      padding-bottom: 10px;
      width: 300px;
    }

    .title-dark {
      text-align: left;
      border-bottom: 1px solid #cccccc;
      color: #4d4d4d;
      font-weight: 700;
      padding-bottom: 5px;
    }

    .item-col {
      padding-top: 20px;
      text-align: left;
      vertical-align: top;
      border-bottom: 1px solid #e7e7e7;
      padding-bottom: 20px;
    }

    .item-col-inner {
      text-align: left;
      vertical-align: top;
    }

    /* Mobile styles */
    @media  only screen and (max-width: 480px) {table[class*="container-for-gmail-android"] {
        min-width: 290px !important;
        width: 100% !important;
      }

      table[class="w320"] {
        width: 320px !important;
      }

      td[class*="mobile-header-padding-left"] {
        width: 160px !important;
        padding-left: 0 !important;
      }

      td[class*="mobile-header-padding-right"] {
        width: 160px !important;
        padding-right: 0 !important;
      }

      td[class="content-padding"] {
        padding: 5px 0 5px !important;
      }

      td[class~="item"] {
        width: 140px !important;
        vertical-align: top !important;
      }

      td[class="mini-container-left"],
      td[class="mini-container-right"] {
        padding: 0 15px 15px !important;
        display: block !important;
        width: 290px !important;
      }
    }

    @font-face {
      font-family: 'Open Sans';
      src: url("http://rms.scubawhere.com/common/fonts/opensans-bold-webfont.eot");
      src: url("http://rms.scubawhere.com/common/fonts/opensans-bold-webfont.eot#iefix") format('embedded-opentype'), url("http://rms.scubawhere.com/common/fonts/opensans-bold-webfont.woff2") format('woff2'), url("http://rms.scubawhere.com/common/fonts/opensans-bold-webfont.woff") format('woff'), url("http://rms.scubawhere.com/common/fonts/opensans-bold-webfont.ttf") format('truetype'), url("http://rms.scubawhere.com/common/fonts/opensans-bold-webfont.svg#open_sansbold") format('svg');
      font-weight: bold;
      font-style: normal;
    }
    @font-face {
      font-family: 'Open Sans';
      src: url("http://rms.scubawhere.com/common/fonts/opensans-regular-webfont.eot");
      src: url("http://rms.scubawhere.com/common/fonts/opensans-regular-webfont.eot#iefix") format('embedded-opentype'), url("http://rms.scubawhere.com/common/fonts/opensans-regular-webfont.woff2") format('woff2'), url("http://rms.scubawhere.com/common/fonts/opensans-regular-webfont.woff") format('woff'), url("http://rms.scubawhere.com/common/fonts/opensans-regular-webfont.ttf") format('truetype'), url("http://rms.scubawhere.com/common/fonts/opensans-regular-webfont.svg#open_sansregular") format('svg');
      font-weight: normal;
      font-style: normal;
    }
    /*! normalize.css v3.0.2 | MIT License | git.io/normalize */
    a {
      background-color: transparent;
    }
    a:active,
    a:hover {
      outline: 0;
    }
    strong {
      font-weight: bold;
    }
    small {
      font-size: 80%;
    }
    img {
      border: 0;
    }
    table {
      border-collapse: collapse;
      border-spacing: 0;
    }
    td {
      padding: 0;
    }
    /*! Source: https://github.com/h5bp/html5-boilerplate/blob/master/src/css/main.css */
    @media  print {*,
      *:before,
      *:after {
        background: transparent !important;
        color: #000 !important;
        box-shadow: none !important;
        text-shadow: none !important;
      }
      a,
      a:visited {
        text-decoration: underline;
      }
      a[href]:after {
        content: " (" attr(href) ")";
      }
      tr,
      img {
        page-break-inside: avoid;
      }
      img {
        max-width: 100% !important;
      }}
    * {
      -webkit-box-sizing: border-box;
      -moz-box-sizing: border-box;
      box-sizing: border-box;
    }
    *:before,
    *:after {
      -webkit-box-sizing: border-box;
      -moz-box-sizing: border-box;
      box-sizing: border-box;
    }
    a {
      color: #337ab7;
      text-decoration: none;
    }
    a:hover,
    a:focus {
      color: #23527c;
      text-decoration: underline;
    }
    a:focus {
      outline: thin dotted;
      outline: 5px auto -webkit-focus-ring-color;
      outline-offset: -2px;
    }
    img {
      vertical-align: middle;
    }

    small {
      font-size: 85%;
    }

    table {
      background-color: transparent;
    }
    table td[class*="col-"] {
      position: static;
      float: none;
      display: table-cell;
    }

    .badge {
      display: inline-block;
      min-width: 10px;
      padding: 3px 7px;
      font-size: 12px;
      font-weight: bold;
      color: #ffffff;
      line-height: 1;
      vertical-align: baseline;
      white-space: nowrap;
      text-align: center;
      background-color: #777777;
      border-radius: 10px;
    }
    .badge:empty {
      display: none;
    }

    @-webkit-keyframes progress-bar-stripes {
      from {
        background-position: 40px 0;
      }
      to {
        background-position: 0 0;
      }
    }
    @keyframes  progress-bar-stripes {
      from {
        background-position: 40px 0;
      }
      to {
        background-position: 0 0;
      }
    }

    .pull-right {
      float: right !important;
    }
    .pull-left {
      float: left !important;
    }
    @-ms-viewport {
      width: device-width;
    }
    @-webkit-keyframes load8 {
      0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
      }
      100% {
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg);
      }
    }
    @-moz-keyframes load8 {
      0% {
        -moz-transform: rotate(0deg);
        transform: rotate(0deg);
      }
      100% {
        -moz-transform: rotate(360deg);
        transform: rotate(360deg);
      }
    }
    @-ms-keyframes load8 {
      0% {
        -ms-transform: rotate(0deg);
        transform: rotate(0deg);
      }
      100% {
        -ms-transform: rotate(360deg);
        transform: rotate(360deg);
      }
    }
    @-o-keyframes load8 {
      0% {
        -o-transform: rotate(0deg);
        transform: rotate(0deg);
      }
      100% {
        -o-transform: rotate(360deg);
        transform: rotate(360deg);
      }
    }
    @keyframes  load8 {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }

    /*        ELEMENT STYLES
    *******************************/
    *:disabled {
      opacity: 0.5;
    }

    a:link,
    a:visited,
    a:active,
    a:hover{
      color: #4a9cff;
      text-decoration: none;
    }

    a:hover{ text-decoration: underline; }

    table{ width: 100%; }

    /* default */
    table td {
      padding: 10px;
      vertical-align: top;
    }

    @-webkit-keyframes border-blink {
      50% { border-color: transparent; }
    }
    @-moz-keyframes border-blink {
      50% { border-color: transparent; }
    }
    @-ms-keyframes border-blink {
      50% { border-color: transparent; }
    }
    @keyframes  border-blink {
      50% { border-color: transparent; }
    }

    /* ###################### */
    /* ### SUMMARY SCREEN ### */
    /* ###################### */

    /* Take care of image borders and formatting, client hacks */
    .container-for-gmail-android {
        min-width: 600px;
    }
  </style>

</head>

<body bgcolor="#f7f7f7">
<table align="center" cellpadding="0" cellspacing="0" class="container-for-gmail-android" width="100%">
  <tr>
    <td align="left" valign="top" width="100%" style="background-color: #fff;">
      <center>
        <table cellpadding="0" cellspacing="0" width="600" height="60" class="w320">
          <tr>
            <td class="mobile-header-padding-left">
              <!--<a href="http://scubawhere.com"><img width="252" height="36" src="https://rms.scubawhere.com/img/scubawhere_logo.png" alt="logo"></a>-->
            </td>
            <td class="mobile-header-padding-right">
              <!--<a href="https://twitter.com/scubawhere"><img width="36" height="36" src="https://rms.scubawhere.com/img/twitter.png" alt="twitter"></a>
              <a href="https://facebook.com/scubawhere"><img width="36" height="36" src="https://rms.scubawhere.com/img/facebook.png" alt="facebook"></a>
              <a href="https://plus.google.com/+scubawhere-com"><img width="36" height="36" src="https://rms.scubawhere.com/img/google.png" alt="google"></a>-->
            </td>
          </tr>
        </table>
      </center>
    </td>
  </tr>

  @yield('content')

  <tr>
    <td align="center" valign="top" width="100%">
      <center>
        <table cellspacing="0" cellpadding="0" width="600" class="w320">
          <tr>
            <td align="center">
              <!--<strong>scubawhere Limited</strong>-->
            </td>
          </tr>
        </table>
      </center>
    </td>
  </tr>
</table>

</body>
</html>
