@extends('index')

@section('content')
{{--<form method="post" id="id-form_messages" >--}}

    {{--<div class="form-group">--}}
        {{--<label for="login"></label>--}}
            {{--<input class="form-control" placeholder="Email или номер телефона" name="login" type="text" id="login" >--}}
    {{--</div>--}}

    {{--<div class="form-group">--}}
        {{--<label for="password"></label>--}}
        {{--<input class="form-control" placeholder="Пароль" name="password" type="password" id="password" >--}}
    {{--</div>--}}

    {{--<div class="form-group">--}}
        {{--<input class="btn btn-primary" type="submit" value="Войти">--}}
    {{--</div>--}}

{{--</form>--}}

<a href="{{$authorize_url}}">Sign in with VK</a>
@stop