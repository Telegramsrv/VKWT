@extends('index')

@section('content')
<div class="row bg-white">
    <img src="{{ $Owner['photo_max'] }}" class="img-circle col-sm-2">
    <div class="col-sm-5">
        <h2>{{ $Owner['first_name']}} {{ $Owner['last_name']}}</h2>
        <p>Всего записей:{{$wallCount}}</p>
        <p>Всего лайков:{{$likeCount}}</p>
        <p>Всего репостов:{{$repostsCount}}</p>
    </div>
    <div class="col-sm-7">
        <img style="width: 100%" src="http://www.istashenko.com/wp-content/uploads/2013/06/grafikKirkorov.jpg">
    </div>
</div><br/>


    <div class="row bg-white">
        <h4 class="text-center">Самый популярный пост</h4>
        <img src="{{ $Owner['photo_max'] }}" class="img-circle col-sm-1">
        <div>
            <p>{{ $Owner['first_name'] }} {{ $Owner['last_name'] }}</p>
            <a href="https://vk.com/wall{{$topWall['to_id']}}_{{$topWall['id']}}">
            <small>{{ date('d-m-Y',$topWall['date']) }}</small>
            </a>
            <p>{{ $topWall['text'] }}</p>
            @if (isset($topWall['attachment']['photo']['src_big'] ))
                <img class="center-block" src="{{ $topWall['attachment']['photo']['src_big'] }}">
            @endif
        </div>
        <p class="text-primary">Likes : {{$topWall['likes']['count']}} Reposts : {{ $topWall['reposts']['count'] }}</p>
    </div><br/>

<div class="row bg-white">
    <h4 class="text-center">Первый пост на странице</h4>
    <img src="{{ $Owner['photo_max'] }}" class="img-circle col-sm-1">
    <div>
        <p>{{ $Owner['first_name'] }} {{ $Owner['last_name'] }}</p>
        <a href="https://vk.com/wall{{$firstWall['to_id']}}_{{$firstWall['id']}}">
        <small>{{ date('d-m-Y',$firstWall['date']) }}</small>
        </a>
        <p>{{ $firstWall['text'] }}</p>
        @if (isset($firstWall['attachment']['photo']['src_big'] ))
        <img class="center-block" src="{{ $firstWall['attachment']['photo']['src_big'] }}">
        @endif
    </div>
    <p class="text-primary">Likes : {{$firstWall['likes']['count']}} Reposts : {{ $firstWall['reposts']['count']}}</p>

</div><br/>

@stop