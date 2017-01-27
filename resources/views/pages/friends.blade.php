@extends('index')

@section('content')

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            var options = {
                title: 'Like statistics',
                curveType: 'function',
                legend: { position: 'bottom' }
            };


            var data = google.visualization.arrayToDataTable([
                ['Date', 'Like'],
                    @foreach ( $Statistics as $date => $qty)
                [ '{{ $date}}', {{ $qty}}],
                @endforeach
            ]);
            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
            chart.draw(data, options);

            //for Friends

            @foreach($FriendStats as $Friend)
                @if($Friend[0])

                     var data{{$Friend[1]['user_id']}} = google.visualization.arrayToDataTable([
                        ['Date', 'Like'],
                        @foreach ( $Friend[0] as $date => $qty)
                              [ '{{ $date}}', {{ $qty}}],
                        @endforeach
                     ]);
                     var chart{{$Friend[1]['user_id']}} = new google.visualization.LineChart(document.getElementById("curve_chart{{$Friend[1]['user_id']}}"));
                     chart{{$Friend[1]['user_id']}}.draw(data{{$Friend[1]['user_id']}}, options);
                @endif
            @endforeach
        }
    </script>

    <div class="row bg-white">
        <img src="{{ $Owner->photo }}" class="img-circle col-sm-2">
        <div class="col-sm-5">
            <h2>{{ $Owner->first_name}} {{ $Owner->last_name}}</h2>
            <p>Всего записей:{{$Owner->qtyWallPosts()}}</p>
            <p>Всего лайков:{{$Owner->qtyLikes()}}</p>
        </div>
        <div id="curve_chart" style="width: 100%; height: 500px" class="col-md-5"></div>
    </div><br/>

    @foreach ( $FriendStats as $Friend)
        <div class="row bg-white" style="height:auto">
            <img src="{{ $Friend[1]['photo']}}" class="img-circle col-sm-1">
            <div class="col-sm-4">
                <h2><a href="/id{{$Friend[1]['user_id']}}"> {{ $Friend[1]['first_name']}} {{ $Friend[1]['last_name']}}</a>
                    <a style="color: silver" href="http://vk.com/id{{$Friend[1]['user_id']}}" >VK</a></h2>
            </div>
            @if($Friend[0])
                <div id="curve_chart{{ $Friend[1]['user_id'] }}" style="width: 100%; height: 300px" class="col-md-5"></div>
            @else
                <h2 class="col-sm-5" style="color: orangered">У данного пользователя отсутсвуют записи на стене!</h2>
            @endif
        </div><br/>
    @endforeach

@stop