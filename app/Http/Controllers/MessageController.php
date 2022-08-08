<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Http\Resources\MessageResource;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        //fetch all the messages in paginated form

        return MessageResource::collection(Message::paginate(8));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMessageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMessageRequest $request)
    {
    
        $user = Auth::user();
        
        if($user->is_admin) {
             
            $data = $request->validated();

            $data['user_id'] = $user->id;

        // Check if image was given and save on local file system
        if (isset($data['cover_img'])) {
            $relativePath  = $this->saveImage($data['cover_img']);
            $data['cover_img'] = $relativePath;
        }

       
            $relativePath  = $this->saveAudio($data['audio']);
            $data['audio'] = $relativePath;
        

            $message = Message::create($data);

       

        return new MessageResource($message);

        } else {
            return abort(403, "Unauthorized action");
        }
        
    }

    private function saveImage($image)
    {
        // Check if image is valid base64 string
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            // Take out the base64 encoded text without mime type
            $image = substr($image, strpos($image, ',') + 1);
            // Get file extension
            $type = strtolower($type[1]); // jpg, png, gif

            // Check if file is an image
            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('invalid image type');
            }
            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false) {
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception('did not match data URI with image data');
        }

        $dir = 'uploads/images/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($relativePath, $image);

        return $relativePath;
    }

    private function saveAudio($audio)
    {
        
        if (preg_match('/^data:audio\/(\w+);base64,/', $audio, $type)) {
            // Take out the base64 encoded text without mime type
            $audio = substr($audio, strpos($audio, ',') + 1);
            // Get file extension
            $type = strtolower($type[1]); // mpeg. mp3, wav

            // Check if file is an audio
            if (!in_array($type, ['mpeg', 'mp3', 'wav', 'ogg'])) {
                throw new \Exception('invalid audio type');
            }
            $audio = str_replace(' ', '+', $audio);
            $audio = base64_decode($audio);

            if ($audio === false) {
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception('did not match data URI with audio data');
        }

        $dir = 'uploads/audios/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($relativePath, $audio);

        return $relativePath;
    }

   
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        return new MessageResource($message);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMessageRequest  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMessageRequest $request, Message $message)
    {
        $user = Auth::user();

        if($user->is_admin) {
            $data = $request->validated();

            // Check if image was given and save on local file system
            if (isset($data['cover_img'])) {
                $relativePath = $this->saveImage($data['cover_img']);
                $data['cover_img'] = $relativePath;
    
                // If there is an old image, delete it
                if ($message->cover_img) {
                    $absolutePath = public_path($message->cover_img);
                    File::delete($absolutePath);
                }
            }

            // Check if audio was given and save on local file system
            if (isset($data['audio'])) {
                $relativePath = $this->saveAudio($data['audio']);
                $data['audio'] = $relativePath;
    
                // If there is an old audio, delete it
                if ($message->audio) {
                    $absolutePath = public_path($message->audio);
                    File::delete($absolutePath);
                }
            }
    
    
            // Update survey in the database
            $message->update($data);
    
            return new MessageResource($message);
        }
        else {
            abort("403","Unauthorized");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message, Request $request)
    {
        $user = Auth::user();
        //$message->delete();

        // $user = $request->user();
        
        if($user->is_admin) {

            $message->delete();

            return response("",204);
        }
        else {
            abort("403","Unauthorized");
        }
    }
}
