@if(  $bayar <= 500000000 )
	<td nowrap>Potongan 25 % x {{ App\Classes\Yoga::buatrp(0) }}</td>
@elseif(  $bayar < 500000000 )
	<td nowrap>Potongan 25 % x {{ App\Classes\Yoga::buatrp( $bayar - 250000000) }}</td>
@else
	<td nowrap>Potongan 25 % x {{ App\Classes\Yoga::buatrp( 500000000) }}</td>
@endif
