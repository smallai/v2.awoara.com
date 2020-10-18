@if(isset($item))
<table>
    <filedset>{{ $item->name }}</filedset>
    <tr>
        <th>{{ $item->date }}</th>
    </tr>
    @foreach($records as $record)
    <tr>
       <td>{{ $record->payment_at->format('m-d') }}</td>
        <td></td>
    </tr>
    @endforeach
</table>
@else
@endif