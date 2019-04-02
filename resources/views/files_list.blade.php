@extends('layouts.app')
@section('content')
    <ul class="list-group">
        @foreach($filesData as $file)
            <li class="list-group-item">
                <span style="color: red; font-size: 16px;">Title: </span>{{$file->title}}
                <span style="margin-left: 100px;"><span style="color: red; font-size: 16px;">Size: </span>{{$file->file_size}} k</span>
                <span style="margin-left: 100px;"><span style="color: red; font-size: 16px;"> MimeType: </span>{{$file->mime_type}}</span>
                <a class="btn btn-primary" style="margin-left: 100px;" href="{{$file->download_url}}">download</a>
            </li>
        @endforeach
    </ul>
@endsection