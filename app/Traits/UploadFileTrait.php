<?php

namespace App\Traits;

trait UploadFileTrait
{
    /**
     * Store Uploaded File.
     *
     * @param $request
     * @param $fileName
     */
    public function storeUploadedFile($request, $fileName)
    {
        if ($request->hasFile($fileName)) {
            return $request->file($fileName)->store('public');
        }
    }
}
