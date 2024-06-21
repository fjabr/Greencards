@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Customer Information')}}</h5>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body p-0">
                <form class="p-4" action="{{ route('customers.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                	@csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Name')}} </label>
                        <div class="col-md-9">
                            <input type="text" name="name" value="{{ $user->name }}" class="form-control" id="name" placeholder="{{translate('Name')}}" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Email')}} </label>
                        <div class="col-md-9">
                            <input type="email" name="email" value="{{ $user->email }}" class="form-control" id="name" placeholder="{{translate('Email')}}" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Phone')}} </label>
                        <div class="col-md-9">
                            <input type="tel" name="phone" value="{{ $user->phone }}" class="form-control" id="name" placeholder="{{translate('Phone')}}" >
                        </div>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
