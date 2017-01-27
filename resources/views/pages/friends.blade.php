@extends('index')

@section('content')

    <script type="text/javascript">

        $(function () {
            var chart = new CanvasJS.Chart("curve_chart", {
                theme: "theme2",

                axisX: {
                    labelFormatter: function (e) {
                        return CanvasJS.formatDate(e.value, "D MMM YY");
                    }
                },
                data: [
                    {
                        type: "spline",
                        dataPoints: [
                            @foreach ( $Statistics as $date => $qty)
                            { x: new Date("{{$date}}"), y: {{$qty}} },
                            @endforeach
                        ]
                    }
                ]
            });
            chart.render();
        });
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
        @if(count($Friend[0]) > 1)
        <script type="text/javascript">

            $(function () {
                var chart{{$Friend[1]['user_id']}} = new CanvasJS.Chart("curve_chart{{$Friend[1]['user_id']}}", {
                    theme: "theme2",

                    axisX: {
                        valueFormatString: "D MMM" ,
                        labelAngle: -50
                    },
                    data: [
                        {
                            type: "spline",
                            dataPoints: [
                                @foreach ( $Friend[0] as $date => $qty)
                                { x: new Date("{{$date}}"), y: {{$qty}}},
                                @endforeach
                            ]
                        }
                    ]
                });
                chart{{$Friend[1]['user_id']}}.render();
            });
        </script>
        @endif

        <div class="row bg-white" style="height:auto">
            <img src="{{ $Friend[1]['photo']}}" class="img-circle col-sm-1">
            <div class="col-sm-5">
                <h2><a href="/id{{$Friend[1]['user_id']}}"> {{ $Friend[1]['first_name']}} {{ $Friend[1]['last_name']}}</a>
                <a style="color: silver" href="http://vk.com/id{{$Friend[1]['user_id']}}" >VK</a></h2>
            </div>
            @if(count($Friend[0])>1)
                <div id="curve_chart{{ $Friend[1]['user_id'] }}" style="width: 100%; height: 300px" class="col-md-5"></div>
            @else
                <h2 style="color: orangered">У данного пользователя отсутсвуют записи на стене!</h2>
            @endif
        </div><br/>
    @endforeach

@stop