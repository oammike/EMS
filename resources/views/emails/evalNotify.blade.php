
<h2>{{$evalSetting}} Rejected </h2>


<p>Your {{$evalSetting}} for  <strong>{{$owner}} </strong> has been rejected by HR team. <br/><br/>
   ------------------------------------------------------------<br/>

	Notes/Detais: <br/>
	<div style="padding:10px; border: 1px dotted #333;">
		<pre> {!! $notes !!}</em></div>
			<br/>
	 <br/>
	 ------------------------------------------------------------<br/><br/><br/>

Please log in to  OAMPI Employee Management System (EMS) for details.</p>
<p><a href="{{url('/')}}/evalForm/{{$theEval->id}}">{{action('EvalFormController@show',$theEval->id)}}</a></p>