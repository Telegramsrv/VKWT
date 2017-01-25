@extends('index')

@section('content')
    <script>
        function updateProcess() {
            $.ajax({
                type: "GET",
                url: "uploadprocess",
                data: null,
                success: function (msg) {
                    $("span").html(msg);
                },
                complete: function (msg) {
                    setTimeout(updateProcess, 1000);
                }
            });
        }

        $( document ).ready(function() {
            updateProcess();
        });
    </script>
    <div>
        @if ($proccess)
        <h2 style="position: absolute;top: 50%; left: 50%;">Загрузка <span id="process">-1</span>%</h2>
        @else
        <h2 style="position: absolute;top: 50%; left: 40%;">Ожидайте,в ближайшое время информация по Вашей учётной записи будет внесена!</h2>
        @endif
    </div>
@stop