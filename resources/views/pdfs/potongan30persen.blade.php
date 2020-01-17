@if(  $bayar > 500000000 )
<td nowrap>Potongan 30 % x {{ App\Classes\Yoga::buatrp( $bayar - 500000000 ) }}</td>
@else
<td nowrap>Potongan 30 % x {{ App\Classes\Yoga::buatrp(0) }}</td>
@endif
