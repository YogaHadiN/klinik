 @if($ju['debit'] - $ju['kredit'] != 0)
	<tr>
		<td></td>
		<td>{{ $ju['coa'] }}</td>
		<td class="text-right">{{  App\Classes\Yoga::buatrp( $ju['debit'] - $ju['kredit']) }}</td>
	</tr>
@endif
