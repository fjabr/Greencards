@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <!--<h1 class="h3">Contracts</h1>-->
            
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('add_notifications_mobile') }}" class="btn btn-primary">
                <span>Add New Notification</span>
            </a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-block d-md-flex">
        <h5 class="mb-0 h6">Notification For Mobile Users إخطار مستخدمي الجوال</h5>
        
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th >#</th>
                        <th width="30%">For</th>
                        <th>Text</th>
                        <th>Created at</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach($notifications as $notif)
                        <tr>
                            <td>{{ $notif->id }}</td>
                            <td>
                                @if($notif->for_all == 1)
                                    For all customers
                                @else
                                    <?php
                                        $ids = explode(',', $notif->users);
                                        echo "<ol>";
                                        foreach($ids as $id){
                                            
                                            foreach($customers as $cs){
                                                
                                                if($id == $cs->id){
                                                    echo "<li><ul>";
                                                    echo "<li>ID: ".$id."</li><li>Name: ".$cs->name."</li>";
                                                    echo "</ul></li>";
                                                }
                                            }
                                        }
                                        echo "</ol>";
                                    ?>
                                @endif
                            </td>
                            <td>{{ $notif->text }}</td>
                            <td>{{ $notif->created_at }}</td>
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

