@extends('backend.layouts.app')

@section('content')
    <div class="">
        <div class="">

            <div class="card">

                <div class="card-header d-block d-md-flex">
                    <h5 class="mb-0 h6">{{ translate('Qr Subscription Report') }}</h5>

                    <form class="" id="" action="" method="GET">
                        <label class="control-label" for="start_date">{{ translate('Date Range') }}</label>
                        <div class="form-group mb-0">
                            <input type="text" class="form-control aiz-date-range"
                                @isset($date_range) value="{{ $date_range }}" @endisset name="date_range"
                                placeholder="Select Date">
                        </div>
                        <div class="form-group mb-0">
                            <label class="control-label" for="search">{{ translate('Search') }}</label>

                            <input type="text" class="form-control" id="search"
                                name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                                placeholder="{{ translate('Type email or name & Enter') }}">
                        </div>
                        <div class="form-group mb-0 text-right">
                            <a href="{{ route('invoices_mobile') }}" class="btn ">{{ translate('Reset') }}</a>
                            <button type="submit" class="btn btn-primary">{{ translate('Search') }}</button>
                        </div>
                    </form>


                </div>
                <div class="card-body">
                    <form action="{{ route('seller_sale_report.index') }}" method="GET">

                    </form>

                    <table class="table aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>{{ translate('Seller Name') }}</th>
                                <th>{{ translate('Address') }}</th>
                                <th>{{ translate('City') }}</th>
                                <th>{{ translate('Customer') }}</th>
                                <th>{{ translate('Amount') }}</th>
                                <th>{{ translate('Coupon') }}</th>
                                <th>{{ translate('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sellerSubscriptions as $key => $sellerSubscription)
                                <tr>
                                    <td>
                                        @if ($sellerSubscription->source === 'branch')
                                            <a href="{{ route('sellers.index', ['branch'=> $sellerSubscription->shop->id]) }}">
                                                {{$sellerSubscription->shop->name}}
                                            </a>
                                        @else
                                            <a href="{{ route('sellers.index', ['shop'=> $sellerSubscription->shop->id]) }}">
                                                 {{$sellerSubscription->shop->name}}
                                            </a>
                                        @endif
                                    </td>
                                    <td>{{ $sellerSubscription->shop->address }}</td>
                                    <td>{{ $sellerSubscription->shop->city->name }}</td>
                                    <td>
                                        <a
                                            href="{{ route('customers.edit', encrypt($sellerSubscription->user->id)) }}">{{ $sellerSubscription->user->name }}</a>
                                    </td>
                                    <td>{{format_price($sellerSubscription->payed_amount)}}</td>
                                    <td>{{ $sellerSubscription->coupon }}</td>
                                    <td>{{ $sellerSubscription->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection
