<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FilesController extends Controller
{

    protected $googleDrive;

    public function __construct(\Google_Client $client)
    {
        $this->middleware(function ($request, $next) use ($client) {
            $client->setAccessToken(Cache::get('user')->token);
            $this->googleDrive = new \Google_Service_Drive($client);
            return $next($request);
        });
    }

    public function showFiles()
    {
        $optParams = array(
            'pageSize' => 10,
            'fields' => 'nextPageToken, files(*)',
        );

        $results = $this->googleDrive->files->listFiles($optParams);
        try {
            $this->saveFiles($results);
        } catch (\Exception $exception) {
            echo $exception;
        }
        $filesData = File::get(['title', 'download_url', 'file_size', 'mime_type']);
        return view('files_list', ['filesData' => $filesData]);
    }

    public function saveFiles($files)
    {
        foreach ($files->getFiles() as $file) {
            File::updateOrCreate([
                'title' => $file->getName(),
                'download_url' => 'https://drive.google.com/file/d/'.$file->getId().'/view',
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);
        }
    }
}
