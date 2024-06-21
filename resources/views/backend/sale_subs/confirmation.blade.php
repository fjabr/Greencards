@extends('backend.layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Sale Confirmation Page') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('sale_subscription.add_customer') }}" method="POST">
                        <div style="display: none">
                            @csrf
                            <div class="form-group row">
                                <label class="col-sm-3 col-from-label" for="name">{{ translate('First Name') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" placeholder="{{ translate('First Name') }}" id="first_name"
                                        value="{{ $request['first_name'] }}" name="first_name" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="form-group  row">
                                <label class="col-sm-3 col-from-label" for="name">{{ translate('Last Name') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" lang="en" min="0" step="0.01"
                                        placeholder="{{ translate('Last Name') }}" value="{{ $request['last_name'] }}"
                                        id="last_name" name="last_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-from-label" fro="email">{{ translate('Email') }}</label>
                                <div class="col-sm-9">
                                    <input type="email" lang="en" min="0" step="1"
                                        placeholder="{{ translate('Email') }}" id="email"
                                        value="{{ $request['email'] }}" name="email" class="form-control">
                                </div>
                            </div>
                            <input type="hidden" name="country_code" value="{{ $request['country_code'] }}">
                            <div class="form-group row">
                                <label class="col-sm-3 col-from-label " pattern="[0-9]{3}-[0-9]{2}-[0-9]{3}"
                                    for="mobile">{{ translate('Mobile') }}</label>
                                <div class="col-sm-9">
                                    <input type="tel" lang="en" min="0" step="1"
                                        placeholder="123456789" value="{{ $request['mobile'] }}" placeholder="123456789" pattern="[0-9]{9}"
                                        id="mobile" name="mobile" class="form-control" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-from-label" for="packages">{{ translate('Packages') }}</label>
                                <div class="col-sm-9">
                                    <select name="package" class="form-control" required id="package">
                                        <option value="" hidden>{{ translate('Select Package') }}</option>
                                        @foreach ($packages as $package)
                                            @if ($request['package'] == $package->id)
                                                <option value="{{ $package->id }}" selected>{{ $package->name }}</option>
                                            @else
                                                <option value="{{ $package->id }}">{{ $package->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" id='deliveryCommentBlock'>
                                <label class="col-sm-3 col-from-label"
                                    for="deliveryComment">{{ translate('Delivery Comment') }}</label>

                                <div class="col-sm-9">
                                    <input type="text" lang="en" min="0" step="1"
                                        placeholder="{{ translate('Delivery Comment') }}" id="deliveryComment"
                                        name="deliveryComment" class="form-control"
                                        value="{{ $request['deliveryComment'] }}"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-from-label" fro="coupon">{{ translate('Coupon') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" lang="en" min="0" step="1"
                                        placeholder="{{ translate('Coupon') }}" value="{{ $request['coupon'] }}"
                                        id="coupon" name="coupon" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-from-label"
                                    for="packages">{{ translate('Payment Method') }}</label>
                                <div class="col-sm-9">
                                    <select name="payment_method" class="form-control" required id="package">
                                        <option value="" hidden>{{ translate('Payment Method') }}</option>
                                        <option value="1" <?php if ($request['payment_method'] == '1') {
                                            echo 'selected';
                                        } ?>>{{ translate('Offline') }}</option>
                                        <option value="2" <?php if ($request['payment_method'] == '2') {
                                            echo 'selected';
                                        } ?>>{{ translate('Online') }}</option>
                                        <option value="3" <?php if ($request['payment_method'] == '3') {
                                            echo 'selected';
                                        } ?>>{{ translate('Cash On delever') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="row">
                                <div class="col col-6">{{ translate('First name') }}</div>
                                <div class="col col-6">{{ $request['first_name'] }}</div>
                            </div>
                            <div class="row">
                                <div class="col col-6">{{ translate('Last name') }}</div>
                                <div class="col col-6">{{ $request['last_name'] }}</div>
                            </div>
                            <div class="row">
                                <div class="col col-6">{{ translate('Email') }}</div>
                                <div class="col col-6">{{ $request['email'] }}</div>
                            </div>
                            <div class="row">
                                <div class="col col-6">{{ translate('mobile') }}</div>
                                <div class="col col-6">{{ '+'.$request["country_code"].$request['mobile'] }}</div>
                            </div>
                            <div class="row">
                                <div class="col col-6">{{ translate('Coupon') }}</div>
                                <div class="col col-6">{{ $request['coupon'] }}</div>
                            </div>


                            @foreach ($packages as $package)
                                @if ($request['package'] == $package->id)
                                    <div class="row">
                                        <div class="col col-6">{{ translate('Package') }}</div>
                                        <div class="col col-6">{{ $package->name }}</div>
                                    </div>

                                    <div class="row">
                                        <div class="col col-6">{{ translate('Package price') }}</div>
                                        <div class="col col-6">{{ $package->amount . ' SAR' }}</div>

                                    </div>
                                @endif
                            @endforeach
                            @if ($request['payment_method'] == '3')
                                <div class="row">
                                    <div class="col col-6">{{ translate('Delivery Comment') }}</div>
                                    <div class="col col-6">{{ $request['deliveryComment'] }}</div>
                                </div>
                            @endif

                            @if (!empty($appliedCouponCalculation))
                                <div class="row">
                                    <div class="col col-6">{{ translate('Applied Discount') }}</div>
                                    <div class="col col-6">{{ $appliedCouponCalculation['coupon']['discount'] }}</div>
                                </div>

                                <div class="row">
                                    <div class="col col-6">{{ translate('Total After Discount') }}</div>
                                    <div class="col col-6">{{ $total_amount . ' SAR' }}</div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col col-6">{{ translate('VAT Total') }}</div>
                                <div class="col col-6">{{ $vta_total . ' SAR' }}</div>
                            </div>

                            <div class="row">
                                <div class="col col-6">{{ translate('TOTAL : ') }}</div>
                                <div class="col col-6">{{ $vta_total + $total_amount . ' SAR' }}</div>
                            </div>

                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                                <a onclick="window.history.back()" class="btn btn-primary">{{ translate('Back') }}</a>
                            </div>

                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
