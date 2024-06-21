@extends('frontend.layouts.app')

@section('content')
    <style>
        .spinner-border {
            border-right-color: #0000003d !important;
        }
        .back-button {
            border: none;
            background: #20a85426;
            margin-top: 10px;
            color: #20a854;
            cursor: pointer;
            padding: 0.416rem 1rem;
            font-size: 0.8125rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }
        body {
            height: 100vh;
        }

        body .payment-screen {
            height: 100%;
        }

        body .payment-screen .container {
            height: 100%;
        }
    </style>
</head>
<section class="py-4 mb-4 payment-screen">
    <div class="container text-center ">
        <div class="d-flex flex-column align-items-center">
            <div class="text-center mb-1">
                <strong>{{translate("Amount to Pay:")}}</strong> <?php echo $amount." SAR";  ?>
            </div>
            <!--data-brands='VISA MASTER AMEX MADA'-->
            <form action="{{ URL::to('/order-complete?'.$query) }}" class="paymentWidgets" data-brands="{{$card_type}}">
                <div class="container text-center ">
                    <div class="d-flex flex-column align-items-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">{{translate("Loading...")}}</span>
                        </div>
                        <div>
                            {{translate('Loading payment...')}}
                        </div>
                    </div>
                </div>
            </form>

            @if(env("HYPERPAY_MODE_TEST") === "on")
                <script src="https://test.oppwa.com/v1/paymentWidgets.js?checkoutId=<?= $id;?>"></script>
            @else
                <script src="https://oppwa.com/v1/paymentWidgets.js?checkoutId=<?= $id;?>"></script>
            @endif
            <div>
                <button class="back-button" onclick="onPaymentComplete('fail')">{{translate("Go back to home page")}}</button>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function onPaymentComplete(data) {
        setTimeout(function () {
            window.ReactNativeWebView?.postMessage(data)
        }, 0)
    }
</script>

