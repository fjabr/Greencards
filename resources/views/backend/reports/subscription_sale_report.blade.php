@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class=" align-items-center">
            <h1 class="h3">{{ translate('Selling Report') }}</h1>
            {{old("approval")}}
        </div>
    </div>

    <div class="row">
        <div class="col mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('sales-reports.index') }}" method="GET">
                        <div class="form-group row ">
                            <div class="col-2">
                                <select class="from-control aiz-selectpicker" name="agent">
                                    <option value="" <?php if(Request::get('agent') == '') echo "selected" ?> >{{ translate('Agents') }}</option>
                                    @foreach ($agents as $agent)
                                        <option <?php if(Request::get('agent') == $agent->id) echo "selected" ?>  value="{{ $agent->id }}">
                                            {{ $agent->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <select class="from-control aiz-selectpicker" name="package">
                                    <option value="" <?php if(Request::get('agent') == '') echo "selected" ?> >{{ translate('Packages') }}</option>
                                    @foreach ($packages as $package)
                                        <option <?php if(Request::get('package') == $package->id) echo "selected" ?>  value="{{ $package->id }}">
                                            {{ $package->getTranslation("name") }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-2">
                                <select class="from-control aiz-selectpicker" name="payment_type">
                                    <option value="" <?php if(Request::get('payment_type') == '') echo "selected" ?> >{{ translate('Payment type') }}</option>
                                    <option value="1"<?php if(Request::get('payment_type') == '1') echo "selected" ?> >{{ translate('Offline') }}</option>
                                    <option value="2"<?php if(Request::get('payment_type') == '2') echo "selected" ?> >{{ translate('Online') }}</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <select class="from-control aiz-selectpicker" name="approval">
                                    <option value="" <?php if(Request::get('approval') == '') echo "selected" ?> >{{ translate('Approval') }}</option>
                                    <option value="0" <?php if(Request::get('approval') == '0') echo "selected" ?> >{{ translate('No') }}</option>
                                    <option value="1" <?php if(Request::get('approval') == '1') echo "selected" ?> >{{ translate('Yes') }}</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-primary" type="submit">{{ translate('Filter') }}</button>
                                <a href = "{{route('sales-reports.index')}}" class="btn btn-primary" type="submit">{{ translate('Reset') }}</a>
                            </div>
                        </div>
                    </form>

                    <table class="table table-bordered aiz-table mb-0">
                        <thead>
                            <th>#</th>
                            <th>{{ translate('Name') }}</th>
                            <th>{{ translate('Email') }}</th>
                            <th data-breakpoints="lg">{{ translate('Phone') }}</th>
                            <th data-breakpoints="lg">{{ translate('Package') }}</th>
                            <th data-breakpoints="lg">{{ translate('Method') }}</th>
                            <th data-breakpoints="lg">{{ translate('Reciept') }}</th>
                            <th data-breakpoints="lg">{{ translate('Approval') }}</th>
                            <th data-breakpoints="lg">{{ translate('Date') }}</th>
                            <th data-breakpoints="lg">{{ translate('Download') }}</th>
                        </thead>
                        <tbody>
                            @foreach ($packagePaymentRequests as $key => $package_payment_request)
                                @if ($package_payment_request->user != null && $package_payment_request->customer_package != null)
                                    <tr>
                                        <td>{{ $key + $index }}</td>
                                        <td>{{ $package_payment_request->user->name }}</td>
                                        <td>{{ $package_payment_request->user->email }}</td>
                                        <td>{{ $package_payment_request->user->phone }}</td>
                                        <td>{{ $package_payment_request->customer_package->getTranslation('name') }}</td>
                                        <td>{{translate($package_payment_request->payment_method)  }}</td>
                                        <td>
                                            @if ($package_payment_request->reciept != null)
                                                <a href="{{ asset('public/uploads/customer_package_payment_reciept/' . $package_payment_request->reciept) }}"
                                                    target="_blank">{{ translate('Open Reciept') }}</a>
                                            @endif
                                        </td>

                                        <td>
                                            <label class="aiz-switch aiz-switch-success mb-0">
                                                <input type="checkbox" <?php if ($package_payment_request->approval == 1) {
                                                    echo 'checked';
                                                } ?> disabled>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td>{{ $package_payment_request->created_at }}</td>
                                        <td>
                                            @if ($package_payment_request->approval == 1)
                                                <a target="_blanck"
                                                    href="{{ route('get_invoices', $package_payment_request->id) }} "
                                                    class="btn btn-primary">{{translate('Download')}}</a>
                                            @else
                                                <span class="">{{translate('Approval Required')}}</span>
                                            @endif
                                        </td>
                                </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                    <div class="aiz-pagination mt-4">
                        {{ $packagePaymentRequests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
