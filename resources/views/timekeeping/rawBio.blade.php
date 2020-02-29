 <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
 <link href="{{asset('public'.elixir('css/all.css'))}}" rel="stylesheet" />
 <link href="{{asset('public/css/font-awesome.min.css')}}" rel="stylesheet">

<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>

<body>

	@if ($canUpload)
	<div style="background: rgba(72, 164, 220, 0.4); position: fixed;right: 0; padding:50px; width: 25%" class="pull-right">
		<img src="../public/img/oabpo_logo_new.jpg" /><h2><br/>Insert Bio Logs<br/>
			<small>for: <strong style="font-size: smaller;">{{$user->lastname}},{{$user->firstname}} </strong><br/>
			</small><em style="font-size: small;">{{$user->campaign->first()->name}} </em></h2>
		<label>Log Date: <br/><small>(actual date of employee's finger scan)</small></small> 
			<input class="form-control datepicker" type="text" name="productionDate" id="productionDate" placeholder="YYYY-MM-DD" datepicker /> </label><br/><br/>
		<label>Time (24hr format): <input class="form-control" type="text" name="logTime" id="logTime" placeholder="HH:mm:ss" /></label><br/><br/>
		<label><input type="radio" name="logType" value="1" checked="checked"> IN </label>&nbsp;&nbsp;&nbsp;
		<label><input type="radio" name="logType" value="2"> OUT </label><BR/><br/>
		
		<a class="btn btn-success btn-lg" id="save">Save Bio Log</a>
	</div>
	@endif
	<table style="border:1px dotted #666; font-family:Arial,sans-serif; font-size:0.9em; font-weight:normal; width:50%">
			  <tr>
			    <th  style="border-bottom:3px solid #333">Production Date</td>
			    <th  style="border-bottom:3px solid #333">Logs</th>
			  </tr>
			@foreach($dtr as $data)
			<tr>
			  <td id="{{$data->id}}" style="border-bottom:1px dotted #333" align="left">&nbsp;&nbsp;&nbsp;{{date('Y-M-d l',strtotime($data->Production_Date))}}</td>
			 
			  <td style="border-bottom:1px dotted #333">
			    <div style="border-bottom:1px solid #666; @if($data->Log_Type == 'Log In') background: rgba(231, 255, 198, 0.4);@else background: rgba(245, 183, 164, 0.4); @endif">
			    	@if($canUpload)
			    	<a style="margin-left: 5px" data-toggle="modal" data-target="#myModal{{$data->logID}}" class="btn btn-xs btn-primary pull-right"><i class="fa fa-times"></i>  </a>@endif

			    	{{$data->Log_Type}} : 
			    	<span style="float:right">{{$data->logTime}}</span>
			    </div><br/>
			     


			  </td>

			  @if ($canUpload)
			  <div class="modal fade" id="myModal{{$data->logID}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				  <div class="modal-dialog">
				    <div class="modal-content">
				      <div class="modal-header">
				        
				          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				          <h4 class="modal-title" id="myModalLabel"> Delete Biometric Log</h4>
				        
				      </div>
				      <div class="modal-body">
				       Are you sure you want to delete this bio log?
				      </div>
				      <div class="modal-footer no-border">
				        {{ Form::open(['route' => ['logs.deleteBio', $data->logID], 'method'=>'POST','class'=>'btn-outline pull-right', 'id'=> "form_".$data->logID ]) }}     
				          <button type="submit" class="btn btn-primary trash glyphicon ">Yes</button>
				        
				        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>{{ Form::close() }}
				      </div>
				    </div>
				  </div>
			  </div>
			  @endif

			</tr>

			

			@endforeach

	</table>

</body>

<!-- REQUIRED JS SCRIPTS -->
<script type="text/javascript" src="{{asset('public'.elixir('js/all.js'))}}"></script>
<script type="text/javascript" src="{{asset('public/js/notify.min.js')}}"></script>

<script type="text/javascript">
	$( ".datepicker" ).datepicker({dateFormat:"YYYY-mm-dd"});

	$('#save').on('click',function(e){
		e.preventDefault();e.stopPropagation();
		var logType_id  = $('input[name="logType"]:checked').val();
		var productionDate = $('#productionDate').val();
		var logTime =  $('#logTime').val();
		var _token = "{{ csrf_token() }}";
		 $.ajax({
                              url: "{{action('LogsController@saveBioLog')}}",
                              type:'POST',
                              data:{ 
                                'logTime': logTime,
								'productionDate': productionDate,
								'logType_id':logType_id,
								'user_id': "{{$id}}",
								'_token':_token
                              },
                              success: function(response){
                              	
                              	if (response.success){
                              		$.notify("Biometric log saved for production date: \n"+productionDate + " "+logTime,{className:"success", globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );
									console.log(response);
									 window.setTimeout(function(){
	                                   window.location.href = "{{action('LogsController@viewRawBiometricsData',$id)}}";
	                                 }, 4000);

                              	}else
                              	{
                              		$.notify("An error occured while saving bio log: \n"+productionDate + " "+logTime+"\n"+response.msg,{className:"error", globalPosition:'right middle',autoHideDelay:3000, clickToHide:true} );

                              	}
                                
                              }
                            });

		

	});

</script>

</html>
	 

