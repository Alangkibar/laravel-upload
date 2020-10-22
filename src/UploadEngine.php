<?php

namespace Alangkibar\LaravelUploader;

use Illuminate\Support\Str;

class UploadEngine
{
    public function start($directory, $file){
        $targetDir = $directory;
        $extension = $file->extension();
        $name = strtotime(date('Y-m-d H:i:s')).Str::random(10).'.'.$extension;
        
        $file->move($targetDir, $name);

        return [
            'file' => [
                'url' => remove_double_slash(str_replace(config('uploader.asset_directory'), config('uploader.asset_url'), $directory.'/'.$name)),
                'name' => $name,
            ],
            'directory' => $directory,
            'full_directory' => remove_double_slash($directory.'/'.$name)
        ];
    }
}
