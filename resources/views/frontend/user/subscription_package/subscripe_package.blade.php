@extends('frontend.layouts.app')

@section('content')
    <section class="py-8 bg-primary text-white">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 mx-auto text-center">
                    <h1 class="mb-0 fw-700">{{ translate('Links for packages') }}</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="py-4 py-lg-5">
        <div class="container">
            <div class="row row-cols-xxl-4 row-cols-lg-3 row-cols-md-2 row-cols-1 gutters-10 justify-content-center">
                @foreach ($customer_packages as $key => $customer_package)
                    <div class="col">
                        <div class="card overflow-hidden">
                            <div class="card-body">
                                <div class="text-center mb-4 mt-3">
                                    <img class="mw-100 mx-auto mb-4" src="{{ uploaded_asset($customer_package->logo) }}" height="100">
                                    <h5 class="mb-3 h5 fw-600">{{$customer_package->getTranslation('name')}}</h5>
                                </div>
                                <ul class="list-group list-group-raw fs-20 mb-5">
                                    <li class="list-group-item py-2">
                                        <i class="las la-check text-success mr-2"></i>
                                        {{ $customer_package->product_upload }} {{translate('Product Upload')}}
                                    </li>
                                </ul>

                                <div class="mb-5 d-flex align-items-center justify-content-center">
                                    @if ($customer_package->amount == 0)
                                        <span class="display-4 fw-600 lh-1 mb-0">{{ translate('Free') }}</span>
                                    @else
                                        <span class="display-4 fw-600 lh-1 mb-0">{{ single_price($customer_package->amount) }}</span>
                                    @endif

                                </div>
                                <div class="text-center">
{{--                                        <button class="btn btn-primary" onclick="get_free_package({{ $customer_package->id}})">{{ translate('Link')}}</button>--}}
                                    <a href="{{ route('customer_packages_list_show',['package'=>$customer_package->name]) }}" class="btn btn-success d-inline-block">
                                         Link for {{$customer_package->getTranslation('name')}}
                                    </a>
                                </div>
                                <div class="text-center">
                                    <span id="{{$customer_package->id}}" onclick="copy_to_clipboard(this.id);">{{env('URL_PACKAGE').$customer_package->name}}</span>
                                </div>

                                

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <script>
        function copy_to_clipboard(clicked_id) {
            var text = document.getElementById(clicked_id).innerHTML;
            navigator.clipboard.writeText(text).then(function() {
                /* clipboard successfully set */
                // on change l'apparence du texte pour montrer qu'il a été copié :
                document.getElementById(clicked_id).style.color = "grey";
                document.getElementById(clicked_id).style.fontStyle = "italic";
                document.getElementById(clicked_id).style.fontSize = "15px";



            }, function() {

            });
        }


    </script>

@endsection



