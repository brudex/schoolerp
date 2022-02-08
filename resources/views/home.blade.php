<!DOCTYPE html>
<html lang="en" @if((!cache('direction') && config('config.direction') == 'rtl') || cache('direction') == 'rtl') dir="rtl" @endif>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="ScriptMint">
        <title>{{config('app.name') ? : 'InstiKit'}}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="shortcut icon" href="{{ config('config.icon') ? url('/'.config('config.icon')) : url('/images/favicon.png') }}">
        @if((!cache('direction') && config('config.direction') == 'rtl') || cache('direction') == 'rtl')
            <link href="{{ mix('/css/style-rtl.css') }}" id="direction" rel="stylesheet">
        @else
            <link href="{{ mix('/css/style.css') }}" id="direction" rel="stylesheet">
        @endif
        <link href="{{ mix('/css/colors/'.(config('config.color_theme') ? : 'blue').'.css') }}" id="theme" rel="stylesheet">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        @if(config('app.mode') != 'live')
            <style>
                @media only screen and (min-width: 600px) {
                    .topheader {
                        top: 40px !important;
                    }
                    .topbar {
                        top: 40px;
                    }
                    .page-wrapper-header{
                        margin-top: 40px;
                    }
                    .page-title {
                        margin-top: 0px !important;
                    }
                    .topbar-content {
                        position: fixed;
                        top: 0;
                        width: 100%;
                        background-color:#000000;
                        z-index:99999999;
                        text-align:center;
                        color: #ffffff;
                        padding: 10px 50px;
                    }
                }
            </style>
        @endif
        <style>
            .main-banner {
                margin-top: 100px;
            }
        </style>
    </head>
    <body class="fix-header fix-sidebar">
        @if(config('app.mode') != 'live')
            @php
                $finishTime = Carbon\Carbon::parse('2020-01-26 19:59');
                $diff = $finishTime->diffInHours(Carbon\Carbon::now());

                if (! $diff) {
                    $diff = $finishTime->diffInMinutes(Carbon\Carbon::now()).' minutes';
                }
                else {
                    $diff .= ' hours';
                }
            @endphp
            @if(Carbon\Carbon::now() <= $finishTime)
                <div class="topbar-content d-none d-sm-block">Flash Sales: <strong>30% Discount on Regular, 20% Discount on Extended License</strong> for next {{$diff}} + Free Mobile App on Release | Free Lifetime Updates</div>
            @else
                <div class="topbar-content d-none d-sm-block">Version 2.9 released. Go to Communication -> Meeting & Start offering Screen Share, Live Classes to Students, Schedule Live Meeting with selected Staff! WhatsApp +91-8-3055-7055-8 (Sales Only, No Support)</div>
            @endif
        @endif
        <div class="preloader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
        </div>
        <div id="root">
            <router-view></router-view>
        </div>
        <script src="/js/lang"></script>
        <script src="{{ mix('/js/app.js') }}"></script>
        <script src="{{ mix('/js/plugin.js') }}"></script>
        @if(config('config.stripe'))
            <script src="https://js.stripe.com/v2"></script>
        @endif
        @if(config('config.paystack') && in_array(getDefaultCurrency('name'), ['NGN', 'GHS']))
            <script src="https://js.paystack.co/v1/inline.js"></script>
        @endif
        @if(config('config.billdesk'))
            @if(! config('config.billdesk_mode'))
                <script type="text/javascript" src="https://services.billdesk.com/checkout-widget/src/app.bundle.js"></script>
            @else
                <script src="https://pgi.billdesk.com/payments-checkout-widget/src/app.bundle.js"></script>
            @endif
        @endif
        @if(Carbon\Carbon::now() > Carbon\Carbon::parse('2020-07-10 00:00') && 
            Carbon\Carbon::now() <= Carbon\Carbon::parse('2020-07-12 23:59'))
            <script>
                $(document).ready(function(){
                    $("#myModal").modal('show');
                });
            </script>
            <div id="myModal" class="modal fade">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Celebrating ScriptMint's 5th Birthday!</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        </div>
                        <div class="modal-body">
                            <img src="https://repo.scriptmint.com/img/scriptmint-5-birthday.png" alt="ScriptMint 5th Birthday Celebration" style="max-width: 100%;" />
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </body>
</html>
