@extends('backend.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0 h6">{{ translate('Your Added Sbscriptions') }}</h3>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Name') }}</th>
                        <th>{{ translate('Email') }}</th>
                        <th data-breakpoints="lg">{{ translate('Phone') }}</th>
                        <th data-breakpoints="lg">{{ translate('Package') }}</th>
                        <th data-breakpoints="lg">{{ translate('Method') }}</th>
                        <th data-breakpoints="lg">{{ translate('Reciept') }}</th>
                        <th data-breakpoints="lg">{{ translate('Approval') }}</th>
                        <th data-breakpoints="lg">{{ translate('Subscribed until') }}</th>
                        <th data-breakpoints="lg">{{ translate('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($packagePaymentRequests as $key => $package_payment_request)
                        @if ($package_payment_request->user != null && $package_payment_request->customer_package != null)
                            <tr>
                                <td>
                                    {{ $key + 1 + ($packagePaymentRequests->currentPage() - 1) * $packagePaymentRequests->perPage() }}

                                </td>
                                <td>{{ $package_payment_request->user->name }}</td>
                                <td>{{ $package_payment_request->user->email }}</td>
                                <td>{{ $package_payment_request->user->phone }}</td>
                                <td>{{ $package_payment_request->customer_package->getTranslation('name') }}</td>
                                <td>{{ $package_payment_request->payment_method }}</td>
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
                                <td>{{ $package_payment_request->user->end_sub_date }}</td>

                                <td>
                                    @can('renew_subscription')
                                        <a href="{{ route('sale_subscription.renew_subscription', $package_payment_request->user->id) }}"
                                            class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                            title="{{ translate('renew subscription') }}">
                                            <i class="las la-user-check"></i>
                                        </a>
                                    @endcan

                                    <a href="{{ route('sale_subscription.details', $package_payment_request->id) }}"
                                        class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                        title="{{ translate('Subscription details') }}">
                                        <i class="las la-list"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $packagePaymentRequests->links() }}
            </div>
        </div>
    </div>
@endsection
