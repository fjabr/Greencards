@extends('frontend.layouts.app')

@section('content')
    <style>
    /* Style for the image container */
    .img-container {
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }

    /* Style for the images */
    .img-container img {
        border-radius: 90px;
        margin-right: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        width: 40%;
        height: 100%;
        aspect-ratio: 2/2;
        object-fit: cover;
      }
      .img-container img:last-child {
        margin-right: 0;
      }
  </style>
    <section class="gry-bg py-4">
        <div class="profile">
            <div class="container">

                <div class="row">
                    <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-8 mx-auto">
                        <div class="card">
                            @if (get_setting('show_language_switcher') == 'on')
                                <li style="margin: 17px;" class="list-inline-item dropdown mr-4" id="lang-change">
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
                            <div class="text-center pt-4">
                                <h1 class="h4 fw-600">
                                    {{ translate('Create an account.')}}
                                </h1>
                                <h3 class="h6 fw-600"> {{translate("You are using the invitation link")}} </h3>
                                <div class="img-container">
                                    <img src="{{uploaded_asset($invitationLink->logo)}}" alt="Image 1">
                                    <img src="{{uploaded_asset(get_setting('owner_logo'))}}" alt="Image 2">
                                  </div>

                                <p>
                                    <span>{{translate("Company :")}}</span>
                                    <span>{{$invitationLink->partner}}</span>
                                </p>
                                <p>
                                    <span>{{translate("Package :")}}</span>
                                    <span>{{$invitationLink->package->getTranslation("name")}}</span>
                                </p>


                            </div>
                            <div class="px-4 py-3 py-lg-4">
                                <div class="">
                                    <form id="reg-form" class="form-default" role="form" action="{{ route('user.links.register',$id) }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <input type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" value="{{ old('first_name') }}" placeholder="{{  translate('First Name') }}" name="first_name">
                                            @if ($errors->has('first_name'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('first_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <input type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" value="{{ old('last_name') }}" placeholder="{{  translate('Last Name') }}" name="last_name">
                                            @if ($errors->has('last_name'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('last_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        @if (addon_is_activated('otp_system'))
                                        <input type="hidden" name="country_code" value="">
                                            <div class="form-group phone-form-group mb-1">
                                                <input type="tel" id="phone-code" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" value="{{ old('mobile') }}" placeholder="" name="mobile" autocomplete="off" required>
                                            </div>
                                        @endif
                                            <div class="form-group">
                                                <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" placeholder="{{  translate('Email (Optional)') }}" name="email">

                                                @if ($errors->has('email'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>


                                        <div class="form-group">
                                            <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{  translate('Password') }}" name="password">
                                            @if ($errors->has('password'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <input type="password" class="form-control" placeholder="{{  translate('Confirm Password') }}" name="password_confirmation">
                                        </div>

                                        @if(get_setting('google_recaptcha') == 0)
                                            <div class="form-group">
                                                <div class="g-recaptcha" data-sitekey="{{ env('CAPTCHA_KEY') }}"></div>
                                            </div>
                                        @endif

                                        <div class="mb-5">
                                            <button type="submit" class="btn btn-primary btn-block fw-600">{{  translate('Create Account') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection


@section('script')
    @if(get_setting('google_recaptcha') == 1)
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif

    <script type="text/javascript">

        @if(get_setting('google_recaptcha') == 1)
        // making the CAPTCHA  a required field for form submission
        $(document).ready(function(){
            // alert('helloman');
            $("#reg-form").on("submit", function(evt)
            {
                var response = grecaptcha.getResponse();
                if(response.length == 0)
                {
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

        function toggleEmailPhone(el){
            if(isPhoneShown){
                $('.phone-form-group').addClass('d-none');
                $('.email-form-group').removeClass('d-none');
                isPhoneShown = false;
                $(el).html('{{ translate('Use Phone Instead') }}');
            }
            else{
                $('.phone-form-group').removeClass('d-none');
                $('.email-form-group').addClass('d-none');
                isPhoneShown = true;
                $(el).html('{{ translate('Use Email Instead') }}');
            }
        }
    </script>
@endsection
