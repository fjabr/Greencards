@extends('backend.layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Sale a new Subscription') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('sale_subscription.sale_sub_confimation') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label" for="name">{{ translate('First Name') }}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ translate('First Name') }}" id="first_name"
                                    name="first_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group  row">
                            <label class="col-sm-3 col-from-label" for="name">{{ translate('Last Name') }}</label>
                            <div class="col-sm-9">
                                <input type="text" lang="en" min="0" step="0.01"
                                    placeholder="{{ translate('Last Name') }}" id="last_name" name="last_name"
                                    class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label" fro="email">{{ translate('Email') }}</label>
                            <div class="col-sm-9">
                                <input type="email" lang="en" min="0" step="1"
                                    placeholder="{{ translate('Email') }}" id="email" name="email"
                                    class="form-control">
                            </div>
                        </div>
                        <input type="hidden" name="country_code" value="">
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label "
                                for="mobile">{{ translate('Mobile') }}</label>
                            <div class="col-sm-9">
                                <input type="tel" id="phone-code" lang="en" min="0" step="1"
                                    placeholder="123456789" pattern="[0-9]{9}" id="mobile" name="mobile"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label" for="packages">{{ translate('Packages') }}</label>
                            <div class="col-sm-9">
                                <select name="package" class="form-control" required id="package">
                                    <option value="" hidden>{{ translate('Select Package') }}</option>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->id }}">{{ $package->getTranslation('name') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label" fro="coupon">{{ translate('Coupon') }}</label>
                            <div class="col-sm-9">
                                <input type="text" lang="en" min="0" step="1"
                                    placeholder="{{ translate('Coupon') }}" id="coupon" name="coupon"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label" for="packages">{{ translate('Payment Method') }}</label>
                            <div class="col-sm-9">
                                <select onchange="deliveryCommentBlock(this)" name="payment_method" class="form-control"
                                    required id="function deliveryCommentBlock(){

                            }">
                                    <option value="" hidden>{{ translate('Payment Method') }}</option>
                                    <option value="1">{{ translate('Offline') }}</option>
                                    <option value="2">{{ translate('Online') }}</option>
                                    <option value="3">{{ translate('Cash On delivery') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" style="display: none" id='deliveryCommentBlock'>
                            <label class="col-sm-3 col-from-label"
                                for="deliveryComment">{{ translate('Delivery Comment') }}</label>
                            <div class="col-sm-9">
                                <input type="text" lang="en" min="0" step="1"
                                    placeholder="{{ translate('Delivery Comment') }}" id="deliveryComment"
                                    name="deliveryComment" class="form-control">
                            </div>
                        </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{ translate('Create') }}</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function deliveryCommentBlock(selctedPaymentMethod) {
            if (selctedPaymentMethod.value === "3") {
                $('#deliveryCommentBlock').css("display", "");
            } else {
                $('#deliveryCommentBlock').css("display", "none");
            }
        }
    </script>
    <script type="text/javascript">
        var isPhoneShown = true,
            countryData = window.intlTelInputGlobals.getCountryData(),
            input = document.querySelector("#phone-code");

        for (var i = 0; i < countryData.length; i++) {
            var country = countryData[i];
            if(country.iso2 == 'bd'){
                country.dialCode = '88';
            }
        }

        var iti = intlTelInput(input, {
            separateDialCode: true,
            utilsScript: "{{ static_asset('assets/js/intlTelutils.js') }}?1590403638580",
            onlyCountries: @php echo json_encode(\App\Models\Country::where('status', 1)->pluck('code')->toArray()) @endphp,
            customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                if(selectedCountryData.iso2 == 'bd'){
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



    </script>
@endsection
