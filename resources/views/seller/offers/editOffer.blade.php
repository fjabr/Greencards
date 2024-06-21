@extends('seller.layouts.app')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Dashboard') }}</h1>
            </div>

        </div>
    </div>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <div class="card" id="containerAdd">
        <div class="card-body" style="padding: 10px; background-color: white">
               @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            <form method="POST" action="/update_offer">
                    <div class="form-row">
                          <div class="form-group col-md-6">
                              <label for="name">Type</label>

                              <select class="form-control" name="type" id="type">
                                  <?php
                                            $allowed_types = explode(",", $contract->offers);
                                    ?>
                                  @foreach($types_offers as $type)
                                      <?php
                                            if(in_array($type->id, $allowed_types)){
                                                if($offer->type_id == $type->id){
                                                    echo "<option value='".$type->id."' selected>".$type->name."---".$type->name_ar."</option>";
                                                }else{
                                                    echo "<option value='".$type->id."'>".$type->name."---".$type->name_ar."</option>";
                                                }

                                            }
                                        ?>
                                  @endforeach
                              </select>
                            </div>
                     <!--</div>-->
                  <!--<div class="form-row">-->
                      @csrf
                      <input type="hidden" class="form-control" id="id" name="id" value="{{ $offer->id }}" >
                    <!--<div class="form-group col-md-6">-->
                    <!--  <label for="name">Name</label>-->
                    <!--  <input type="text" class="form-control" id="name" name="name" placeholder="Name Offer" value="{{ $offer->title }}" >-->
                    <!--</div>-->
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="description">Description</label>
                      <input type="text" required class="form-control" id="description" name="description" placeholder="Description" value="{{ $offer->description }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="description">Description Arabic</label>
                        <input type="text" required class="form-control" id="description" name="description_ar" placeholder="Description Arabic" value="{{ $offer->description_ar }}">
                      </div>
                    </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="points">Number of points</label>
                      <input type="number" required class="form-control" id="points" name="points" value="{{ $offer->nb_points }}" placeholder="points">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="limitless">Limitless</label>

                        <select class="form-control" id="limitless" name="limitless" required>
                            <option value="1"
                              @if ($offer->ilimitless_usage == 1)
                                  selected
                              @endif
                            >YES</option>
                            <option value="0"
                              @if ($offer->ilimitless_usage == 0)
                                  selected
                              @endif
                            >NO</option>
                        </select>
                      </div>
                </div>
                <div class="form-row">

                    <div class="form-group col-md-6">
                      <label for="usage">Number Of Usage</label>
                      <input type="number" class="form-control" id="usage" name="usage" placeholder="usage" value="{{ $offer->member_of_usage }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Offer</button>
                <a href="/offers" id="cancelForm" class="btn btn-default">Cancel</a>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>



@endsection
