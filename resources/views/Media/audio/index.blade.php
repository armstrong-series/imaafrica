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


   
    .has-spinner.active {
    cursor:progress;
    }

    .has-spinner.active .fa-spinner {
    opacity: 1;
    max-width: 50px; 
    }

    @import url('https://fonts.googleapis.com/css2?family=Lato:wght@300&display=swap');

    
   
$player-height: 190px;
$player-width: 430px;

h1 {
   font-family: 'Open Sans', sans-serif;
   font-size: 13pt;
   font-weight: 600;
   text-transform: uppercase;
   color: white;
   cursor: default;
}

h4 {
   font-family: 'Open Sans', sans-serif;
   font-size: 8pt;
   font-weight: 400;
   cursor: default;
}

h2 {
   font-family: 'Open Sans', sans-serif;
   font-size: 13pt;
   font-weight: 300;
   color: white;
   cursor: default;
}

@mixin unselectable() {
   -webkit-touch-callout: none;
   -webkit-user-select: none;
   -moz-user-select: none;
   -ms-user-select: none;
   user-select: none;
}

.player {
   height: $player-height;
   width: $player-width;
   background-color: #1E2125;
   position: absolute;
  
   top: 50%;
   left: 50%;
   transform: translate(-50%, -50%);
   -webkit-transform: translate(-50%, -50%);

   ul {
      list-style: none;
      li {
         display: inline-block;
      }
   }
}

.cover {
   position: absolute;
   top: 0;
   left: 0;
   img {
      height: $player-height;
      width: $player-height;
   }
}

.info {

   h1 {
      margin-top: 15px;
      margin-left: 180px;
      line-height: 0;
   }

   h4 {
      margin-left: 180px;
      line-height: 20px;
      color: #636367;
   }

   h2 {
      margin-left: 180px;
   }
}

.button-items {
   margin-left: 180px;
}

#slider {
	width: 182px;
	height: 4px;
	background: #151518;
   border-radius: 2px;
   div {
      width: 4px;
      height: 4px;
      margin-top: 1px;
      background: #EF6DBC;
      border-radius: 2px;
   }
}

#timer {
   color: #494B4E;
   line-height: 0;
   font-size: 9pt;
   float: right;
   font-family: Arial, Sans-Serif;
}

.controls {
   margin-top: 20px;

   svg:nth-child(2) {
      margin-left: 5px;
      margin-right: 5px;
   }
}

#play {
   padding: 0 3px;
   width: 30px;
   height: 30px;
   x: 0px;
   y: 0px;
   enable-background: new 0 0 25 25;

   g {
      stroke: #FEFEFE;
      stroke-width: 1;
      stroke-miterlimit: 10;

      path {
         fill: #FEFEFE;
      }
   }
}

#play:hover {
   cursor: pointer;
   g {
      stroke: #8F4DA9;
      cursor: pointer;

      path {
         fill: #9b59b6;
         cursor: pointer;
      }
   }
}

.step-backward {
   width: 18px;
   height: 18px;
   x: 0px;
   y: 0px;
   enable-background: new 0 0 25 25;
   margin-bottom: 5px;

   g polygon {
      fill: #FEFEFE;
   }
}

.step-foreward {
   width: 18px;
   height: 18px;
   x: 0px;
   y: 0px;
   enable-background: new 0 0 25 25;
   margin-bottom: 5px;

   g polygon {
      fill: #FEFEFE;
   }
}

#pause {
   x: 0px;
   y: 0px;
   enable-background: new 0 0 25 25;
   width: 30px;
   height: 30px;
   position: absolute;
   margin-left: -38px;
   cursor: pointer;

   rect {
      fill: white;
   }
}

#pause:hover rect {
   fill: #8F4DA9;
}

.step-backward g polygon:hover, .step-foreward g polygon:hover {
   fill: #EF6DBC;
   cursor: pointer;
}

.social {
   text-align: center;
}

.twitter {
  color: #BDBDBD;
  font-family: sans-serif;
  text-decoration: none;
  
  &:hover {
    color: #ecf0f1;
  }
}

.github {
  color: #BDBDBD;
  font-family: sans-serif;
  text-decoration: none;
  
  &:hover {
    color: #ecf0f1;
  }
}

p {
  color: #BDBDBD;
}

#skip {
  float: right;
  margin-top: 10px;
  
  p {
    color: #2980b9;
  }
  
  p:hover {
    color: #e74c3c;
    cursor: pointer;
  }
}

.expend {
   padding: 0.5px;
   cursor: pointer;
   
   svg:hover g polygon {
      fill: #EF6DBC;
   }
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
            @if(Auth::user())  
            <button data-target="#audioUpload" title="Add a track" style="padding:8px;" data-toggle="modal" class="btn btn-primary">
            Add Track
            </button>
            @endif
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
                            
                                
                                    
                            <audio  controls id="music">
                                <source :src="'/storage/audios/' + audio.file" type="audio/mpeg"> 
                            </audio>

                                
                            
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
        var music = document.getElementById("music");
        var playButton = document.getElementById("play");
        var pauseButton = document.getElementById("pause");
        var playhead = document.getElementById("elapsed");
        var timeline = document.getElementById("slider");
        var timer = document.getElementById("timer");
        var duration;
        pauseButton.style.visibility = "hidden";

        var timelineWidth = timeline.offsetWidth - playhead.offsetWidth;
        music.addEventListener("timeupdate", timeUpdate, false);

            function timeUpdate() {
                var playPercent = timelineWidth * (music.currentTime / duration);
                playhead.style.width = playPercent + "px";

                var secondsIn = Math.floor(((music.currentTime / duration) / 3.5) * 100);
                if (secondsIn <= 9) {
                    timer.innerHTML = "0:0" + secondsIn;
                } else {
                    timer.innerHTML = "0:" + secondsIn;
                }
            }

            playButton.onclick = function() {
                music.play();
                playButton.style.visibility = "hidden";
                pause.style.visibility = "visible";
            }

            pauseButton.onclick = function() {
                music.pause();
                playButton.style.visibility = "visible";
                pause.style.visibility = "hidden";
            }

            music.addEventListener("canplaythrough", function () {
                duration = music.duration;
            }, false);
    </script>


    <script>

        $(function(){
            $('a.has-spinner, button.has-spinner').click(function() {
                $(this).toggleClass('active');
            });
        });
    </script>
@endsection
