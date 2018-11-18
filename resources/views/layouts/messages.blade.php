<div id="notificaton">

    @foreach ($errors->all() as $error)
        @include('layouts.alert', ['class' => 'danger', 'message' => $error])
    @endforeach

    @foreach(Session::get('messages') ?? [] as $class => $message)
        @include('layouts.alert', ['class' => $class, 'message' => $message])
    @endforeach

    <alert v-for="(alert, index) in alerts" :key="index" :alert="alert">
    </alert>

</div>