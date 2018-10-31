<div class="rounded p-2 bg-white shadow mb-8">
    <table class="table rounded p-4 bg-white">
        <tbody>
            @foreach($resource as $key => $value)
                <tr>
                    <td><span class="text-grey-dark p-2"> {{ $key }} </span></td>
                    <td><span class="text-lg leading-loose p-2"> {{ $value }} </span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>