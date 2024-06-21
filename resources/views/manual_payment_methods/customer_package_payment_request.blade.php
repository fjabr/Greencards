@extends('backend.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0 h6">{{ translate('Offline Customer Package Payment Requests') }}</h3>


            <form class="" id=""
                action="{{ route('offline_customer_package_payment_request.export_offline_payment_request') }}"
                method="POST">
                @csrf
                <div class="dropdown mb-2 mb-md-0">
                    <input type="hidden" class="form-control" id="search"
                        name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                        placeholder="{{ translate('Type email or name & Enter') }}">
                    <input type="hidden" @isset($date_range) value="{{ $date_range }}" @endisset
                        name="date_range" placeholder="Select Date">
                    <input type="submit" class="form-control" value="Export">
                </div>
            </form>

            <form class="" id="" action="" method="GET">
                <label class="control-label" for="start_date">{{ translate('Date Range') }}</label>
                <div class="form-group mb-0">
                    <input type="text" class="form-control aiz-date-range"
                        @isset($date_range) value="{{ $date_range }}" @endisset name="date_range"
                        placeholder="Select Date">
                </div>
                <div class="form-group mb-0">
                    <label class="control-label" for="search">{{ translate('Search') }}</label>

                    <input type="text" class="form-control" id="search"
                        name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                        placeholder="{{ translate('Type email or name & Enter') }}">
                </div>
                <div class="form-group mb-0 text-right">
                    <a href="{{ route('offline_customer_package_payment_request.index') }}"
                        class="btn ">{{ translate('Reset') }}</a>
                    <button type="submit" class="btn btn-primary">{{ translate('Search') }}</button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Name') }}</th>
                        <th>{{ translate('Package') }}</th>
                        <th>{{ translate('Method') }}</th>
                        <th>{{ translate('TXN ID') }}</th>
                        <th>{{ translate('Reciept') }}</th>
                        <th>{{ translate('Approval') }}</th>
                        <th>{{ translate('Date') }}</th>
                        <th>{{ translate('Download') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($package_payment_requests as $key => $package_payment_request)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $package_payment_request->user ? $package_payment_request->user->name : '' }}</td>
                            <td>{{ $package_payment_request->customer_package ? $package_payment_request->customer_package->getTranslation('name') : '' }}
                            </td>
                            <td>{{ $package_payment_request->payment_method }}</td>
                            <td>{{ $package_payment_request->payment_details }}</td>
                            <td>
                                @if ($package_payment_request->reciept != null)
                                    <a href="{{ asset('public/uploads/customer_package_payment_reciept/' . $package_payment_request->reciept) }}"
                                        target="_blank">{{ translate('Open Reciept') }}</a>
                                @endif
                            </td>
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    @if ($package_payment_request->approval == 1)
                                        <input type="checkbox" checked disabled>
                                    @else
                                        <input onchange="offline_payment_approval(this)" id="payment_approval"
                                            value="{{ $package_payment_request->id }}" type="checkbox">
                                    @endif
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>{{ $package_payment_request->created_at }}</td>
                            <td>
                                @if ($package_payment_request->approval == 1)
                                    <a target="_blanck" href="{{ route('get_invoices', $package_payment_request->id) }} "
                                        class="btn btn-primary">Download</a>
                                @else
                                    <span class="">Approval Required</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $package_payment_requests->links() }}
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function offline_payment_approval(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('offline_customer_package_payment.approved') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    $("#payment_approval").prop("disabled", true);
                    AIZ.plugins.notify('success',
                        '{{ translate('Offline Customer Package Payment approved successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.aiz-date-range').daterangepicker();
        });
    </script>
@endsection
