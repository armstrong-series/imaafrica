@extends('app')

@section('title'){{ trans('users.upload').' - ' }}@endsection

@section('css')
<link href="{{ asset('plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/tagsinput/jquery.tagsinput.min.css') }}" rel="stylesheet" type="text/css" />
<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.1.1/min/dropzone.min.css" rel="stylesheet"/> -->
@endsection

@section('styles')
<style>
    
    
    .has-spinner .fa-spinner {
    opacity: 0;
    max-width: 0;

    -webkit-transition: opacity 0.25s, max-width 0.45s; 
    -moz-transition: opacity 0.25s, max-width 0.45s;
    -o-transition: opacity 0.25s, max-width 0.45s;
    transition: opacity 0.25s, max-width 0.45s; 
    }

    .has-spinner.active {
    cursor:progress;
    }

    .has-spinner.active .fa-spinner {
    opacity: 1;
    max-width: 50px; 
    }

      .simple-icon-reload{
        font-size:30px;
        color: gray;
        cursor: pointer;
      }
      .fa-check-circle{
        font-size:40px;
        color: blue;
      }
      

</style>
@endsection

@section('content')

<main id="media">
    <div>
        @if(Auth::user())  
        <header class="mb-4 d-flex align-items-center justify-content-between">
            <h3 class="mb-0">Videos</h3>
            <button data-target="#videoUpload"  data-toggle="modal" class="btn btn-primary px-4 py-2">Create New</button>
        </header>
        @endif
        <div class="container margin-bottom-40 padding-top-40"  >
            <div class="row" >
                <div class="col-md-4" v-for="(video, index) in videos"  style="padding:8px;">
                    <div class="card"  style="width: 300px; box-shadow: 2px 3px 3px grey;">
                       
                        <div class="card-body">
                            <video v-cloak width="300" height="220" controls>
                                <source :src="'/storage/videos/' + video.file">  
                            </video>
                            <h5 v-cloak class="card-title" style="text-align: center;">@{{ video.title }}</h5>
                            <div v-cloak class="btn-group">
                             
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-th-large"></i> 
                                </button>
                                <ul class="dropdown-menu">   
                                    <!-- <li><a href="javascript:void(0)">View</a></li> -->
                                    @if(Auth::user())           
                                    <li><a :href="url.video.edit + video.uuid" data-toggle="modal">Edit </a></li>
                                    <li role="separator" class="divider"></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="javascript:void(0)" @click="deleteVideo(index)" class="alert-link">Delete</a></li>
                                    @endif
                                    <li><a href="javascript:void(0)" @click="downloadVideo(video.file)"  title="Download" class="alert-link">Download</li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- row -->
        </div><!-- container -->
    </div>


    <div class="modal fade" id="videoUpload" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        @csrf
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <!-- <h4 class="modal-title" id="modalLabel">Add Video</h4> -->
                </div>
                <div class="modal-body">
                    <section class="text-center">
                        <div class="d-flex flex-column justify-content-center" style="height: 300px;">
                            <!-- <header>
                                <h3 class="mb-4 font-weight-light">File Upload</h3>
                                <p>Upload your video here</p>
                            </header> -->

                            <div class="mx-auto mt-5 w-75 " >
                            <div class="fallback dropzone dz-clickable" @dragover="dragover" @dragleave="dragleave"  @drop="drop"
                            style="height: 300px; cursor: pointer; background: #F9F9F9; border: 2px dashed #066CF2;"> 
                                <input type="file" @change="onChange()" ref="file" accept="video/*"
                                name="" id="assetsFieldHandle">


                                <label  for="assetsFieldHandle"  class="dz-default dz-message needsclick p-5 d-block d-flex flex-column justify-content-center">
                                    <div class="mb-3 align-self-center"  >
                                        <svg width="91" height="71" viewBox="0 0 91 71" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g opacity="0.802223">
                                            <path d="M75.8195 23.3279C71.6137 6.8156 54.6222 -3.21007 37.8675 0.934916C24.7742 4.17423 15.267 15.327 14.2874 28.5961C4.99062 30.1071 -1.30316 38.7595 0.229987 47.9219C1.59288 56.0673 8.75445 62.0326 17.1309 61.9999H31.3481V56.3952H17.1309C10.8493 56.3952 5.7571 51.3766 5.7571 45.1859C5.7571 38.9952 10.8493 33.9766 17.1309 33.9766C18.7013 33.9766 19.9743 32.722 19.9743 31.1743C19.9601 17.2452 31.4062 5.94197 45.5397 5.92813C57.7743 5.91605 68.3062 14.4404 70.6729 26.2702C70.9066 27.4514 71.8792 28.3537 73.0898 28.5121C80.8634 29.6031 86.2675 36.698 85.1607 44.3591C84.1668 51.2384 78.2069 56.3605 71.1562 56.3952H59.7825V61.9999H71.1562C82.149 61.9671 91.0333 53.158 90.9999 42.3242C90.9722 33.306 84.7088 25.4681 75.8195 23.3279Z" fill="#67758D"/>
                                            <path d="M44.0521 34.8276L33 46.2115L36.8959 50.2244L43.2508 43.7071V71H48.7768V43.7071L55.1041 50.2244L59 46.2115L47.9479 34.8276C46.8702 33.7241 45.1298 33.7241 44.0521 34.8276Z" fill="#67758D"/>
                                            </g>
                                        </svg>
                                    </div>
                                    <p class="font-weight-light lead ">DRAG N DROP VIDEO FILE </p>
                                    <span class="underline needsclick text-primary">Browse from your computer  </span>
                                </label> 
                            
                                <ul class="mt-4" v-cloak>
                                    <li class="text-sm p-1">
                                        @{{ videoFile.name }}        
                                        <button v-if="videoFile" class="ml-2 btn-md btn-danger" type="button" @click.stop="remove()" title="Remove file">remove</button>
                                    </li>
                                </ul>
                            </div>   
                            </div>            
                        </div>
                                        
                    
                    </section>
                <!-- end of file upload -->
                </div>
                <div class="modal-footer">
                    <button v-if="!isLoading" type="button" @click="uploadVideo()" class="btn btn-primary px-4 py-2">Proceed</button>
                    <button  v-if="!isLoading" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    
                    <a v-if="isLoading" class="btn btn-info has-spinner" style="width:75px">
                        Loading...
                        <i class="fa fa-spinner fa-spin"></i>
                    </a>
            

                </div>
            </div>
        </div>
    </div>
    <textarea name="" id="videos"  style="display:none;" cols="30" rows="10">{{ json_encode($video) }}</textarea>
    <textarea name="" id="edit-video"  style="display:none;" cols="30" rows="10">{{ route('user.video.edit') }}</textarea>
    <textarea name="" id="uploadVideo"  style="display:none;" cols="30" rows="10">{{ route('users.videos.upload')}}</textarea>
    <textarea name="" id="deletVideo"  style="display:none;" cols="30" rows="10">{{ route('users.video.delete')}}</textarea>
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
