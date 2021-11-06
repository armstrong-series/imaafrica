@extends('app')

@section('title'){{ trans('users.upload').' - ' }}@endsection

@section('css')
<link href="{{ asset('plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/tagsinput/jquery.tagsinput.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('styles')
    <style>
        body{
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: #5256ad;
        }
        .drag-area{
            border: 2px dashed #fff;
            height: 500px;
            width: 700px;
            border-radius:5px;
            display:flex;
            align-items:center;
            justify-content: center;
            flex-direction: column;
        }
        .drag-area .icon header{
            font-size:30px;
            font-weight: 500;
            color:#fff;
        }
        .drag-area span{
          margin:10px 0 15px 0;
          font-size:25px;
          font 
        }
    </style>
@endsection
@section('content')

<main id="video">
    <div class="drag-area">
        <div class="icon">
             <i class="fas fa-cloud-upload-alt"></i>
        </div>
        <header>Drag and Drop File to Upload</header>
        <span>OR</span>
        <input type="file" class="form-control">
    </div> 
</main>
@endsection

@section('javascript') 
    <script src="{{ asset('js/app/upload.js')}}"></script>
	<script src="{{ asset('plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('plugins/tagsinput/jquery.tagsinput.min.js') }}" type="text/javascript"></script>
@endsection
