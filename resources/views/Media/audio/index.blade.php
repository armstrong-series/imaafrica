@extends('app')

@section('title'){{ trans('users.upload').' - ' }}@endsection


@section('css')
<link href="{{ asset('plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/tagsinput/jquery.tagsinput.min.css') }}" rel="stylesheet" type="text/css" />

@endsection


@section('styles')
<style>
    .fileUpload {
      position: relative;
      overflow: hidden;
      margin: 10px;
      }
    .fileUpload input.upload {
      position: absolute;
      top: 0;
      right: 0;
      margin: 0;
      padding: 0;
      font-size: 20px;
      cursor: pointer;
      opacity: 0;
      filter: alpha(opacity=0);
    }
    * {
    box-sizing: border-box;
    }


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
</style>

@endsection

@section('content')

<main id="playListTrack">
    <header class="mb-4 d-flex align-items-center justify-content-between">
     <div>
        <div class="container margin-bottom-40 padding-top-40">
            <button data-target="#audioUpload" title="Add a track" style="padding:8px;" data-toggle="modal" class="btn btn-primary">
              Add Track
            <!-- <i class="fas fa-headphones"></i> -->
            </button>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Track</th>
                        <th scope="col">Name</th>
                        <th scope="col">Category</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(audio, index) in audios">
                        
                        <td v-cloak>
                            <audio controls>
                                <!-- <source :src="audio.media_path">     -->
                                <source :src="'/storage/audios/' + audio.file">    
                            </audio>
                        </td>
                        <td v-cloak>@{{ audio.name }}</td>
                        <td v-cloak>@{{ audio.category }}</td>
                        <td v-cloak>
                            <!-- Single button -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-th-large"></i> 
                                </button>
                                <ul class="dropdown-menu">
                                    
                                    <!-- <li><a href="javascript:void(0)" >Clone Track</a></li> -->
                                    <li role="separator" class="divider"></li>
                                    <li><a href="javascript:void(0)"  data-target="#changeTrack" >Change File</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="javascript:void(0)" @click="showDialogInfo(index)" data-toggle="modal"  data-target="#edit-track">Edit Details </a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="javascript:void(0)" @click="downloadTrack(audio.file)" title="Download" class="alert-link">Download file</li>
                                    <li><a href="javascript:void(0)" @click="deletePlaylist(index)" class="alert-link">Delete</a></li>
                                </ul>
                            </div>
                          
                        </td>
                    </tr>
                </tbody>
            </table>

        </div><!-- container -->


        

        <div class="modal fade" id="audioUpload" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            @csrf
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="modalLabel">Audio Track</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" class="form-control" placeholder="Give a name" v-model="audio.name">
                        </div>
                        <div class="form-group">
                            <label for="">Category</label>
                            <input type="text" class="form-control" placeholder="Categorize your audio" v-model="audio.category">
                        </div>
                        <div class="form-group">
                            <div class="fileUpload px-5 py-2 mt-5 btn-md btn-secondary" style="box-shadow:3px 4px 4px grey; background:navy; color:white; border-radius:3px;">
                                <h5> &nbsp;&nbsp;<i class="fas fa-headphones"></i></h5>
                                <input type="file" id="uploadBtn file" name="audio_track" accept="audio/*" @change="handleFileUpload($event)" class="form-control upload">      
                            </div>
                            <div  class="text-center">
                                <audio id="audio-preview" style="box-shadow:3px 2px 3px grey;" controls v-show="file != ''"/>
                            </div> 
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button v-if="!isLoading" type="button" @click="sendAudio()"  class="btn btn-primary px-4 py-2">Proceed</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <!-- <div v-if="isLoading"> -->
                            <a v-if="isLoading" class="btn btn-success has-spinner" style="width:75px">
                                Loading...
                                <i class="fa fa-spinner fa-spin"></i>
                            </a>
                        <!-- </div>   -->
                    </div>
                </div>
            </div>
        </div>



        <div class="modal fade" id="edit-track" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            @csrf
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="modalLabel">Audio Track</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                        <label for="">Name</label>
                        <input type="text" class="form-control" placeholder="Give a name" v-model="audioEdit.name">
                        </div>
                        <div class="form-group">
                            <label for="">Category</label>
                            <input type="text" class="form-control" placeholder="Categorize your audio" v-model="audioEdit.category">
                        </div>
                      
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="updateTrackDetails()"  class="btn btn-primary px-4 py-2">Proceed</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <div v-if="isLoading" class="spinner-border text-info" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <textarea name="" style="display:none;" id="playlists" cols="30" rows="10">{{ json_encode($playlist) }}</textarea>
        <textarea name="" style="display:none;" id="createAudio" cols="30" rows="10">{{ route('user.audio.upload') }}</textarea>
        <textarea name="" style="display:none;" id="updateAudio" cols="30" rows="10">{{ route('user.audio.update') }}</textarea>
        <textarea name="" style="display:none;" id="delete" cols="30" rows="10">{{ route('users.delete-track') }}</textarea>
        <textarea name="" style="display:none;" id="download" cols="30" rows="10">{{ route('users.downlad.track') }}</textarea>
   </div>
  
</main>
@endsection

@section('javascript')


    <script src="{{ asset('library/vue.js') }}"></script>
    <script src="https://unpkg.com/vue-toastr/dist/vue-toastr.umd.min.js"></script>
	<script src="{{ asset('library/axios.min.js') }}"></script>
    <script src="{{ asset('js/app/upload.js')}}"></script>
	<script src="{{ asset('plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('plugins/tagsinput/jquery.tagsinput.min.js') }}" type="text/javascript"></script>

    <script>

        $(function(){
            $('a.has-spinner, button.has-spinner').click(function() {
                $(this).toggleClass('active');
            });
        });
    </script>
@endsection
