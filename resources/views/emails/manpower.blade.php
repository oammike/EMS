
<h2><img src="{{asset('public/img/eval-login-logo.png')}} " width="290" /> <br/>
	<br/>Manpower Request</h2>


<p>New Manpower request from: <strong> @if(is_null($employee->nickname)) {{$employee->firstname}} @else {{$employee->nickname}} @endif {{$employee->lastname}} </strong> <br/><br/>
   ------------------------------------------------------------<br/>

	Program: {{$request[0]->program}} <br/>
	Position: <strong>{{$request[0]->jobTitle}}</strong>  --  [ {{$request[0]->lob}} ] <br/>
	Type: {{$request[0]->type}} <br/>
	Total Needed: <strong style="font-size: large;"> {{$request[0]->howMany}}</strong> <br/>

	<?php $s=collect($allStatus)->where('id',$request[0]->status); $f = collect($foreignStatus)->where('id',$request[0]->foreignStatus); ?> 
                          

	Status:  @if(count($s) > 0) {{$s->first()['name']}} @endif | @if(count($f) > 0) {{$f->first()->name}} [foreign] @endif   <br/><br/>

	Reason:  {{$request[0]->reason}} <br/>
	Notes/Detais: <br/>
	<div style="padding:10px; border: 1px dotted #333;"><em> {!! $request[0]->notes !!}</em></div><br/>
	Hiring source: {{$request[0]->source}}<br/>
	Boost FB job ad: @if($request[0]->mktgBoost)<strong>YES</strong> @else NO @endif <br/>
	Start Date: <strong><?php echo date("M d, Y", strtotime($request[0]->trainingStart)); ?></strong> <br/>
	 ------------------------------------------------------------<br/><br/><br/>

Please log in to  OAMPI Employee Management System for details.</p>
<p><a href="{{url('/')}}/manpower">{{action('ManpowerController@index')}}</a></p>