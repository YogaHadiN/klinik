 @if($ju['debit'] - $ju['kredit'] != 0)
	<tr>
		<td></td>
		<td>{{ $ju['coa'] }}</td>
		<td>{{ $ju['debit'] - $ju['kredit'] }}</td>
	</tr>
@endif