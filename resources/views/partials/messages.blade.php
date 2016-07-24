<div id="notificaton">
@if (count($errors) > 0)
    <div class="alert alert-danger">
        <button tytpe="button" class="close" data-dismiss="alert" aria-hidden="true">
            &times;
        </button>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


@if (Session::has('messages'))

    <?php $messages = Session::get('messages') ?>
    @if(is_a($messages, "Illuminate\Support\MessageBag"))
        <?php $messages = $messages->getMessages() ?>
    @endif
    @foreach($messages as $type => $message)
        <div class="alert alert-{{ $type }} alert-dismissible">
            <ul class="flash-message-list">
            @if(is_array($message))
                @foreach($message as $item)
                    <li>{{ $item }}</li>
                @endforeach
            @else
                <li>{{ $message }}</li>
            @endif
            </ul>
            <div class="button-container">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            </div>
        </div>
    @endforeach
@endif

@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
        <div class="button-container">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        </div>
    </div>
@endif

</div>
