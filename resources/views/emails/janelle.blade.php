
<h2 align="center">OAMPI {{$greet}} Celebrators:</h2>

<p>Hi Janelle,<br/><br/>Here are our {{$greet}} celebrators for this period:</p>

<table border="1">
	<thead>
		<tr>
			@if($type == '1')
			<th>Employee</th><th>Program</th><th>Birthday</th><th>Tenue</th>
			@else
			<th>Employee</th><th>Program</th><th>Tenure</th><th>Work Anniversary</th>
			@endif
		</tr>
	</thead>
	<tbody>
		{!! $msg !!}
	</tbody>
</table>
