@extends('index')

@section('content')
    {{--<script type="text/javascript">--}}
        {{--google.charts.load('current', {'packages':['corechart']});--}}
        {{--google.charts.setOnLoadCallback(drawChart);--}}

        {{--function drawChart() {--}}
            {{--var data = google.visualization.arrayToDataTable([--}}
                {{--['Date', 'Like'],--}}
                    {{--@foreach ( $postsStatistics as $date => $qty)--}}
                {{--[ '{{ $date}}', {{ $qty}}],--}}
                {{--@endforeach--}}
            {{--]);--}}

            {{--var options = {--}}
                {{--title: 'Like statistics',--}}
                {{--curveType: 'function',--}}
                {{--legend: { position: 'bottom' }--}}
            {{--};--}}

            {{--var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));--}}

            {{--chart.draw(data, options);--}}
        {{--}--}}
    {{--</script>--}}

<div class="row bg-white">
    <img src="{{ $Owner->photo }}" class="img-circle col-sm-2">
    <div class="col-sm-5">
        <h2>{{ $Owner->first_name}} {{ $Owner->last_name}}</h2>
        <p>Всего записей:{{$Owner->qtyWallPosts()}}</p>
        <p>Всего лайков:{{$Owner->qtyLikes()}}</p>
    </div>
        {{--<div id="curve_chart" style="width: 100%; height: 500px" class="col-md-5"></div>--}}
</div><br/>




    {{--<div class="_post_content">--}}

        {{--<div class="post_header">--}}
            {{--<a class="post_image _online" href="http://vk.com/id{{ $Owner->user_id}}" aria-label="{{ $Owner->first_name}} {{ $Owner->last_name}}">--}}
                {{--<img src="{{ $Owner->photo}}" data-post-id="{{ $Owner->user_id}}_{{ $Owner->getTopRatedPost()->wall_id}}" width="50" height="50" class="post_img" data-alt="{{ $Owner->first_name}} {{ $Owner->last_name}}">--}}
                {{--<span class="blind_label">.</span>--}}
            {{--</a>--}}
        {{--<div class="post_header_info">--}}
                {{--<h5 class="post_author"><a class="author" href="http://vk.com/id{{ $Owner->user_id}}" data-from-id="{{ $Owner->user_id}}" data-post-id="{{ $Owner->user_id}}_{{ $Owner->getTopRatedPost()->wall_id}}">{{ $Owner->first_name}} {{ $Owner->last_name}}</a></h5>--}}
                {{--<div class="post_date"><a class="post_link" href="http://vk.com/wall{{ $Owner->user_id}}_{{ $Owner->getTopRatedPost()->wall_id}}">--}}
                        {{--<span class="rel_date">{{ date('H:m d-m-Y',$Owner->getTopRatedPost()->date)}}</span></a></div>--}}
                {{--<div class="wall_text"><div id="wpt44424561_1840" class="_wall_post_cont"><div class="wall_post_text">Great evening.fixie ride kiyv--}}
                            {{--<a href="/feed?section=search&amp;q=%23fixedgear">#fixedgear</a>--}}
                            {{--<a href="/feed?section=search&amp;q=%23fixie">#fixie</a>--}}
                            {{--<a href="/feed?section=search&amp;q=%23winter">#winter</a>--}}
                            {{--<a href="/feed?section=search&amp;q=%23kiyv">#kiyv</a></div>--}}
                        {{--<div class="page_post_sized_thumbs  clear_fix" style="width: 510px; height: 382px;">--}}
                            {{--<a href="/photo44424561_456239023" aria-label="фотографія Great evening.fixie ride kiyv #fixedgear #fixie #winter #kiyv" onclick="return showPhoto('44424561_456239023', 'wall44424561_1840', {&quot;temp&quot;:{&quot;base&quot;:&quot;https://pp.vk.me/c837239/v837239561/&quot;,&quot;x_&quot;:[&quot;cf76/lY3CYpAZHrs&quot;,604,453],&quot;y_&quot;:[&quot;cf77/E9MqH09dTQg&quot;,640,480]},queue:1}, event)" style="width: 510px; height: 383px; background-image: url(https://pp.vk.me/c837239/v837239561/cf76/lY3CYpAZHrs.jpg)" class="page_post_thumb_wrap image_cover  page_post_thumb_last_column page_post_thumb_last_row"></a></div></div></div>--}}
            {{--</div>--}}
        {{--</div>--}}

    {{--</div>--}}



@stop