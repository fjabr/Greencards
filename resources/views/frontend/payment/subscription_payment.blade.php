
<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/custom-style.css') }}">
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
<body class="bg-light">
    <section class="py-4 mb-4 payment-screen">
        @if ($paymentResponse["success"] == true)
            <div class="container text-center ">
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            @php
                $ssl = env("HYPERPAY_SSL") == "on"? true : false;
                $entityId = "entityId=".env('HYPERPAY_ID', '8ac7a4ca7d97962b017d997e2b8d06e4');
                if (env("HYPERPAY_MODE_TEST") == "on") {
                    if ($paymentResponse['data']['card_type'] === 'MADA') $entityId = '8ac7a4ca7d97962b017d997e2b8d06e4';
                    if ($paymentResponse['data']['card_type'] === 'VISA') $entityId = '8ac7a4ca7d97962b017d997ca72b06de';
                    if ($paymentResponse['data']['card_type'] === 'MASTER') $entityId = '8ac7a4ca7d97962b017d997ca72b06de';
                    if ($paymentResponse['data']['card_type'] === 'AMEX') $entityId = '8ac7a4ca7d97962b017d997ca72b06de';
                    if ($paymentResponse['data']['card_type'] === 'STC_PAY') $entityId = '8ac7a4ca7d97962b017d997ca72b06de';
                    $baseUrl = "https://eu-test.oppwa.com/v1/checkouts";
                    $token = "OGFjN2E0Y2E3ZDk3OTYyYjAxN2Q5OTdiNzU3YzA2ZGF8Ykc0eUtiOGF5Yg==";
                } else {
                    if ($paymentResponse['data']['card_type'] === 'MADA') $entityId = env('HYPERPAY_ID');
                    if ($paymentResponse['data']['card_type'] === 'VISA') $entityId = env('HYPERPAYVISA_ENTITYID');
                    if ($paymentResponse['data']['card_type'] === 'MASTER') $entityId = env('HYPERPAYMASTER_ENTITYID');
                    if ($paymentResponse['data']['card_type'] === 'AMEX') $entityId = env('HYPERPAYMASTER_ENTITYID');
                    if ($paymentResponse['data']['card_type'] === 'STC_PAY') $entityId = env('HYPERPAYMASTER_ENTITYID');
                    $baseUrl = "https://oppwa.com/v1/checkouts";
                    $token = "OGFjZGE0Y2E4MDQ2NGZhMTAxODA1ZmZhNDgyNDA1Y2J8NE5zZ1B6NFpzbg==";
                }

                $amount = number_format((float) $paymentResponse['data']['amount'], 2, '.', '');

                $data = "entityId=" . $entityId .
                        "&amount=" .$amount .
                        "&currency=" .env("HYPERPAY_CURRENCY", "SAR") .
                        "&paymentType=".env("HYPERPAY_PAYMENT_TYPE", "DB").
                        "&locale=".env("HYPERPAY_COUNTRY", "ar-SA");
                        "&registration_source=".$paymentResponse['data']['registration_source'] ;
                        "&seller_id=".$paymentResponse['data']['seller_id'] ;
                        "&source=".$paymentResponse['data']['source'] ;

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $baseUrl);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization:Bearer '.$token));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $responseData = curl_exec($ch);
                if(curl_errno($ch)) {
                    $curlError = curl_error($ch);
                }
                curl_close($ch);

                $responseData = json_decode($responseData, true);
                Log::info($responseData);

                $id = urlencode($responseData['id']);
                header("Location: " . URL::to('/subscription-checkout/'.$id.'?'.$paymentResponse["query"]), true, 302);
                exit();
            @endphp
        @else
            <div class="container text-center ">
                <div class="d-flex flex-column">
                    <p>{{$paymentResponse["message"]}}</p>
                    <div>
                        <button class="back-button" onclick="onPaymentComplete('fail')">Back to App</button>
                    </div>
                </div>
            </div>
        @endif
    </section>
    <script type="text/javascript">
        function onPaymentComplete(data) {
            setTimeout(function () {
                window.ReactNativeWebView?.postMessage(data)
                window?.flutter_inappwebview?.callHandler('closeWebView', 'close://webview');

            }, 0)
        }
    </script>

</body>
</html>
