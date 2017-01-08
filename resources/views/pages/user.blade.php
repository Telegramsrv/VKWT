@extends('index')

@section('content')
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Date', 'Like'],
                    @foreach ( $postsStatistics as $date => $qty)
                [ '{{ $date}}', {{ $qty}}],
                @endforeach
            ]);

            var options = {
                title: 'Like statistics',
                curveType: 'function',
                legend: { position: 'bottom' }
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

            chart.draw(data, options);
        }
    </script>
<div class="row bg-white">
    <img src="{{ $Owner['photo_100'] }}" class="img-circle col-sm-2">
    <div class="col-sm-5">
        <h2>{{ $Owner['first_name']}} {{ $Owner['last_name']}}</h2>
        <p>Всего записей:{{$Owner['wallcount']}}</p>
        <p>Всего лайков:{{$Owner['likescount']}}</p>
    </div>
        <div id="curve_chart" style="width: 100%; height: 500px" class="col-md-5"></div>
</div><br/>


    <div class="row bg-white">
        <h4 class="text-center">Самый популярный пост</h4>
        <img src="{{ $Owner['photo_100'] }}" class="img-circle col-sm-1">
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
        <p class="text-primary container">Likes : {{$topWall['likes']['count']}} Reposts : {{ $topWall['reposts']['count'] }}</p>
    </div><br/>

<div class="row bg-white">
    <h4 class="text-center">Первый пост на странице</h4>
    <img src="{{ $Owner['photo_100'] }}" class="img-circle col-sm-1">
    <div>
        <p>{{ $Owner['first_name'] }} {{ $Owner['last_name'] }}</p>
        <a href="https://vk.com/wall{{$firstWall['to_id']}}_{{$firstWall['id']}}">
        <small>{{ date('d-m-Y',$firstWall['date']) }}</small>
        </a>
        <p>{{ $firstWall['text'] }}</p>
        @if (isset($firstWall['attachment']['photo']['src_big'] ))
            <img class="center-block" src="{{ $firstWall['attachment']['photo']['src_big'] }}">
        @endif
    </div><br/>
    <p class="text-primary container">Likes : {{$firstWall['likes']['count']}} Reposts : {{ $firstWall['reposts']['count']}}</p>
</div><br/>

@stop