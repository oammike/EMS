
<h2><img src="{{asset('public/img/eval-login-logo.png')}} " width="290" /> <br/>
	<br/>Health Declaration Alert</h2>


<p>New Health Form Declaration Alert from: <strong> @if(is_null($employee->nickname)) {{$employee->firstname}} @else {{$employee->nickname}} @endif {{$employee->lastname}} [ {{$program->name}} ]</strong> <br/><br/>

	
   ------------------------------------------------------------<br/><br/>

	In line with our Health and Safety Protocols, please be informed that {{$employee->firstname}} {{$employee->lastname}} is required to stay at home.  <br/><br/>
	Our Nurses will advise you if{{$employee->firstname}} is cleared to return to work in the office. <br/><br/>
	
	
   ------------------------------------------------------------<br/><br/><br/>

Please log in to  Employee Management System (EMS) for details. <br/>
<em>Clinical Services &raquo; View all Responses</em></p>
