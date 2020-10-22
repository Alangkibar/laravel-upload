<?php

namespace Alangkibar\LaravelUploader;

use File;
use Validator;
use Illuminate\Http\Request;
use Alangkibar\LaravelUploader\UploadEngine;

class Uploader{
    public $directory;
    public $response;
    public $type;

    function __construct(){
        $this->directory = 'files';
        $this->type = 'picture';
        $this->response = 'default';

        $this->upload = new UploadEngine;
    }

    public function directory($dir){
        $this->directory = $dir;
        return $this;
    }

    public function response($res){
        $this->response = $res;
        return $this;
    }

    public function type($type){
        $this->type = $type;
        return $this;
    }

    public function upload($file){
        $validation = Validator::make([
            'file' => $file
        ], [
            'file' => 'required|file|image|mimes:jpeg,png,jpg|max:5000'
        ]);
        
        if ($validation->fails()) {
            return response()->json($validation->errors()->first(), 422);
        }else{
            $target_directory = config("uploader.asset_directory").'/'.$this->directory;

            if (!File::isDirectory($target_directory)) {
                File::makeDirectory($target_directory, 0777, true);
            }

            $uploaded = $this->upload->start($target_directory, $file);

            return $this->build_response($uploaded);
        }
    }

    public function build_response($uploaded){
        switch ($this->response) {
            case 'krajee-input-file':
                $data = [
                    'initialPreview' => [
                        $uploaded['file']['url'],
                    ],
                    'initialPreviewConfig' => [
                        [
                            'caption' => $uploaded['file']['name'],
                            'width' => "120px",
                            "url" => null,
                            "key" => uniqid(),
                            "extra" => [
                                "_token" => csrf_token(),
                                "_method" => "DELETE",
                                "is_new" => true,
                                "type" => $this->type
                            ]
                        ]
                    ],
                    'append' => true // whether to append content to the initial preview (or set false to overwrite)
                ];
                break;
            }

        return response()->json($data, 200);
    }
}