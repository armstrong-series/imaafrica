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

    .np-image-upload-picker {
      padding: 20px;
      background: #eee;
      border-radius: 16px;
      margin: 10px;
      }
    np-image-preview {
      padding: 20px;
      background: #eee;
      border-radius: 16px;
      margin: 10px;
      }

    img.np-preview {
        background-color: #fff;
        /* border: 1px solid #ddd; */
        padding: 5px;
        height: 230px;
        width: 400px;
        margin: 10px;
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

    @import url('https://fonts.googleapis.com/css2?family=Lato:wght@300&display=swap');

    .music-container{
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 10px 0 grey;
        display: flex;
        padding: 10px 20px;
        position: relative;
        margin: 100px 0;
        /* z-index: 10; */
        width: 400px;
    }
    body{
        font-family: 'Lato', sans-serif;
    }
    .img-container{
       
        position: relative;
        width: 110px; 
    }

    .img-container img {
        border-radius: 50%;
        width: inherit;
        object-fit: cover;
        height: 110px;
        position: absolute;
        bottom: 0;
        left: 0;
        animation: rotate 3s linear infinite;
        animation-play-state: paused;
    }
    .music-container.play .img-container img{
        animation-play-state: running;
    }
    
    @keyframes rotate {
        from{
            transform: rotate(0deg)
        }
        to{
            transform: rotate(360deg)
        }
    }



    .navigation{
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }

    .action-btn{
        background-color: #fff;
        border: 0;
        font-size: 20px;
        color: silver;
        cursor: pointer;
        padding: 10px;
        margin: 0 20px;
    }
    .action-btn .action-btn-big{
        color: #cdc2d0;
        font-size: 30px;
    }
    .action-btn:focus{
        outline: none;
    }

    .music-info{
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 15px 15px 0 0;
        position: absolute;
        opacity: 0;
        width: calc(100% - 40px);
        padding: 10px 10px 10px 150px;
        top: 0;
        left: 20px;
        transform: translateY(0%);
        transition: transform .3s ease-in, opacity .3s ease-in;
        z-index: 0;

    }
    .music-info h4{
        margin: 0;
    }
    .progress-container{
        background-color: #fff;
        border-radius: 5px;
        cursor: pointer;
        margin: 10px 0;
        height: 5px;
        width: 100%;
    }

    .music-container.play .music-info{
        opacity: 1;
        transform: translateY(-100%);
    }

    .progress{
        height: 100%;
        border-radius: 5%;
        width:50%;
        background-color: green;
        transition: width .1s linear;

    }
    

    
</style>

@endsection

@section('content')

<main id="playListTrack">



    <div class="input-group input-group-lg searchBar">
        <input type="text" class="form-control" v-model="filterTracks" @keyup="searchTrack()" id="btnItems" placeholder="Search Audios By Category...">
        <div  v-for="result in results" class="list-group" style="width:800px; border-radius:9px; box-shadow: 4px 4px 4px grey;">
            <a href="javascript:void(0);" class="list-group-item" style="color: gray;">
            <strong v-cloak>@{{ result.name }}</a>
        </div>
        <!-- <span class="input-group-btn">
            <button class="btn btn-main btn-flat" type="submit" id="btnSearch">
                <i class="glyphicon glyphicon-search"></i>
            </button>
        </span> -->
    </div>
          
    <header class="mb-4 d-flex align-items-center justify-content-between"></header>
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
                        <th scope="col">Title</th>
                        <th scope="col">Category</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(audio, index) in audios">
                        <td v-cloak>
                            <div class="music-container" id="music-coantainer">
                                <div class="music-info">
                                    <h4 id="title">@{{ audio.name }}</h4>
                                  <div class="progress-container" id="progresscontainer">
                                    <div id="progress" class="progress"></div>
                                  </div>
                                </div>
                             
                               <audio id="audio" :src="'/storage/audios/' + audio.file"></audio>
                               <div class="img-container">
                                    <!-- <img v-cloak id="cover" width="20" height="20" src="{{ asset('img/disc.png')}}" alt=""> -->
                                    <img v-cloak id="cover" width="20" height="20" :src="'/storage/audios/cover' + audio.img_cover" alt="">
                               </div>
                               <div class="navigation">
                                   <!-- <button id="prev" class="action-btn"><i class="fas fa-backward"></i></button> -->
                                   <button id="play" class="action-btn action-btn-big"><i class="fas fa-play"></i></button>
                                   <!-- <button id="next" class="action-btn"><i class="fas fa-forward"></i></button> -->
                               </div>
                            </div>
                        </td>
                        <td v-cloak>@{{ audio.title }}</td>
                        <td v-cloak>@{{ audio.category }}</td>
                        <td v-cloak>
                            <!-- Single button -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-th-large"></i> 
                                </button>
                                <ul class="dropdown-menu">    
                                    @if(Auth::user())                  
                                        <li role="separator" class="divider"></li>
                                        <li><a href="javascript:void(0);"  data-target="#changeTrack">Change File</a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="javascript:void(0);" @click="showDialogInfo(index)" data-toggle="modal"  data-target="#edit-track">Edit Details </a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="javascript:void(0)" @click="deletePlaylist(index)" class="alert-link">Delete</a></li>
                                    @endif
                                    <li>
                                        <a href="javascript:void(0);" @click="downloadTrack(audio.file)" title="Download" class="alert-link">Download
                                    </li>
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
                        <label for="">Title</label>
                        <input type="text" class="form-control" placeholder="Track Title" v-model="audio.title">
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

                        <div class="form-group">
                            <div  v-if="imageFile == null || imageFile.length == 0" class="fileUpload px-5 py-2 mt-5 btn-md btn-primary" style="height:20px; color:white; padding: 2px; border-radius:3px;">
                                 <span> Audio Cover</span>&nbsp;&nbsp;<i v-if="!imageFile"></i>
                                <input type="file" id="uploadBtn file" name="img_cover" accept="image/*" @change="audioCoverPreview($event)" class="form-control upload">      
                            </div>
                            <div class="text-center" v-if="imageFile != null && imageFile.length != 0" >
                                <img class="np-preview" :src="imageFile" />
                            </div> 

                            <div v-if="imageFile != null && imageFile.length != 0 && !isImageUploading">
                                <button class="btn-sm btn-default" v-on:click="clearImage()" title="Remove">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button v-if="!isLoading" type="button" @click="sendAudio()"  class="btn btn-primary px-4 py-2">Proceed</button>
                        <button  v-if="!isLoading" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                        <label for="">Title</label>
                        <input type="text" class="form-control" placeholder="Give a name" v-model="audioEdit.title">
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
        <textarea name="" style="display:none;" id="download" cols="30" rows="10">{{ route('users.download.track') }}</textarea>
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
      const musicContainer =  document.getElementById("music-coantainer");
      const progressContainer =  document.getElementById("progress-coantainer");
      const playBtn =  document.getElementById("play");
      const prevBtn =  document.getElementById("prev");
      const nextBtn =  document.getElementById("next");
      const audio =  document.getElementById("audio");
      
      const title =  document.getElementById("title");


      const songs = [];
      let songIndex = 2;
      loadSong(songs[songIndex]);

      function loadSong(song){
         title.innerText = song; 
         audio.src = `storage/audios/${song}`;
      }

      playBtn.addEventListener('click', () => {
          const isPlaying = musicContainer.classList.contains("play");
        //   isPlaying ? pauseSong() : playSong();
        
          if(isPlaying){
            pauseSong();
          }else{
            playSong();
          }
      });

      function playSong(){
          musicContainer.classList.add("play");
          playBtn.querySelector("i.fas").classList.remove("fa-play");
          playBtn.querySelector("i.fas").classList.add("fa-pause");

          audio.play();
      }


      function pauseSong(){
          musicContainer.classList.add("play");
          playBtn.querySelector('i.fas').classList.add("fa-play");
          playBtn.querySelector('i.fas').classList.remove("fa-pause");

          audio.pause();
      }



  </script>
    <script>

        $(function(){
            $('a.has-spinner, button.has-spinner').click(function() {
                $(this).toggleClass('active');
            });
        });
    </script>
@endsection
