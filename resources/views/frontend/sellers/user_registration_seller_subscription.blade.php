@extends('frontend.layouts.app')

@section('content')
    <style>
        /* Style for the image container */
        .my-container {
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* Style for the images */
        .seller-logo {
            width: 50%;
            height: 100%;
            aspect-ratio: 2/2;
            object-fit: cover;
        }

        .payment-summary {
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
        }

        .payment-summary h2 {
            margin-top: 0;
        }

        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .description {
            font-weight: bold;
            font-family: DINPro, Black;
            font-size: 15px;
        }

        .price {
            font-weight: bold;

            font-family: DINPro, Black;
            font-size: 15px;
        }

        .total {
            margin-top: 10px;
            border-top: 1px solid #ccc;
            padding-top: 5px;
            display: flex;
            justify-content: space-between;
        }
    </style>


    <section class="gry-bg py-4">

        <div class="profile">
            <div class="container">

                <div class="row">
                    <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8 mx-auto">
                        <div class="card">
                            @if (get_setting('show_language_switcher') == 'on')
                                <li class="list-inline-item dropdown mr-4" id="lang-change">
                                    @php
                                        if (Session::has('locale')) {
                                            $locale = Session::get('locale', Config::get('app.locale'));
                                        } else {
                                            $locale = 'en';
                                        }
                                    @endphp
                                    <a href="javascript:void(0)" class="dropdown-toggle text-secondary fs-12 py-2"
                                        data-toggle="dropdown" data-display="static">
                                        <span
                                            class="">{{ \App\Models\Language::where('code', $locale)->first()->name }}</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-left">
                                        @foreach (\App\Models\Language::where('status', 1)->get() as $key => $language)
                                            <li>
                                                <a href="javascript:void(0)" data-flag="{{ $language->code }}"
                                                    class="dropdown-item @if ($locale == $language) active @endif">
                                                    <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                        data-src="{{ static_asset('assets/img/flags/' . $language->code . '.png') }}"
                                                        class="mr-1 lazyload" alt="{{ $language->name }}" height="11">
                                                    <span class="language">{{ $language->name }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                            <div class="my-container ">
                                <img class='seller-logo'
                                    src="{{ uploaded_asset($data["logo"]) }}"
                                    width="75%">
                            </div>
                            <div class="">
                                <img width="100%" src="{{App::getLocale()=='ar'? static_asset('uploads/all/banner-salller-subscription-ar.png') :static_asset('uploads/all/banner-salller-subscription.png') }}"
                                    width="75%">
                            </div>

                            <div style="margin-left: 20px; margin-right: 20px">
                                <br />
                                <span>{{ translate('Seller') }} :</span>
                                <span>{{ $data['name'] }}</span>
                                <br />
                                <span>{{ translate('Seller Address') }} :</span>
                                <span>
                                    {{ $data['address'] }}
                                </span>
                                <br />
                                <span>{{ translate('Package') }} :</span>
                                <span>{{ $package->getTranslation('name') }}</span>
                                <br />
                                <span>{{ translate('Price') }} :</span>
                                <span>{{ format_price(convert_price($package->amount)) }} </span>

                                <br />
                                <br />

                            </div>
                            <div
                                style="background: #8bbf4d; text-align: center; padding: 7px; color: white; font-weight: 800; font-size: 15px; font-family: Ubuntu, Bold;">
                                {{ translate('Fill the below information to create your account') }}
                            </div>
                            <form method="POST" id="seller-qr-form-registration"
                                action="{{ route('sellers.customer.subscribe') }}">
                                <div class="px-4 py-3 py-lg-4">
                                    @csrf
                                    <input type="hidden" name="shop_id" value="{{ $data['id'] }}" />
                                    <input type="hidden" name="source" value="{{ $data['source'] }}" />
                                    <div class="form-group">
                                        <input required type="text"
                                            class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}"
                                            value="{{ old('first_name') }}" placeholder="{{ translate('First Name') }}"
                                            name="first_name">
                                        @if ($errors->has('first_name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('first_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <input required type="text"
                                            class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}"
                                            value="{{ old('last_name') }}" placeholder="{{ translate('Last Name') }}"
                                            name="last_name">
                                        @if ($errors->has('last_name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('last_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    @if (addon_is_activated('otp_system'))
                                        <input type="hidden" name="country_code" value="">
                                        <div class="form-group phone-form-group mb-1">
                                            <input required type="tel" id="phone-code"
                                                class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}"
                                                value="{{ old('mobile') }}" placeholder="" name="mobile"
                                                autocomplete="off" required>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <input type="email"
                                            class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                            value="{{ old('email') }}" placeholder="{{ translate('Email (Optional)') }}"
                                            name="email">

                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>


                                    <div class="form-group">
                                        <input type="password"
                                            class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                            placeholder="{{ translate('Password') }}" name="password">
                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <input type="password" class="form-control"
                                            placeholder="{{ translate('Confirm Password') }}" name="password_confirmation">
                                    </div>

                                </div>
                                <div
                                    style="background: #8bbf4d; text-align: center; padding: 7px; color: white; font-weight: 800; font-size: 15px; font-family: Ubuntu, Bold;">
                                    {{ translate('Choose your payment Method') }}
                                </div>
                                <div class="px-4 py-3 py-lg-4">


                                    <div class="row">
                                        <div class="col-12">
                                            <label>{{ translate('Payment Method') }}</label>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <select class="form-control selectpicker rounded-0" data-live-search="true"
                                                    name="payment_option">
                                                    @if (get_setting('paypal_payment') == 1)
                                                        <option value="paypal">{{ translate('Paypal') }}</option>
                                                    @endif
                                                    @if (get_setting('stripe_payment') == 1)
                                                        <option value="stripe">{{ translate('Stripe') }}</option>
                                                    @endif
                                                    @if (get_setting('mercadopago_payment') == 1)
                                                        <option value="mercadopago">{{ translate('Mercadopago') }}
                                                        </option>
                                                    @endif
                                                    @if (get_setting('toyyibpay_payment') == 1)
                                                        <option value="toyyibpay">{{ translate('ToyyibPay') }}</option>
                                                    @endif
                                                    @if (get_setting('sslcommerz_payment') == 1)
                                                        <option value="sslcommerz">{{ translate('sslcommerz') }}</option>
                                                    @endif
                                                    @if (get_setting('instamojo_payment') == 1)
                                                        <option value="instamojo">{{ translate('Instamojo') }}</option>
                                                    @endif
                                                    @if (get_setting('razorpay') == 1)
                                                        <option value="razorpay">{{ translate('RazorPay') }}</option>
                                                    @endif
                                                    @if (get_setting('paystack') == 1)
                                                        <option value="paystack">{{ translate('PayStack') }}</option>
                                                    @endif
                                                    @if (get_setting('voguepay') == 1)
                                                        <option value="voguepay">{{ translate('Voguepay') }}</option>
                                                    @endif
                                                    @if (get_setting('payhere') == 1)
                                                        <option value="payhere">{{ translate('Payhere') }}</option>
                                                    @endif
                                                    @if (get_setting('ngenius') == 1)
                                                        <option value="ngenius">{{ translate('Ngenius') }}</option>
                                                    @endif
                                                    @if (get_setting('iyzico') == 1)
                                                        <option value="iyzico">{{ translate('Iyzico') }}</option>
                                                    @endif
                                                    @if (get_setting('nagad') == 1)
                                                        <option value="nagad">{{ translate('Nagad') }}</option>
                                                    @endif
                                                    @if (get_setting('bkash') == 1)
                                                        <option value="bkash">{{ translate('Bkash') }}</option>
                                                    @endif
                                                    @if (addon_is_activated('paytm') && get_setting('myfatoorah') == 1)
                                                        <option value="myfatoorah">{{ translate('MyFatoorah') }}</option>
                                                    @endif
                                                    @if (addon_is_activated('paytm') && get_setting('khalti_payment') == 1)
                                                        <option value="khalti">{{ translate('Khalti') }}</option>
                                                    @endif
                                                    @if (addon_is_activated('african_pg'))
                                                        @if (get_setting('mpesa') == 1)
                                                            <option value="mpesa">{{ translate('Mpesa') }}</option>
                                                        @endif
                                                        @if (get_setting('flutterwave') == 1)
                                                            <option value="flutterwave">{{ translate('Flutterwave') }}
                                                            </option>
                                                        @endif
                                                        @if (get_setting('payfast') == 1)
                                                            <option value="payfast">{{ translate('PayFast') }}</option>
                                                        @endif
                                                    @endif
                                                    @if (get_setting('Hyperpay_payment') == 1)
                                                        <option value="MADA">{{ translate('MADA') }}</option>
                                                    @endif
                                                    @if (get_setting('Hyperpay_visapayment') == 1)
                                                        <option value="VISA">{{ translate('VISA') }}</option>
                                                        <option value="MASTER">{{ translate('MASTER') }}</option>
                                                        <option value="AMEX ">{{ translate('AMEX') }}</option>
                                                        <option value="STC_PAY  ">{{ translate('STC PAY ') }}</option>
                                                    @endif

                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label>{{ translate('coupon') }}</label>
                                        </div>
                                        <div class="col-7">
                                            <input id='coupon' class="form-control rounded-0"
                                                placeholder="{{ translate('Have coupon code? Apply here') }}"
                                                data-live-search="true" name="coupon" />
                                        </div>

                                        <div class="col-5">
                                            <a id="coupon-apply"
                                                style="color: white; background-color:#8bbf4d; border-color:#8bbf4d; border-radius: 30px;"
                                                class="btn btn-primary btn-block fw-600">
                                                {{ translate('apply') }}</a>
                                        </div>

                                    </div>
                                    <br />
                                    <br />
                                    <div class="payment-summary" style="border:none">
                                        <h2 style="font-family: DINPro, Black; font-weight: bold; font-size: 19px;">{{ translate('Payment Summary') }}</h2>
                                        <div class="item">
                                            <span class="description">{{ translate('Package') }} :
                                                {{ $package->getTranslation('name') }}</span>
                                            <span
                                                class="price">{{ format_price(convert_price($package->amount)) }}</span>
                                            <span style="display: none;" id="package_price">{{ $package->amount }}</span>
                                        </div>

                                        <div class="item">
                                            <span class="description">{{ translate('VAT') }}</span>
                                            <span class="price">15%</span>
                                        </div>

                                    </div>
                                    <div class="payment-summary" style="border:none; background-color: white" >
                                        <div style="display: flex; justify-content: space-between;">
                                            <span class="description">{{ translate('Total') }} :</span>
                                            <span class="price"
                                                id='total_to_pay'>{{ format_price(convert_price($package->amount + $package->amount * 0.15)) }}</span>
                                        </div>
                                    </div>
                                    <div class="mb-5">
                                        <button type="submit" style="background-color: #8bbf4d;  border-color:#8bbf4d"
                                            class="btn btn-primary btn-block fw-600">
                                            {{ translate('Create Account') }}</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
@section('modal')
@endsection

@section('script')
    @if (get_setting('google_recaptcha') == 1)
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif

    <script type="text/javascript">
        function select_payment_type(id) {
            $('input[name=package_id]').val(id);
            $('#select_payment_type_modal').modal('show');
        }

        function payment_type(type) {
            if (type == 'online') {
                $("#select_type_cancel").click();
                show_price_modal();
            } else if (type == 'offline') {
                $("#select_type_cancel").click();
                $.post('{{ route('offline_customer_package_purchase_modal') }}', {
                    _token: '{{ csrf_token() }}',
                    package_id: package_id
                }, function(data) {
                    $('#offline_customer_package_purchase_modal_body').html(data);
                    $('#offline_customer_package_purchase_modal').modal('show');
                });
            }
        }

        function show_price_modal() {
            $('input[name=first_name]').val($('input[name=first_name1]').val());
            $('input[name=last_name]').val($('input[name=last_name1]').val());
            $('input[name=email]').val($('input[name=email1]').val());
            $('input[name=country_code]').val($('input[name=country_code]').val());
            $('input[name=mobile]').val($('input[name=mobile1]').val());
            $('input[name=password]').val($('input[name=password1]').val());
            $('input[name=password_confirmation]').val($('input[name=password_confirmation1]').val());

            $('#price_modal').modal('show');
        }


        @if (get_setting('google_recaptcha') == 1)
            // making the CAPTCHA  a required field for form submission
            $(document).ready(function() {
                // alert('helloman');
                $("#reg-form").on("submit", function(evt) {
                    var response = grecaptcha.getResponse();
                    if (response.length == 0) {
                        //reCaptcha not verified
                        alert("please verify you are humann!");
                        evt.preventDefault();
                        return false;
                    }
                    //captcha verified
                    //do the rest of your validations here
                    $("#reg-form").submit();
                });
            });
        @endif

        var isPhoneShown = true,
            countryData = window.intlTelInputGlobals.getCountryData(),
            input = document.querySelector("#phone-code");

        for (var i = 0; i < countryData.length; i++) {
            var country = countryData[i];
            if (country.iso2 == 'bd') {
                country.dialCode = '88';
            }
        }

        var iti = intlTelInput(input, {
            separateDialCode: true,
            utilsScript: "{{ static_asset('assets/js/intlTelutils.js') }}?1590403638580",
            onlyCountries: @php
                echo json_encode(
                    \App\Models\Country::where('status', 1)
                        ->pluck('code')
                        ->toArray(),
                );
            @endphp,
            customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                if (selectedCountryData.iso2 == 'bd') {
                    return "01xxxxxxxxx";
                }
                return selectedCountryPlaceholder;
            }
        });

        var country = iti.getSelectedCountryData();
        $('input[name=country_code]').val(country.dialCode);

        input.addEventListener("countrychange", function(e) {
            // var currentMask = e.currentTarget.placeholder;

            var country = iti.getSelectedCountryData();
            $('input[name=country_code]').val(country.dialCode);

        });

        function toggleEmailPhone(el) {
            if (isPhoneShown) {
                $('.phone-form-group').addClass('d-none');
                $('.email-form-group').removeClass('d-none');
                isPhoneShown = false;
                $(el).html('{{ translate('Use Phone Instead') }}');
            } else {
                $('.phone-form-group').removeClass('d-none');
                $('.email-form-group').addClass('d-none');
                isPhoneShown = true;
                $(el).html('{{ translate('Use Email Instead') }}');
            }
        }

        $(document).on("click", "#coupon-apply", function() {
            const data = new FormData();
            data.append('code', $('#coupon').val());

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: "POST",
                url: "{{ route('checkout.apply_coupon_code3') }}",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data, textStatus, jqXHR) {
                    AIZ.plugins.notify(data.response_message.response, data.response_message.message);
                    if (data.response_message.coupon) {
                        const amount = parseInt($("#package_price").text())
                        const coupon = data.response_message.coupon;
                        let discountAmount = 0;
                        if (coupon.discount_type == 'percent') {
                            discountAmount = (amount * coupon.discount) / 100;
                        } else if (coupon.discount_type == 'amount') {
                            discountAmount = coupon.discount;
                        }
                        console.log(amount);
                        const totalAmont = amount - discountAmount;
                        $("#total_to_pay").html(totalAmont * 0.15 + totalAmont + " SAR");
                    }
                }
            })
        });
    </script>
@endsection
