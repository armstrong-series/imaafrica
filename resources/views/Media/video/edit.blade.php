@extends('app')

@section('title'){{ trans('users.upload').' - ' }}@endsection

@section('css')
<link href="{{ asset('plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/tagsinput/jquery.tagsinput.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('styles')
    <style>
   
   
    </style>
@endsection
@section('content')
<main id="media">
 
    <div class="vid-main-wrapper clearfix row">
        @csrf
       <div class="col-md-8">
            <div class="vid-container">
                <video id="video-player" style="padding:20px; border-radius:9%; width:60%; height:58%;" playsinline controls  loop >
                     <source src="/storage/videos/{{ $video->file }}" type="video/mp4"> 
                    
                </video>    
            </div>
       </div>

            <div  class="vid-container"style="padding-top:25px;">
                <div class="col-md-4" >
                    <div  class="vid-container"style="padding-right:25px;">
                        <div class="form-group">
                            <input type="text" v-model="videoEdit.title" class="form-control" placeholder="Enter title">
                        </div>

                        <div class="form-group">
                            <input type="text" v-model="videoEdit.category"  class="form-control" placeholder="Categorize Video">
                        </div>

                        <div class="form-group">
                            <input type="text" v-model="videoEdit.contributor" class="form-control" placeholder="Name of Contributor">
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="button" style="width:200px; box-shadow: 3px 4px 3px grey;" @click="updateVideoDetails()"class="btn btn-block btn-info">Update Details</button>
                    </div>
                    <!-- <a v-if="isLoading" class="btn btn-info has-spinner" style="width:75px">
                        Loading...
                        <i class="fa fa-spinner fa-spin"></i>
                    </a> -->
                </div>
            </div>

    </div>
    <textarea name="" id="updateDetails"  style="display:none;" cols="30" rows="10">{{ route('users.video-details.update') }}</textarea>
    <textarea name="" id="videos"  style="display:none;" cols="30" rows="10">{{ json_encode($video) }}</textarea>

</main>
@endsection




@section('javascript')
<script src="{{ asset('library/vue.js') }}"></script>
    <script src="https://unpkg.com/vue-toastr/dist/vue-toastr.umd.min.js"></script>
	<script src="{{ asset('library/axios.min.js') }}"></script>
    <script src="{{ asset('js/app/videos.js')}}"></script>
	<script src="{{ asset('plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('plugins/tagsinput/jquery.tagsinput.min.js') }}" type="text/javascript"></script>
@endsection
