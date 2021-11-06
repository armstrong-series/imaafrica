@extends('app')

@section('title')
  {{ trans('users.upload').' - ' }}
  @endsection

@section('css')
<link href="{{ asset('plugins/iCheck/all.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/tagsinput/jquery.tagsinput.min.css') }}" rel="stylesheet" type="text/css" />
<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.1.1/min/dropzone.min.css" rel="stylesheet"/> -->
@endsection

@section('styles')
    <style>
    .fa-stop-circle{
        font-size:40px;
        color: rgb(227, 0, 51);
        cursor: pointer;
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
    <div class="drag-area">
         <section class="text-center">
          <div class="d-flex flex-column justify-content-center" style="height: 300px;">
            <header>
              <h3 class="mb-4 font-weight-light">File Upload</h3>
              <p>Upload your video here</p>
            </header>

            <div class="mx-auto mt-5 w-75 " >
              <div class="fallback dropzone dz-clickable" id="uploadHere" style="height: 300px; cursor: pointer; background: #F9F9F9; border: 2px dashed #066CF2;"> 
                <label class="dz-default dz-message needsclick p-5 d-block d-flex flex-column justify-content-center">
                <div class="mb-3 align-self-center"  >
                  <svg width="91" height="71" viewBox="0 0 91 71" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g opacity="0.802223">
                        <path d="M75.8195 23.3279C71.6137 6.8156 54.6222 -3.21007 37.8675 0.934916C24.7742 4.17423 15.267 15.327 14.2874 28.5961C4.99062 30.1071 -1.30316 38.7595 0.229987 47.9219C1.59288 56.0673 8.75445 62.0326 17.1309 61.9999H31.3481V56.3952H17.1309C10.8493 56.3952 5.7571 51.3766 5.7571 45.1859C5.7571 38.9952 10.8493 33.9766 17.1309 33.9766C18.7013 33.9766 19.9743 32.722 19.9743 31.1743C19.9601 17.2452 31.4062 5.94197 45.5397 5.92813C57.7743 5.91605 68.3062 14.4404 70.6729 26.2702C70.9066 27.4514 71.8792 28.3537 73.0898 28.5121C80.8634 29.6031 86.2675 36.698 85.1607 44.3591C84.1668 51.2384 78.2069 56.3605 71.1562 56.3952H59.7825V61.9999H71.1562C82.149 61.9671 91.0333 53.158 90.9999 42.3242C90.9722 33.306 84.7088 25.4681 75.8195 23.3279Z" fill="#67758D"/>
                        <path d="M44.0521 34.8276L33 46.2115L36.8959 50.2244L43.2508 43.7071V71H48.7768V43.7071L55.1041 50.2244L59 46.2115L47.9479 34.8276C46.8702 33.7241 45.1298 33.7241 44.0521 34.8276Z" fill="#67758D"/>
                        </g>
                    </svg>
                 </div>
                <p class="font-weight-light lead ">DRAG N DROP FILE TO UPLOAD</p>
                <span class="needsclick text-primary">Browse from your computer</span>
              </label>
             </div>   
            </div>            
          </div>
                           
          <div class="text-center" id="submit-video" style="display: none;margin-top: 80px;margin-bottom: 40px;">
              <button type="submit" class="btn btn-primary" name="videoDropzoneSubmit" id="videoDropzoneSubmit" v-if="isUpload">Save</button>
              <button class="btn btn-primary" type="button" v-if="isLoading" style="margin-top: 80px;">
                  <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                  Processing...
                </button>
          </div>
        </section>
        <!-- end of file upload -->
    </div> 




     <div class="video-controls">
        <div class="video-control h-100">                             
            <button class="btn btn-primary" type="button" v-if="isLoading" v-cloak>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Uploading...
            </button>
              
          <!-- <a href="javascript:void" class="video-control-item" title="Upload"
            data-toggle="tooltip" data-placement="top"
            :class="{active: view == 'upload'}"
            @click.prevent="setView('upload')" v-if="vidIcons">
            <svg width="38" height="33" viewBox="0 0 38 33" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M26.4753 20.5467C27.1809 18.6075 27.1718 18.1595 26.4663 17.4032L21.7844 12.1866C20.3716 10.672 18.0777 10.5731 16.6648 12.0877L11.9847 17.0502C11.2792 17.8085 10.1757 19.0108 11.9847 19.7691V19.7555C11.9847 20.5137 13.8354 20.506 14.5428 19.7497L15.867 18.3263C16.4369 17.7154 17.4119 18.144 17.4119 19.0089V31.0607C17.4119 32.1312 18.3671 33 19.3657 33H19.4399C20.4385 33 21.0301 32.1312 21.0301 31.0607V19.0089C21.0301 18.144 22.0033 17.7115 22.5732 18.3224L23.8992 20.1453C24.6048 20.9016 25.7861 20.5467 26.4934 20.5467H26.4753ZM26.6094 27.9564C24.0973 27.9564 24.1011 23.9647 26.6094 23.9647C28.6145 23.9647 30.0576 24.1064 31.7874 22.865C36.3749 19.5678 33.99 11.7958 28.3183 12.0094C25.7872 -1.17537 6.04935 3.28541 9.97034 16.8614C5.99428 13.5382 1.33086 19.9769 5.32021 23.0945C8.17219 25.3219 13.3179 22.2742 13.3179 25.9606C13.3179 28.4714 10.6064 27.9564 8.0032 27.9564C1.66885 27.9564 -2.23506 20.8511 1.39162 15.4183C3.12332 12.8237 5.84428 12.183 5.84428 12.183C7.09748 1.97811 18.1826 -3.41075 26.393 2.36531C29.9171 4.84618 31.252 8.4707 31.252 8.4707C41.0022 11.5843 40.7972 27.9564 26.6094 27.9564Z"/>
              </svg>
          </a> -->
        </div>
      </div>
</main>

@endsection


<textarea name="" id="uploadVideo"  style="display:none;" cols="30" rows="10">{{ route('users.videos.upload')}}</textarea>
@section('javascript') 
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.1.1/min/dropzone.min.js"></script> -->
    <script src="{{ asset('js/app/videos.js')}}"></script>
	<script src="{{ asset('plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
	<script src="{{ asset('plugins/tagsinput/jquery.tagsinput.min.js') }}" type="text/javascript"></script>
@endsection
