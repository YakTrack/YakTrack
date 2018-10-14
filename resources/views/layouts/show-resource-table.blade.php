<table class="table table-borderless table-hover show-resource-table">
    <tbody>
        @foreach($resource as $key => $value)
            <tr>
                <td><span class="h5"> {{ $key }} </span></td>
                <td><span class="h5"> {{ $value }} </span></td>
            </tr>
        @endforeach
    </tbody>
</table>