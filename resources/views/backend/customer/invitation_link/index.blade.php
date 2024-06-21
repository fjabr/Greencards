@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{ translate('Customers Invitation Links') }}</h1>
        </div>
    </div>


    <div class="card">
        <form class="" id="sort_customers" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-0 h6">{{ translate('Customers Invitation Links') }}</h5>
                </div>

                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <a href="{{ route('customers.links.create') }}" class="btn btn-circle btn-info">
                            <span>{{translate('Add New invitaion link')}}</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <!--<th data-breakpoints="lg">#</th>-->
                            <th >#</th>
                            <th >{{ translate('Descripiton') }}</th>
                            <th >{{ translate('Partner') }}</th>
                            <th >{{ translate('Package') }}</th>
                            <th data-breakpoints="lg">{{ translate('Logo') }}</th>
                            <th data-breakpoints="lg">{{ translate('Total Number of invitation') }}</th>
                            <th data-breakpoints="lg">{{ translate('Usage') }}</th>
                            <th>{{ translate('Options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invitationLinks as $key => $invitationLink)
                            <tr>
                                <td>{{ ($key+1) + ($invitationLinks->currentPage() - 1)*$invitationLinks->perPage()  }}</td>
                                <td>{{ $invitationLink->description }}</td>
                                <td>{{ $invitationLink->partner }}</td>
                                <td>{{ $invitationLink->logo }}</td>
                                <td>{{ $invitationLink->package->getTranslation('name') }}</td>
                                <td>{{ $invitationLink->nb_members }}</td>
                                <td>{{ count($invitationLink->invitationLinks)  }}</td>
                                <td>
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="confirm_delete('{{route('customers.links.delete', ['invitationLink' => $invitationLink->id])}}');"  title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                    <a href="#" class="btn btn-soft-success btn-icon btn-circle btn-sm" onclick="copy_link_modal('{{ url('/').$invitationLink->link }}');" title="{{ translate('Copy') }}">
                                        <i class="las la-copy"></i>
                                    </a>
                                    <a href="{{route('customers.links.edit',$invitationLink->id)}}" class="btn btn-soft-success btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                                        <i class="las la-pen"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $invitationLinks->links() }}
              </div>
            </div>
        </form>


        <!-- copy url Modal -->
        <div class="modal fade" id="copy-url-modal">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">


                    <div class="modal-header">
                        <h4 class="modal-title h6">{{translate('Copy link')}}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mt-1">{{translate('copy the link')}}</p>
                        <input type="text" readonly lang="en" placeholder="{{translate('invitation link')}}" id="invitation_link" name="invitation_link" class="form-control" required>

                        <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{translate('Cancel')}}</button>
                        <button onclick="copy_link()" id="delete-link" data-dismiss="modal" class="btn btn-primary mt-2">{{translate('Copy')}}</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal -->

        <!-- delete Modal -->
        <div class="modal fade" id="confirm-delete">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">

                    <form id="my-confirm-delete-form" action="" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h4 class="modal-title h6">{{translate('Delete Confirmation')}}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        </div>
                        <div class="modal-body text-center">
                            <p class="mt-1">{{translate('Are you sure to delete this?')}}</p>
                            <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{translate('Cancel')}}</button>
                            <button type="submit" class="btn btn-link mt-2">{{translate('Delete')}}</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <!-- /.modal -->
    </div>
@endsection


@section('script')
    <script type="text/javascript">

        function confirm_delete(url)
        {
            $('#confirm-delete').modal('show', {backdrop: 'static'});
            $('#my-confirm-delete-form').attr('action', url);
        }

        function copy_link_modal(url)
        {
            $('#invitation_link').val(url);
            $('#copy-url-modal').modal('show', {backdrop: 'static'});
        }
        function copy_link() {
            const input = $('#invitation_link')
            input.select();
            document.execCommand("copy");
            alert("Text copied to clipboard!");
        }
    </script>
@endsection
