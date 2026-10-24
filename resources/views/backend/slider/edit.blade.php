@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<section class="content-main">
    <div class="row">
        <div class="col-md-10 offset-1">
            <div class="content-header">
                <h2 class="content-title">Slider Edit</h2>
                <div class="">
                    <a href="{{ route('slider.index') }}" class="btn btn-primary p-3" title="Slider List"><i class="fa fa-list"></i> </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-sm-10">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form method="post" action="{{ route('slider.update', $slider->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                   <label for="title_en" class="col-form-label col-md-2" style="font-weight: bold;"> Title (English):</label>
                                    <input class="form-control" id="title_en" type="text" name="title_en" placeholder="Write Slider Title English"  value="{{ $slider->title_en }}">
                                </div>
                                <div class="mb-4">
                                  <label for="title_bn" class="col-form-label col-md-2" style="font-weight: bold;"> Title (Bangla):</label>
                                    <input class="form-control" id="title_bn" type="text" name="title_bn" placeholder="Write Slider Title bangla"  value="{{ $slider->title_bn }}">
                                </div>
                                <div class="mb-4">
                                  <label for="slider_url" class="col-form-label col-md-2" style="font-weight: bold;"> Slider Url:</label>
                                    <input class="form-control" id="slider_url" type="text" name="slider_url" placeholder="Write Slider Url"  value="{{ $slider->slider_url }}">
                                </div>

                                <div class="mb-4">
                                  <label for="description_en" class="col-form-label col-md-3" style="font-weight: bold;">Description (English):</label>
                                    <textarea name="description_en" id="description_en" cols="5" placeholder="Write Slider description english" class="form-control " >{{ $slider->description_en }}</textarea>
                                    @error('description_en')
		                            	<span class="text-danger">{{ $message }}</span>
		                            @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="description_bn" class="col-form-label col-md-3" style="font-weight: bold;">Description (Bangla):</label>
                                    <textarea name="description_bn" id="description_bn" cols="5" placeholder="Write Slider description english" class="form-control " >{{ $slider->description_bn }}</textarea>
                                </div>
                                <div class="mb-4">
                                    <label for="image" class="form-label fw-bold">Desktop Slider Image:</label>
                                    <input name="slider_img" class="form-control" type="file" id="image" accept="image/jpeg,image/png,image/webp">
                                    <small class="form-text text-muted">Current image:</small><br>
                                    <img class="rounded mt-2" src="{{ asset($slider->slider_img) }}" alt="Current desktop slider" width="180" style="max-height:90px;object-fit:contain">
                                    <small class="form-text text-muted d-block">Upload a wide image for desktop screens. JPG, PNG, or WebP; maximum 20 MB.</small>
                                    @error('slider_img')
                                        <span class="text-danger d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="mobile_image" class="form-label fw-bold">Mobile Slider Image (optional):</label>
                                    <input name="slider_img_mobile" class="form-control" type="file" id="mobile_image" accept="image/jpeg,image/png,image/webp">
                                    @if($slider->slider_img_mobile)
                                        <small class="form-text text-muted">Current image:</small><br>
                                        <img class="rounded mt-2" src="{{ asset($slider->slider_img_mobile) }}" alt="Current mobile slider" width="100" style="max-height:140px;object-fit:contain">
                                    @else
                                        <small class="form-text text-muted">No separate mobile image yet; desktop image is currently used on mobile.</small>
                                    @endif
                                    <small class="form-text text-muted d-block">Upload a portrait image for mobile screens. Leave empty to keep the current image.</small>
                                    @error('slider_img_mobile')
                                        <span class="text-danger d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="form-check-input me-2 cursor" name="status" id="status" value="1" {{ $slider->status == 1 ? 'checked': '' }} >
                                        <label class="form-check-label cursor" for="status">Status</label>
                                    </div>
                                </div>
                                <div class="row mb-4 justify-content-sm-end">
                                    <div class="col-lg-3 col-md-4 col-sm-5 col-6">
                                        <input type="submit" class="btn btn-primary" value="Update">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- .row // -->
                </div>
                <!-- card body .// -->
            </div>
            <!-- card .// -->
        </div>
    </div>
</section>
@endsection
