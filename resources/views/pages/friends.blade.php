@extends('index')

@section('content')
    {{--<div class="row bg-white">--}}
        {{--<img src="{{ $Owner['photo_max'] }}" class="img-circle col-sm-2">--}}
        {{--<div class="col-sm-5">--}}
            {{--<h2>{{ $Owner['first_name']}} {{ $Owner['last_name']}}</h2>--}}
            {{--<p>Всего записей:{{$wallCount}}</p>--}}
            {{--<p>Всего лайков:{{$likeCount}}</p>--}}
            {{--<p>Всего репостов:{{$repostsCount}}</p>--}}
        {{--</div>--}}
    {{--</div><br/>--}}
    @foreach ( $FriendList as $user)
        <div class="row bg-white" style="height:150px">
            <img src="{{ $user['photo_max']}}" class="img-circle col-sm-1">
            <div class="col-sm-5">
                <h3>{{ $user['first_name']}} {{ $user['last_name']}}</h3>
                <p>Всего постов:100</p>
                <p>Всего лайков:15</p>
                <p>Всего репостов:5</p>
            </div>
            <div class="col-sm-6">
                <img style="width: inherit" src="http://www.istashenko.com/wp-content/uploads/2013/06/grafikKirkorov.jpg">
            </div>
        </div><br/>
    @endforeach

@stop