    @extends('backend.layouts.app')

    @section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6 ">{{translate('Paypal Credential')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        <input type="hidden" name="payment_method" value="paypal">
                        @csrf
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="PAYPAL_CLIENT_ID">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('Paypal Client Id')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="PAYPAL_CLIENT_ID" value="{{  env('PAYPAL_CLIENT_ID') }}" placeholder="{{ translate('Paypal Client ID') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="PAYPAL_CLIENT_SECRET">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('Paypal Client Secret')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="PAYPAL_CLIENT_SECRET" value="{{  env('PAYPAL_CLIENT_SECRET') }}" placeholder="{{ translate('Paypal Client Secret') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('Paypal Sandbox Mode')}}</label>
                            </div>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input value="1" name="paypal_sandbox" type="checkbox" @if (get_setting('paypal_sandbox') == 1)
                                        checked
                                    @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6 ">{{translate('Stripe Credential')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="stripe">
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="STRIPE_KEY">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('Stripe Key')}}</label>
                            </div>
                            <div class="col-md-8">
                            <input type="text" class="form-control" name="STRIPE_KEY" value="{{  env('STRIPE_KEY') }}" placeholder="{{ translate('STRIPE KEY') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="STRIPE_SECRET">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('Stripe Secret')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="STRIPE_SECRET" value="{{  env('STRIPE_SECRET') }}" placeholder="{{ translate('STRIPE SECRET') }}" required>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

 <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Hyperpay')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="authorizenet">
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="HYPERPAY_ID">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('HYPERPAY_ID')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="HYPERPAY_ID" value="{{  env('HYPERPAY_ID') }}" placeholder="{{ translate('HYPERPAY ID') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="HYPERPAY_TOKEN">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('HYPERPAY_TOKEN')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="HYPERPAY_TOKEN" value="{{  env('HYPERPAY_TOKEN') }}" placeholder="{{ translate('HYPERPAY_TOKEN') }}" required>
                            </div>
                        </div>

                        
                        
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="HYPERPAY_SSL">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('HYPERPAY_SSL')}}</label>
                            </div>
                            <div class="col-md-8 row">
                                <div class="col">
                                    
                                    <input type="radio" name="HYPERPAY_SSL" value="on" @if (env('HYPERPAY_SSL') == "on")
                                        checked
                                    @endif/>
                                    <label>YES</label>
                                </div>
                                <div class="col">
                                    
                                    <input type="radio" name="HYPERPAY_SSL" value="off" @if (env('HYPERPAY_SSL') == "off")
                                        checked
                                    @endif/>
                                    <label>NO</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="HYPERPAY_MODE_TEST">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('Test Mode')}}</label>
                            </div>
                            <div class="col-md-8 row">
                                <div class="col">
                                    
                                    <input type="radio" name="HYPERPAY_MODE_TEST" value="on" @if (env('HYPERPAY_MODE_TEST') == "on")
                                        checked
                                    @endif/>
                                    <label>YES</label>
                                </div>
                                <div class="col">
                                    
                                    <input type="radio" name="HYPERPAY_MODE_TEST"  value="off" @if (env('HYPERPAY_MODE_TEST') == "off")
                                        checked
                                    @endif/>
                                    <label>NO</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="HYPERPAY_CURRENCY">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('HYPERPAY_CURRENCY')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" oninput="this.value = this.value.toUpperCase()" class="form-control" name="HYPERPAY_CURRENCY" value="{{  env('HYPERPAY_CURRENCY') }}" placeholder="{{ translate('HYPERPAY_CURRENCY') }}" required>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

 <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Hyperpay Mada Credential')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="HYPERPAY_ACCESS_TOKEN">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('Access Token')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="HYPERPAY_ACCESS_TOKEN" value="{{  env('HYPERPAY_ACCESS_TOKEN') }}" placeholder="{{ translate('HYPERPAY_ACCESS_TOKEN') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="HYPERPAY_ENTITYID">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('EntityID')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="HYPERPAY_ENTITYID" value="{{  env('HYPERPAY_ENTITYID') }}" placeholder="{{ translate('HYPERPAY_ENTITYID') }}" required>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Hyperpay Visa,Master,Amex Credential')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="HYPERPAYVISA_ACCESS_TOKEN">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('Access Token')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="HYPERPAYVISA_ACCESS_TOKEN" value="{{  env('HYPERPAYVISA_ACCESS_TOKEN') }}" placeholder="{{ translate('HYPERPAYVISA_ACCESS_TOKEN') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="HYPERPAYVISA_ENTITYID">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('EntityID')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="HYPERPAYVISA_ENTITYID" value="{{  env('HYPERPAYVISA_ENTITYID') }}" placeholder="{{ translate('HYPERPAYVISA_ENTITYID') }}" required>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Hyperpay STC_PAY Credential')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="HYPERPAYSTCPAY_ACCESS_TOKEN">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('Access Token')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="HYPERPAYSTCPAY_ACCESS_TOKEN" value="{{  env('HYPERPAYSTCPAY_ACCESS_TOKEN') }}" placeholder="{{ translate('HYPERPAYSTCPAY_ACCESS_TOKEN') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="HYPERPAYSTCPAY_ENTITYID">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('EntityID')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="HYPERPAYSTCPAY_ENTITYID" value="{{  env('HYPERPAYSTCPAY_ENTITYID') }}" placeholder="{{ translate('HYPERPAYSTCPAY_ENTITYID') }}" required>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6 ">{{translate('Mercadopago Credential')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        <input type="hidden" name="payment_method" value="paypal">
                        @csrf
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MERCADOPAGO_KEY">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('Mercadopago Key')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="MERCADOPAGO_KEY" value="{{  env('MERCADOPAGO_KEY') }}" placeholder="{{ translate('Mercadopago Key') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MERCADOPAGO_ACCESS">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('Mercadopago Access')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="MERCADOPAGO_ACCESS" value="{{  env('MERCADOPAGO_ACCESS') }}" placeholder="{{ translate('Mercadopago Access') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MERCADOPAGO_CURRENCY">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('MERCADOPAGO CURRENCY')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="MERCADOPAGO_CURRENCY" value="{{  env('MERCADOPAGO_CURRENCY') }}" placeholder="{{ translate('MERCADOPAGO CURRENCY') }}" required>
                                <br>
                                <div class="alert alert-primary" role="alert">
                                    Currency must be <b>es-AR</b> or <b>es-CL</b> or <b>es-CO</b> or <b>es-MX</b> or <b>es-VE</b> or <b>es-UY</b> or <b>es-PE</b> or <b>pt-BR</b><br>
                                    If kept empty, <b>en-US</b> will be used automatically
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

       
       

       
    </div>

    @endsection
