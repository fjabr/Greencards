@extends('backend.layouts.app')
@section('content')

<div class="row">
	<div class="col-xl-10 mx-auto">
		<h6 class="fw-600">{{ translate('Mobile banner Settings') }}</h6>

		{{-- Big sales Slider --}}
		<div class="card">
            <ul class="nav nav-tabs nav-fill border-light">
                @foreach (\App\Models\Language::all() as $key => $language)
                  <li class="nav-item">
                    <a class="nav-link text-reset @if (isset($lang) && $language->code == $lang) active @else bg-soft-dark border-light border-left-0 @endif py-3" href="{{ route('mobileSetup.banners', ['lang'=> $language->code] ) }}">
                      <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
                      <span>{{$language->name}}</span>
                    </a>
                  </li>
                    @endforeach
              </ul>
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Big sales') }}</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-info">
					{{ translate('We have limited banner height to maintain UI. We had to crop from both left & right side in view for different devices to make it responsive. Before designing banner keep these points in mind.') }}
				</div>
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-group">
						<label>{{ translate('Photos & values') }}</label>
						<div class="home-slider-target">
							<input type="hidden" name="types[][{{ $lang }}]" value="big_salles_image">
							<input type="hidden" name="types[][{{ $lang }}]" value="big_salles_value">
							@if (get_setting('big_salles_image','',$lang) != null && get_setting('big_salles_value','',$lang) != null)
								@foreach (json_decode(get_setting('big_salles_image','',$lang), true) as $key => $value)
									<div class="row gutters-5">
										<div class="col-md-5">
											<div class="form-group">
												<div class="input-group" data-toggle="aizuploader" data-type="image">
					                                <div class="input-group-prepend">
					                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
					                                </div>
					                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
													<input type="hidden" name="types[][{{ $lang }}]" value="big_salles_image">
					                                <input type="hidden" name="big_salles_image[]" class="selected-files" value="{{ json_decode(get_setting('big_salles_image','',$lang), true)[$key] }}">
					                            </div>
					                            <div class="file-preview box sm">
					                            </div>
				                            </div>
										</div>
										<div class="col-md">
											<div class="form-group">
												<input type="hidden" name="types[][{{ $lang }}]" value="big_salles_value">
												<input type="text" class="form-control" placeholder="75" name="big_salles_value[]" value="{{ json_decode(get_setting('big_salles_value','',$lang), true)[$key] }}">
											</div>
										</div>
										<div class="col-md-auto">
											<div class="form-group">
												<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
													<i class="las la-times"></i>
												</button>
											</div>
										</div>
									</div>
								@endforeach
							@endif
						</div>
						<button
							type="button"
							class="btn btn-soft-secondary btn-sm"
							data-toggle="add-more"
							data-content='
							<div class="row gutters-5">
								<div class="col-md-5">
									<div class="form-group">
										<div class="input-group" data-toggle="aizuploader" data-type="image">
											<div class="input-group-prepend">
												<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
											</div>
											<div class="form-control file-amount">{{ translate('Choose File') }}</div>
											<input type="hidden" name="types[][{{ $lang }}]" value="big_salles_image">
											<input type="hidden" name="big_salles_image[]" class="selected-files">
										</div>
										<div class="file-preview box sm">
										</div>
									</div>
								</div>
								<div class="col-md">
									<div class="form-group">
										<input type="hidden" name="types[][{{ $lang }}]" value="big_salles_value">
										<input type="text" class="form-control" placeholder="75" name="big_salles_value[]">
									</div>
								</div>
								<div class="col-md-auto">
									<div class="form-group">
										<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
											<i class="las la-times"></i>
										</button>
									</div>
								</div>
							</div>'
							data-target=".home-slider-target">
							{{ translate('Add New') }}
						</button>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>

		{{-- end  Big sales Slider --}}



        {{-- Product less than price Slider --}}
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Product  less than price') }}</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-group">
						<label>{{ translate('Photos & values') }}</label>
						<div class="home-slider-target">
							<input type="hidden" name="types[][{{ $lang }}]" value="product_less_than_price_image">
							<input type="hidden" name="types[][{{ $lang }}]" value="product_less_than_price_value">
							@if (get_setting('product_less_than_price_image','',$lang) != null && get_setting('product_less_than_price_value','',$lang) != null)
								@foreach (json_decode(get_setting('product_less_than_price_image','',$lang), true) as $key => $value)
									<div class="row gutters-5">
										<div class="col-md-5">
											<div class="form-group">
												<div class="input-group" data-toggle="aizuploader" data-type="image">
					                                <div class="input-group-prepend">
					                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
					                                </div>
					                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
													<input type="hidden" name="types[][{{ $lang }}]" value="product_less_than_price_image">
					                                <input type="hidden" name="product_less_than_price_image[]" class="selected-files" value="{{ json_decode(get_setting('product_less_than_price_image','',$lang), true)[$key] }}">
					                            </div>
					                            <div class="file-preview box sm">
					                            </div>
				                            </div>
										</div>
										<div class="col-md">
											<div class="form-group">
												<input type="hidden" name="types[][{{ $lang }}]" value="product_less_than_price_value">
												<input type="text" class="form-control" placeholder="75" name="product_less_than_price_value[]" value="{{ json_decode(get_setting('product_less_than_price_value','',$lang), true)[$key] }}">
											</div>
										</div>
										<div class="col-md-auto">
											<div class="form-group">
												<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
													<i class="las la-times"></i>
												</button>
											</div>
										</div>
									</div>
								@endforeach
							@endif
						</div>
						<button
							type="button"
							class="btn btn-soft-secondary btn-sm"
							data-toggle="add-more"
							data-content='
							<div class="row gutters-5">
								<div class="col-md-5">
									<div class="form-group">
										<div class="input-group" data-toggle="aizuploader" data-type="image">
											<div class="input-group-prepend">
												<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
											</div>
											<div class="form-control file-amount">{{ translate('Choose File') }}</div>
											<input type="hidden" name="types[][{{ $lang }}]" value="product_less_than_price_image">
											<input type="hidden" name="product_less_than_price_image[]" class="selected-files">
										</div>
										<div class="file-preview box sm">
										</div>
									</div>
								</div>
								<div class="col-md">
									<div class="form-group">
										<input type="hidden" name="types[][{{ $lang }}]" value="product_less_than_price_value">
										<input type="text" class="form-control" placeholder="75" name="product_less_than_price_value[]">
									</div>
								</div>
								<div class="col-md-auto">
									<div class="form-group">
										<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
											<i class="las la-times"></i>
										</button>
									</div>
								</div>
							</div>'
							data-target=".home-slider-target">
							{{ translate('Add New') }}
						</button>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>

		{{-- end Product less than price Slider --}}
		{{-- start slider greencart app --}}
        <div class="card">
			<div class="card-header">
				<h6 class="mb-0">{{ translate('Slider Green cart app') }}</h6>
			</div>
			<div class="card-body">
				<form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="form-group">
						<label>{{ translate('Photos & values') }}</label>
						<div class="home-slider-target">
							<input type="hidden" name="types[][{{ $lang }}]" value="slider_greencard_app_image">
							<input type="hidden" name="types[][{{ $lang }}]" value="slider_greencard_app_link">
							@if (get_setting('slider_greencard_app_image','',$lang) != null && get_setting('slider_greencard_app_link','',$lang) != null)
								@foreach (json_decode(get_setting('slider_greencard_app_image','',$lang), true) as $key => $value)
									<div class="row gutters-5">
										<div class="col-md-5">
											<div class="form-group">
												<div class="input-group" data-toggle="aizuploader" data-type="image">
					                                <div class="input-group-prepend">
					                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
					                                </div>
					                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
													<input type="hidden" name="types[][{{ $lang }}]" value="slider_greencard_app_image">
					                                <input type="hidden" name="slider_greencard_app_image[]" class="selected-files" value="{{ json_decode(get_setting('slider_greencard_app_image','',$lang), true)[$key] }}">
					                            </div>
					                            <div class="file-preview box sm">
					                            </div>
				                            </div>
										</div>
										<div class="col-md">
											<div class="form-group">
												<input type="hidden" name="types[][{{ $lang }}]" value="slider_greencard_app_link">
												<input type="text" class="form-control" placeholder="Link" name="slider_greencard_app_link[]" value="{{ json_decode(get_setting('slider_greencard_app_link','',$lang), true)[$key] }}">
											</div>
										</div>
										<div class="col-md-auto">
											<div class="form-group">
												<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
													<i class="las la-times"></i>
												</button>
											</div>
										</div>
									</div>
								@endforeach
							@endif
						</div>
						<button
							type="button"
							class="btn btn-soft-secondary btn-sm"
							data-toggle="add-more"
							data-content='
							<div class="row gutters-5">
								<div class="col-md-5">
									<div class="form-group">
										<div class="input-group" data-toggle="aizuploader" data-type="image">
											<div class="input-group-prepend">
												<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
											</div>
											<div class="form-control file-amount">{{ translate('Choose File') }}</div>
											<input type="hidden" name="types[][{{ $lang }}]" value="slider_greencard_app_image">
											<input type="hidden" name="slider_greencard_app_image[]" class="selected-files">
										</div>
										<div class="file-preview box sm">
										</div>
									</div>
								</div>
								<div class="col-md">
									<div class="form-group">
										<input type="hidden" name="types[][{{ $lang }}]" value="slider_greencard_app_link">
										<input type="text" class="form-control" placeholder="Link" name="slider_greencard_app_link[]">
									</div>
								</div>
								<div class="col-md-auto">
									<div class="form-group">
										<button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".row">
											<i class="las la-times"></i>
										</button>
									</div>
								</div>
							</div>'
							data-target=".home-slider-target">
							{{ translate('Add New') }}
						</button>
					</div>
					<div class="text-right">
						<button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
					</div>
				</form>
			</div>
		</div>
		{{-- end Pslider greencart app --}}


	</div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
		$(document).ready(function(){
		    AIZ.plugins.bootstrapSelect('refresh');
		});
    </script>
@endsection
