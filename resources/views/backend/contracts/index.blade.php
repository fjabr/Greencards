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
        <h5 class="mb-0 h6">Contracts</h5>
        
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th >#</th>
                        <th>User Name</th>
                        <th>Job title</th>
                        <th>Contract File</th>
                        <th>Status</th>
                        <th  class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contracts as  $contract)
                        <tr>
                            <td>{{ $contract->id }}</td>
                            <td>{{ $contract->name }}</td>
                            <td>{{ $contract->job_name }} </td>
                            <td>
                                @if($contract->file_url !== null)
                                    <a href="{{ asset("public/".$contract->file_url) }}" target="_blanck" class="btn btn-primary">
                                        <i class="las la-file-pdf aiz-side-nav-icon"></i>    
                                    </a>
                                @else
                                
                                waiting to upload contract
                                
                                @endif
                            </td>
                            <td>
                                @if($contract->status == 2)
                                    <button class="btn btn-success" style="color: white;">Approved</button>
                                @elseif($contract->status == -1)
                                    <button class="btn btn-danger" style="color: white;">Refused</button>
                                @else
                                    <button class="btn btn-warning" style="color: white;">Processing</button>
                                @endif
                            </td>
                            <td class="text-right">
                                @if($contract->status == 2)
                                     <!--<a href="{{route('refuse_contract', ['id'=>$contract->id] )}}" class="btn btn-warning">Refuse</a>-->
                                     <button onclick="deleteContarct({{$contract->id}}, '{{$contract->name}}')"  class="btn btn-warning">Refuse</button>
                                     
                                @elseif($contract->status == -1)
                                    <a href="{{route('approv_contract', ['id'=>$contract->id] )}}" class="btn btn-success">Approve</a>
                                @else
                                    <a href="{{route('approv_contract', ['id'=>$contract->id] )}}" class="btn btn-success">Approve</a>
                                    <!--<br/>-->
                                    <button onclick="deleteContarct({{$contract->id}}, '{{$contract->name}}')"  class="btn btn-warning">Refuse</button>
                                    <!--<a href="{{route('refuse_contract', ['id'=>$contract->id] )}}" class="btn btn-warning">Refuse</a>-->
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        async function deleteContarct(id, name){
            const { value: text } = await Swal.fire({
              input: 'textarea',
              inputLabel: 'Message to '+name,
              inputPlaceholder: 'Type your message here...',
              inputAttributes: {
                'aria-label': 'Type your message here'
              },
              showCancelButton: true
            })
            if (text) {
                $.post( "/admin/refuse_contract",
                    { message: text, id: id, "_token": "{{ csrf_token() }}",}
                )
                .done(function( data ) {
                    console.log(data);
                    if(data["success"]){
                        location.reload();
                    }else{
                        Swal.fire(
                            'Error! Try again',
                            'error'
                        )
                    }
                });
            }else {
                Swal.fire(
                    'Message is required!',
                    'error'
                )
            }
        }
    </script>
</div>
@endsection


@section('modal')
    @include('modals.delete_modal')
@endsection

