@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <!--<h1 class="h3">Contracts</h1>-->

            </div>

        </div>
    </div>
    <div class="card">
        <div class="card-header d-block d-md-flex">
            <h5 class="mb-0 h6">{{translate("Invoices Mobile")}}</h5>

            <form class="" id="" action="{{ route('export_invoices_mobile') }}" method="POST">
                @csrf
                <div class="dropdown mb-2 mb-md-0">
                    <input type="hidden" @isset($date_range) value="{{ $date_range }}" @endisset name="date_range"
                        placeholder="Select Date">
                    <input type="hidden" class="form-control" id="search"
                        name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                        placeholder="{{ translate('Type email or name & Enter') }}">
                    <input type="submit" class="form-control" value="Export">
                </div>
            </form>

            <form class="" id="" action="" method="GET">
                <label class="control-label" for="start_date">{{ translate('Date Range') }}</label>
                <div class="form-group mb-0">
                    <input type="text" class="form-control aiz-date-range" @isset($date_range) value="{{ $date_range }}" @endisset name="date_range"
                        placeholder="Select Date">
                </div>
                <div class="form-group mb-0">
                <label class="control-label" for="search">{{ translate('Search') }}</label>

                    <input type="text" class="form-control" id="search"
                        name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                        placeholder="{{ translate('Type email or name & Enter') }}">
                </div>
                <div class="form-group mb-0 text-right">
                    <a href="{{route('invoices_mobile')}}" class="btn ">{{translate('Reset')}}</a>
                    <button type="submit" class="btn btn-primary">{{translate('Search')}}</button>
                </div>
            </form>


        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('User Name') }}</th>
                        <th>{{ translate('User Email') }}</th>
                        <th>{{ translate('Amount') }}</th>
                        <th>{{ translate('VAT Amount') }}</th>
                        <th>{{ translate('Qr Code') }}</th>
                        <th>{{ translate('Invoice Date') }}</th>
                        <th>{{ translate('Download') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoices as $key => $invoice)
                        <tr>
                            <td>{{ $key + 1 + ($invoices->currentPage() - 1) * $invoices->perPage() }}</td>
                            <td>{{ $invoice->userName }}</td>
                            <td>{{ $invoice->userEmail }}</td>
                            <td>
                                {{ $invoice->amount }}
                            </td>
                            <td>
                                {{ $invoice->vat_total }}
                            </td>
                            <td>
                        
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&amp;data={{ $invoice->qr_code }}">
                            </td>
                            <td>
                                {{ $invoice->created_at }}
                            </td>
                            <td>
                                <a target="_blanck" href="{{ route('get_invoices', $invoice->id) }} "
                                    class="btn btn-primary">Download</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.aiz-date-range').daterangepicker();
        });
    </script>
@endsection
