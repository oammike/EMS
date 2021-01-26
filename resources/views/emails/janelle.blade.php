
<h2 align="center" style="font-family: Arial,sans-serif; color:#333;" >OAMPI {{$greet}} Celebrators:</h2>

<p style="font-family: Arial,sans-serif; color:#333;">Hi Janelle,<br/><br/>Here are our {{$greet}} celebrators for this period:</p>

<table border="1" style="font-family: Arial,sans-serif; color:#333;">
	<thead>
		<tr>
			@if($type == '1')
			<th>Employee</th><th>Program</th><th>Birthday</th><th>Tenure</th>
			@else
			<th>Employee</th><th>Program</th><th>Tenure</th><th>Work Anniversary</th>
			@endif
		</tr>
	</thead>
	<tbody>
		{!! $msg !!}
	</tbody>
</table>
