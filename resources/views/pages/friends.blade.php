@extends('index')

@section('content')
    <div class="row bg-white">
        <img src="{{ $Owner['photo_100'] }}" class="img-circle col-sm-2">
        <div class="col-sm-5">
            <h2>{{ $Owner['first_name']}} {{ $Owner['last_name']}}</h2>
            <p>Всего записей:{{$Owner['wallcount']}}</p>
            <p>Всего лайков:{{$Owner['likescount']}}</p>
        </div>
    </div><br/>
    @foreach ( $FriendList as $user)
        <div class="row bg-white" style="height:150px">
            <img src="{{ $user['photo_100']}}" class="img-circle col-sm-1">
            <div class="col-sm-5">
                <h3>{{ $user['first_name']}} {{ $user['last_name']}}</h3>
                <p>Всего постов:{{$user['wallcount']}}</p>
                <p>Всего лайков:{{$user['likescount']}}</p>
            </div>
        </div><br/>
    @endforeach

@stop