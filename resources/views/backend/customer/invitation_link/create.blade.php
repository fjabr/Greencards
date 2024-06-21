@extends('backend.layouts.app')
@section('content')

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Create New Invitaion Link')}}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('customers.links.store') }}" method="POST" enctype="multipart/form-data">
                  	@csrf
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Partner')}}</label>
                        <div class="col-sm-9">
                            <input type="text" lang="en" min="0" step="1" placeholder="{{translate('Partner')}}" id="Partner" name="partner" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">{{ translate('Logo') }}</label>
                        <div class="col-md-10">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="logo" value="" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Package')}}</label>
                        <div class="col-sm-9">
                            <select name="package_id" class="form-control" required id="shop_id">
                                <option value="">{{translate('Select Package')}}</option>
                                @foreach( $packages as $package)
                                    <option value="{{ $package->id }}">{{ $package->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Number of invitations')}}</label>
                        <div class="col-sm-9">
                            <input type="number" lang="en" min="0" step="1" placeholder="{{translate('Number of invitations')}}" id="number_of_invitaion" name="number_of_invitaion" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Description')}}</label>
                        <div class="col-sm-9">
                            <input type="text" lang="en" min="0" step="1" placeholder="{{translate('Description')}}" id="description" name="description" class="form-control" required>
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
