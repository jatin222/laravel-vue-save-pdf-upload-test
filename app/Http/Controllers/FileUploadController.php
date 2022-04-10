<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Models\FileUpload;
use App\Models\FileSeenHistory;

class FileUploadController extends Controller
{
    /**
     * upload file
     */
    public function fileUpload(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'file' => 'required|mimes:pdf|max:5000',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'data' => $validator->errors(),
                'message' => "validation_error"
            ]);
        }

        $user = Auth::user();

        $name = $request->file('file')->getClientOriginalName();
        $path = 'uploads/'.$user->id;
        $request->file->move(public_path($path), $name);
        $path .= '/'.$name;

        //check if name already exists
        $file_upload_exist = FileUpload::where('name', $name)->where('user_id', $user->id)->first();
        if($file_upload_exist)
        {
            return response()
                ->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'already_uploaded',
                ]);
        }

        $file_upload = new FileUpload;
        $file_upload->user_id = $user->id;
        $file_upload->name = $name;
        $file_upload->path = $path;
        $file_upload->save();

        return response()
                ->json([
                    'status' => true,
                    'data' => $file_upload,
                    'message' => 'success',
                ]);
    }

    /**
     * send all user files
     */
    public function getFiles()
    {
        $user = Auth::user();
        $files = FileUpload::where('user_id', $user->id)->with(['tags'])->get();

        return response()
                ->json([
                    'status' => true,
                    'data' => $files,
                    'message' => 'success',
                ]);
    }

    /**
     * add tags to file
     */
    public function addTags(Request $request)
    {
        $tags = $request->tags;
        $tags_arr = explode(',', $tags);
        if(count($tags_arr))
        {
            $file_upload = FileUpload::find($request->file_upload_id);
            $file_upload->syncTags($tags_arr);

            $file_upload = FileUpload::where('id', $request->file_upload_id)->with(['tags'])->first();
            

            return response()
                ->json([
                    'status' => true,
                    'data' => $file_upload,
                    'message' => 'success',
                ]);
        }
        else
        {
            return response()
                ->json([
                    'status' => true,
                    'data' => null,
                    'message' => 'no_tags_found',
                ]);
        }
    }

    /**
     * save select file history
     */
    public function selectFile(Request $request)
    {
        $user = Auth::user();
        $file_seen_history = new FileSeenHistory;
        $file_seen_history->user_id = $user->id;
        $file_seen_history->file_upload_id = $request->file_id;
        $file_seen_history->save();

        return response()
                ->json([
                    'status' => true,
                    'data' => $file_seen_history,
                    'message' => 'success',
                ]);
    }

    /**
     * get last selected file
     */
    public function getSelectedFile()
    {
        $user = Auth::user();
        $file_seen_history = FileSeenHistory::where('user_id', $user->id)->latest()->first();

        if($file_seen_history)
        {
            $file_upload = FileUpload::where('id', $file_seen_history->file_upload_id)->with(['tags'])->first();
            return response()
                ->json([
                    'status' => true,
                    'data' => $file_upload,
                    'message' => 'success',
                ]);
        }
        else
        {
            return response()
                ->json([
                    'status' => false,
                    'data' => null,
                    'message' => 'success',
                ]);
        }
    }
}
