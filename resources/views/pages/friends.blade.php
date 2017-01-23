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

                var data{{$Friend['user_id']}} = google.visualization.arrayToDataTable([
                    ['Date', 'Like'],
                    @foreach ( $Friend as $date => $qty)
                        @if( strtotime($date))
                          [ '{{ $date}}', {{ $qty}}],
                        @endif
                    @endforeach
                ]);
                var chart{{$Friend['user_id']}} = new google.visualization.LineChart(document.getElementById("curve_chart{{$Friend['user_id']}}"));
                chart{{$Friend['user_id']}}.draw(data{{$Friend['user_id']}}, options);

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
        <div class="row bg-white" style="height:400px">
            <img src="{{ $Friend['photo']}}" class="img-circle col-sm-1">
            <div class="col-sm-5">
                <h2><a href="/id{{$Friend['user_id']}}"> {{ $Friend['first_name']}} {{ $Friend['last_name']}}</a></h2>
                <p><a href="http://vk.com/id{{$Friend['user_id']}}" >VK</a></p>
            </div>
            <div id="curve_chart{{ $Friend['user_id'] }}" style="width: 100%; height: 300px" class="col-md-5"></div>
        </div><br/>
    @endforeach

@stop