@if($session->isLinkedTo($app))
    <button class="btn btn-light"><i class="fas fa-check text-success"></i></button>
@else
    <form action="{{ route('third-party-application-session.store') }}" method="post">
        {{ csrf_field() }}
        <input type="hidden" name="session_id" value="{{ $session->id }}">
        <input type="hidden" name="third_party_application_id" value="{{ $app->id }}">
        <button class="btn btn-light"> Link </button>
    </form>
@endif