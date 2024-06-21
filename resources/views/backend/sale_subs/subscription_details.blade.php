@extends('backend.layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Subscription Payment Details') }}</h5>
                    @can('renew_subscription')
                        <h5 class="mb-0 h6">
                            {{ translate('Renew Subscription') }}
                            <a href="{{ route('sale_subscription.renew_subscription', $packagePayment->user->id) }}"
                                class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                title="{{ translate('renew subscription') }}">
                                <i class="las la-user-check"></i>
                            </a>
                        </h5>
                    @endcan
                </div>
                <div class="card-body">
                    <div>
                        <h6 class="mb-0 h7">{{ translate('User info') }}</h6>
                        <div class="row" style="padding: 10px">
                            <div class="col col-6">{{ translate('Name') }}</div>
                            <div class="col col-6">{{ $packagePayment->user->name }}</div>
                        </div>
                        <div class="row" style="padding: 10px">
                            <div class="col col-6">{{ translate('Email') }}</div>
                            <div class="col col-6">{{ $packagePayment->user->email }}</div>
                        </div>
                        <div class="row" style="padding: 10px">
                            <div class="col col-6">{{ translate('Phone') }}</div>
                            <div class="col col-6">{{ $packagePayment->user->phone }}</div>
                        </div>


                        <div class="row" style="padding: 10px">
                            <div class="col col-6">{{ translate('is subscribed') }}</div>
                            <div class="col col-6">

                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" <?php if (isSubscribedUser($packagePayment->user)) {
                                        echo 'checked';
                                    } ?> disabled>
                                    <span class="slider round"></span>

                                </label>
                            </div>
                        </div>

                        <div class="row" style="padding: 10px">
                            <div class="col col-6">{{ translate('Subscription Starting Date') }}</div>
                            <div class="col col-6">{{ $packagePayment->user->start_sub_date }}</div>
                        </div>

                        <div class="row" style="padding: 10px">
                            <div class="col col-6">{{ translate('Subscription Ending Date') }}</div>
                            <div class="col col-6">{{ $packagePayment->user->end_sub_date }}</div>
                        </div>
                    </div>
                    <hr />
                    <div>
                        <h6 class="mb-0 h7">{{ translate('Package Details') }}</h6>
                        <div class="row" style="padding: 10px">
                            <div class="col col-6">{{ translate('Package Name') }}</div>
                            <div class="col col-6">{{ $packagePayment->customer_package->getTranslation('name') }}</div>
                        </div>
                        <div class="row" style="padding: 10px">
                            <div class="col col-6">{{ translate('Package Price') }}</div>
                            <div class="col col-6">{{ format_price($packagePayment->customer_package->amount) }}</div>
                        </div>
                        <div class="row" style="padding: 10px">
                            <div class="col col-6">{{ translate('Package Duration') }}</div>
                            <div class="col col-6">{{ $packagePayment->customer_package->duration }}</div>
                        </div>
                    </div>
                    <hr />
                    <div>
                        <h6 class="mb-0 h7">{{ translate('Payment details') }}</h6>
                        <div class="row" style="padding: 10px">
                            <div class="col col-6">{{ translate('Method') }}</div>
                            <div class="col col-6">{{ $packagePayment->payment_method }}</div>
                        </div>
                        <div class="row" style="padding: 10px">
                            <div class="col col-6">{{ translate('Approval') }}</div>
                            <div class="col col-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" <?php if ($packagePayment->approval == 1) {
                                        echo 'checked';
                                    } ?> disabled>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="row" style="padding: 10px">
                            <div class="col col-6">{{ translate('Reciept') }}</div>
                            <div class="col col-6">
                                @if ($packagePayment->reciept != null)
                                    <a href="{{ asset('public/uploads/customer_package_payment_reciept/' . $packagePayment->reciept) }}"
                                        target="_blank">{{ translate('Open Reciept') }}</a>
                                @else
                                    <span>
                                        {{ translate('no reciept') }}
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="row" style="padding: 10px">
                            <div class="col col-6">{{ translate('Payment date') }}</div>
                            <div class="col col-6">{{ $packagePayment->created_at }}</div>
                        </div>


                        @if ($packagePayment->approval)
                            <div class="row" style="padding: 10px">
                                <div class="col col-6">{{ translate('Download Invoice') }}</div>
                                <div class="col col-6">
                                    <a href="{{ route('get_invoices', $packagePayment->id) }} "
                                        class="btn btn-primary">{{ translate('Download') }}</a>
                                </div>
                            </div>
                        @endif
                        @if ($packagePayment->approval)
                            <div class="row" style="padding: 10px">
                                <div class="col col-6">{{ translate('Resend Invoice') }}</div>
                                <div class="col col-6">
                                    <a href="{{ route('customers.resend_invoice', encrypt($packagePayment->id)) }} "
                                        class="btn btn-primary">{{ translate('Resend invoice') }}</a>
                                </div>
                            </div>
                        @endif

                        @if ($packagePayment->approval == 0)
                            <div class="row" style="padding: 10px">
                                <div class="col col-6">{{ translate('Resend Invoice') }}</div>
                                <div class="col col-6">
                                    <td>
                                        <form enctype="multipart/form-data" method="POST"
                                            action="{{ route('sale_subscription.upload_receipt') }}">
                                            @csrf
                                            <input type="hidden" name='id' style="display:none"
                                                value={{ $packagePayment->id }} />
                                            <input type="file" name='receipt' onchange="this.parentNode.submit()" />
                                        </form>
                                    <td>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
