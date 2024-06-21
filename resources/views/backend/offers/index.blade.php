@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">All Types Offers</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('offers_types.create') }}" class="btn btn-primary">
                <span>Add New Offer Type</span>
            </a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-block d-md-flex">
        <h5 class="mb-0 h6">TYPES</h5>

    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th >#</th>
                    <th>{{ translate('Name') }}</th>
                    <th>{{ translate('Name Arabic') }}</th>
                    <th width="10%" class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($offers as $key => $offer)
                    <tr>
                        <td>{{ $offer->id }}</td>
                        <td>{{ $offer->name }}</td>
                        <td>{{ $offer->name_ar }}</td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('offers_types.edit', ['id'=>$offer->id] )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('offers_types.destroy', $offer->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection


@section('modal')
    @include('modals.delete_modal')
@endsection

