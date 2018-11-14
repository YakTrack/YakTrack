
@if($sessions->count())
<div class="p-4 bg-white rounded shadow">
    <table class="table table-hover bg-white">
        <thead>
            <tr>
                <th class="pl-0"> <input type="checkbox"/> </th>
                <th> Start Time </th>
                <th> End Time </th>
                <th> Total Time </th>
                <th> Linked To </th>
                @foreach($thirdPartyApplications as $app)
                    <th> Linked to {{ $app->name }} </th>
                @endforeach
                @if($showInvoiceColumn ?? true)<th> Invoice </th>@endif
                <th class="text-right pr-0">
                    <dropdown :options="[
                        {
                            name: 'Link to invoice',
                            event: 'sessions.link-to-invoice',
                        }
                    ]"></dropdown>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $key => $session)
                <tr
                >
                    <td class=""> <input type="checkbox"/></td>
                    <td class="min-w-1">
                        {{ $session->localStartedAtTimeForHumans }}
                    </td>
                    <td class="min-w-1">
                        @if ($session->isRunning())
                            <a class="btn" href="{{ route('session.stop', ['session' => $session]) }}"><i class="fa fa-stop text-red"></i></a>
                        @else
                            {{ $session->localEndedAtTimeForHumans }}
                        @endif
                    </td>
                    <td class="min-w-1">
                        {{ $session->durationForHumans }}
                    </td>
                    <td class="max-w-3">
                        @include('partials.session.session-task', ['session' => $session])
                    </td>
                    @foreach($thirdPartyApplications as $app)
                        <td>
                            
                            @include('partials.session.third-party-application')
                        </td>
                    @endforeach
                    @if($showInvoiceColumn ?? true)<td>
                        @if($session->invoice)
                            <a class="no-underline text-xs" href="{{ route('invoice.show', ['invoice' => $session->invoice] )}}"> {{ $session->invoice->number }} </a>
                        @endif
                    </td>
                    @endif
                    <td class="text-right inline-flex pb-2 @if($key == 0) pt-2 @endif float-right">
                        <form action="{{ route('session.destroy', $session->id) }}" method="post">
                            <div class="btn-group pull-right">
                                <a
                                    href="{{ route('session.edit', ['session' => $session]) }}"
                                    class="btn btn-default"
                                >
                                    <i class="fa fa-edit"></i>
                                </a>
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn delete-item-button">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
    <div>
        You have not created any sessions yet.
    </div>
@endif
