@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">Country</h5>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body p-0">
                <form class="p-4" action="{{ route('countries.store') }}" method="POST" enctype="multipart/form-data">

                	@csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Name')}} <i class="las la-language text-danger" title="{{translate('Name')}}"></i></label>
                        <div class="col-md-9">
                            <input type="text" name="name" value="" class="form-control" id="name" placeholder="{{translate('Name')}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Name arabic')}} <i class="las la-language text-danger" title="{{translate('Arabic Name')}}"></i></label>
                        <div class="col-md-9">
                            <input type="text" name="name_ar" value="" class="form-control" id="name_ar" placeholder="{{translate('Arabic Name')}}" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Code')}} <i class="las la-language text-danger" title="{{translate('Code')}}"></i></label>
                        <div class="col-md-9">
                            <input type="text" name="code" value="" class="form-control" id="code" placeholder="{{translate('code')}}" required>
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
