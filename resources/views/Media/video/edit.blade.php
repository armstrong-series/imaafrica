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

<main id="video-edit">
    <div class="vid-main-wrapper clearfix">
    <!-- THE YOUTUBE PLAYER -->
        <div class="vid-container">
            <video id="video-player" class="position-absolute h-100 w-100 d-flex align-items-center justify-content-center" style="border-radius: inherit;top: 0;left: 0;width: 100%;" playsinline controls poster="{{$video->video_thumbnail_path}}" loop >
             <source src="{{$video->video_path}}" type="video/mp4">
            <source src="{{$video->video_path}}" type="video/webm">
        </div>

        <!-- THE PLAYLIST -->
        <div class="vid-list-container">
            <ul id="vid-list">  
                <li>
                <a href="javascript:void();" >
                    <span class="vid-thumb"><img width=72 src="https://img.youtube.com/vi/cOSEOYi9JS4/default.jpg" /></span>
                    <div class="desc">WeatherBeater™ Product Video</div>
                </a>
                </li>
    
                
            </ul>
        </div>
    </div>
</main>
@endsection

<textarea name="" id="updateVideo"  style="display:none;" cols="30" rows="10"></textarea>
@section('javascript') 

    <script src="{{ asset('js/app/video.js')}}"></script>
	<script src="{{ asset('plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('plugins/tagsinput/jquery.tagsinput.min.js') }}" type="text/javascript"></script>
@endsection
