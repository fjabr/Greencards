@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">New Notification</h5>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('save_notifications_mobile') }}" method="POST" enctype="multipart/form-data">
                	@csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Text')}}</label>
                        <div class="col-md-9">
                            <!--<input type="text" placeholder="{{translate('Text')}}" id="textNotif" name="textNotif" class="form-control" required>-->
                            <textarea id="textNotif" name="textNotif" class="form-control" required row="5"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('For all')}}</label>
                        <div class="col-md-9 row">
                            <div class="col-6">
                                <input type="radio" checked value="1" id="for_all_yes" name="for_all" onclick="handleClick(this);" > YES
                            </div>
                            <div class="col-6">
                                <input type="radio" value="0" id="for_all_no" name="for_all" onclick="handleClick(this);" > No
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Customers')}}</label>
                        <div class="col-md-9">
                            <select id="users" name="users[]" class="form-control" multiple>
                                @foreach($customers as $cs)
                                    <option value="{{ $cs->id }}" >ID: {{ $cs->id }} | Name: {{ $cs->name }}</option>
                                @endforeach
                            </select>
                            <span><pre>Use ctrl to select multiple</pre> </span>
                        </div>
                    </div>
                    
                    
                    
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            const s = document.getElementById("users");
            s.setAttribute("disabled","");
            
            var currentValue = 1;
            function handleClick(myRadio) {
                // alert('Old value: ' + currentValue);
                // alert('New value: ' + myRadio.value);
                currentValue = myRadio.value;
                if(currentValue == 1){
                    s.setAttribute("disabled","");
                }
                
                if(currentValue == 0){
                    s.removeAttribute("disabled");
                }
            }
        </script>
    </div>
</div>

@endsection
