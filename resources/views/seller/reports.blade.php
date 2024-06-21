@extends('seller.layouts.app')

@section('panel_content')
    {{-- Branches Settings --}}

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Sales subscription reports')}}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>{{translate("ID")}}</th>
                        <th>{{translate("Adress")}}</th>
                        <th>{{translate("City")}}</th>
                        <th>{{translate("Customer")}}</th>
                        <th>{{translate("Paid amount")}}</th>
                        <th>{{translate("Date")}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sellerSubscriptions as $key=>$sellerSubscription)
                        <tr>
                            <td>{{$sellerSubscription->id}}</td>
                            <td>{{$sellerSubscription->shop->address}}</td>
                            <td>{{$sellerSubscription->shop->city->name}}</td>
                            <td>{{$sellerSubscription->user->name}}</td>
                            <td>{{format_price($sellerSubscription->payed_amount)}}</td>
                            <td>{{$sellerSubscription->created_at}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>


@endsection
