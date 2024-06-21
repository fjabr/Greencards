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

        @keyframes donut-spin {
                        0% {
                            transform: rotate(0deg);
                        }
                        100% {
                            transform: rotate(360deg);
                        }
                    }
                    .redirection-timer {
                        display: inline-block;
                        border: 1px dotted rgba(0, 0, 0, 0.1);
                        border-left-color: #0c9129;
                        padding: 0;
                        margin: 0;
                        height: 24px;
                        width: 24px;
                        line-height: 24px;
                        text-align: center;
                        border-radius: 50%;
                        color: green;
                        font-weight: bold;
                        animation: donut-spin 1s linear infinite;
                    }
    </style>
<section class="py-4 mb-4 payment-screen">
    <div class="container text-center ">
        <div class="d-flex flex-column align-items-center">
            @if ($paymentResponse['success'] === true)
                <h2>{{translate('Congratulations')}}</h2>
                <p>{{translate("Your payment was successful")}}</p>
                <p>
                    <span>
                        <svg style="color: #20a854" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right-circle-fill" viewBox="0 0 16 16">
                            <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM4.5 7.5a.5.5 0 0 0 0 1h5.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H4.5z"/>
                        </svg>
                    </span>
                </p>
                <p>{{translate("Returning to home page...")}}</p>

                <div>
                    <div class="text-center">
                            <span style="position: relative;height: 24px;width: 24px;display: block;text-align: center;margin: auto;">
                                <span class="redirection-timer"></span>
                                <span id="timerId" style="position: absolute;top: 50%;transform: translate(-50%, -50%);left: 50%;">1</span>
                            </span>
                    </div>
                    <div>
                    <button class="back-button" onclick="onPaymentComplete('success')">{{translate("Back to home page")}}</button>
                </div>
                </div>
            @else
                <h2>{{translate("Payment Unsuccessful")}}</h2>
                <p>{{$paymentResponse['message']}}</p>
                <div>
                    <button class="back-button" onclick="onPaymentComplete('fail')">{{translate("Back to home page")}}</button>
                </div>
            @endif
        </div>
    </div>
</section>
<script type="text/javascript">
    function onPaymentComplete(data) {
        window.location = "/";
    }

    window.addEventListener("load", function () {
        let time = 1;
        let interval = setInterval(function () {
            --time;
            document.getElementById('timerId').innerHTML = time.toString();
            if (time === 0) {
                console.log("redirect")
                clearInterval(interval);
            }
        }, 1000)
    })
</script>
@endsection
