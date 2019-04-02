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
        //use Google Provider as middleware for the controller to auth the step of getting files
        $this->middleware(function ($request, $next) use ($client) {
            $client->setAccessToken(Cache::get('user')->token);
            $this->googleDrive = new \Google_Service_Drive($client);
            return $next($request);
        });
    }

    //function for show files of the google drive
    public function showFiles()
    {
        $optParams = array(
            'pageSize' => 10,
            'fields' => 'nextPageToken, files(*)',
        );
        //list files
        $results = $this->googleDrive->files->listFiles($optParams);
        try {
            //save files
            $this->saveFiles($results);
        } catch (\Exception $exception) {
            echo $exception;
        }
        //get files we saved from DB
        $filesData = File::get(['title', 'download_url', 'file_size', 'mime_type']);
        //return view
        return view('files_list', ['filesData' => $filesData]);
    }

    public function saveFiles($files)
    {
        //loop files we get and save it in local db
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
