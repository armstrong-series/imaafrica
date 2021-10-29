<?php

namespace App\Http\Controllers\MediaIntegration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AudioModel;
use App\Helpers\Paths;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AudioController extends Controller
{
    public function getAudioPlaylist(){
        try {
            $playlist = AudioModel::all();
            $countPlaylist = count($playlist);
            $data = [
                "page" => "trackPlaylist",
                "playlist" => $playlist, 
                "countPlaylist" => $countPlaylist
            ];
            return view('Media.audio', $data);
            
        } catch (Exception $error) {
           Log::info('MediaIntegration\VideoController@videos error message: ' . $error->getMessage());
           $message = "Unable to get Resource";
           return $message;
        }
    }


    public function audioPlaylistHandler(Request $request){
        try {
            if (!$request->file('audio_track')) {
                $message = "Add a track!";
                return response()->json(['message' => $message], 400);
            }

            if($request->hasFile("audio_track")){
                $track_path = storage_path('app/' . Paths::PLAYLIST_PATH);
                $extension = $request->file('audio_track')->getClientOriginalExtension();
                if (in_array(strtolower($extension), ["mp3", "mp4", "wav", "aac"])) {
                    $fileName = time() . '.' . $extension;
                    $request->file('audio_track')->move($track_path, $fileName);
                    $track = new AudioModel();
                    $track->user_id = Auth::id();
                    $track->file =  $fileName;
                    $track->save();
                    $url = route('audio.playlist.details', $track->uuid);
                    return response()->json([
                        "status" => "success",
                        "track" => $track,
                        "message" => "Track Added!",
                        "url" => $url,
                    ], 200);

                } else {
                    $message = "Invalid file format!";
                    return response()->json(['message' => $message], 400);
                }
            }
        } catch (Exception $error) {
            Log::info('MediaIntegration\AudioController@audioPlaylistHandler error message: ' . $error->getMessage());
            $message = 'Sorry, unable to create upload audio file. Please try again';
            return response()->json([
                'error' => true,
                "message" => $message,
            ], 500);
        }
    }
    

    public function editPlayList($playListUuid){
        try {
            $track = AudioModel::where('uuid', $playListUuid)->first();
            if (!$track) {
                $message = 'Track not found!';
                return response()->json(["message" => $message], 404);
            }
            $data = [
                "page" =>  "track", 
                "sub" =>  "track",
                "track" => $track
            ];

            return view('Media.audio.edit', $data);
       
        } catch (Exception $error) {
            Log::info('MediaIntegrations\AudioController@editPlayList error message: ' . $error->getMessage());
            $message = 'Unable to edit Track. Try Again';
            return $message;
        }
    }

    public function updateTrackDetails(Request $request)
    {
        try {
            if (!$request->title ) {
                $message = "Track title Required!";
                return response()->json(['message' => $message], 400);
            }

            $track = AudioModel::where('id', $request->id)->first();
            if (!$track ) {
                $message = "Track not found!";
                return response()->json(['message' => $message], 404);
            }

            $track->title = $request->title ?  $request->title : $track->title;
            $track->category = $request->category  ?  $request->category  : $track->category;
            $track->save();

            return response()->json([
                "status" => "success",
                "message" => "Track update successful!",
            ], 200);

        } catch (Exception $error) {
            Log::info('Admin\AdminController@updateTrackDetails error message: ' . $error->getMessage());
            $message = 'Sorry, unable to create template. Please try again';
            return response()->json([
                'error' => true,
                "message" => $message,
            ], 500);
        }
    }


    public function deleteTrack(Request $request)
    {
        try {
            $track = AudioModel::where('id', $request->id)->first();
            if (!$track) {
                $message = "Template not found!";
                return response()->json(['message' => $message], 404);
            }
            $track_path = Paths::PLAYLIST_PATH . $track->audio_track;
            if (Storage::has($track_path)) {
                Storage::delete($track_path);
            }

            $track->delete();
            $message = "Delete Comnpleted!";
            return response()->json(["message" => $message], 200);

        } catch (Exception $error) {
            Log::info('MediaIntegration\AudioController@deleteTrack error message: ' . $error->getMessage());
            $message = 'Sorry, unable to create template. Please try again';
            return response()->json([
                'error' => true,
                "message" => $message,
            ], 500);
        }
    }


    public function changeTrack(Request $request)
    {
        try {

            $track = AudioModel::where('id', $request->id)->first();
            if (!$track) {
                $message = "Track not found!";
                return response()->json(['message' => $message], 404);
            }

            if (!$request->hasFile('audio_track')) {
                $message = "An Mp3 file is required!";
                return response()->json(['message' => $message], 400);
            }

            $track_path = Paths::PLAYLIST_PATH. $track->audio_track;
            if (Storage::has($track_path)) {
                Storage::delete($track_path);
            }

            $audio_path = storage_path('app/' . Paths::PLAYLIST_PATH);
            $extension = $request->file('audio_track')->getClientOriginalExtension();
            if (!in_array(strtolower($extension), ["mp3"])) {
                $message = "Invalid file format!";
                return response()->json(['message' => $message], 400);
            }

            $fileName = time() . '.' . $extension;
            $request->file('audio_track')->move($audio_path, $fileName);
            $track->audio_track = $fileName;
            $track->save();
            return response()->json([
                "status" => "success",
                "message" => "Track Updated successfully",
            ], 200);

        } catch (Exception $error) {
            Log::info('MediaIntegration\AudioController@changeTrack error message: ' . $error->getMessage());
            $message = 'Sorry, unable to update track. Please try again';
            return response()->json([
                'error' => true,
                "message" => $message,
            ], 500);
        }
    }


    public function cloneTrack(Request $request) {
        try {

            if (!$request->title || !$request->id) {
                $message = "Track Details are Required!";
                return response()->json(['message' => $message], 400);
            }
            $track = AudioModel::where('id', $request->id)->first();
            if (!$track) {
                $message = "Audio Track not found!";
                return response()->json(["message" => $message], 404);
            }
            $newTrack = $track->replicate();
            $newTrack->save();

            return response()->json([
                'error' => false,
                'track' => $newTrack,
                'message' => "Clone Sucessful!",
            ], 200);
        } catch (Exception $error) {
            Log::info('Admin\TemplatesController@clone error message: ' . $error->getMessage());
            $message = 'Unable to update Resource. Encountered an error.';
            return response()->json([
                'error' => true,
                'status_code' => 404,
                "message" => $message,
            ], 500);
        }
    }


    public function downloadAudioTrack($file){

        $file_path = storage_path('app/' . Paths::PLAYLIST_PATH . $file);
        $header = ['Content-Type' => 'application/mp3, application/webm'];
    
        return response()->download($file_path, $file, $header);
    }

}
