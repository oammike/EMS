
<h2><img src="{{asset('public/img/eval-login-logo.png')}} " width="290" /> <br/>
	<br/> New Regularization Evaluation</h2>


<p>Kindly check the following Regularization evaluation:<br/><br/>
   ------------------------------------------------------------<br/>

	Employee: <strong> {{$employee->lastname}}, {{$employee->firstname}} </strong><br/>
	Evaluated by: <strong> {{$tl->firstname}} {{$tl->lastname}}</strong><br/>
	Eval Period:  <strong><?php echo date("M d, Y", strtotime($evalForm->startPeriod)); ?> - <?php echo date("M d, Y", strtotime($evalForm->endPeriod)); ?></strong> <br/>
	 ------------------------------------------------------------<br/><br/><br/>

Please log in to  OAMPI Employee Management System for details.</p>
<p><a href="{{action('EvalFormController@show',$evalForm->id)}}">{{action('EvalFormController@show',$evalForm->id)}}</a></p>