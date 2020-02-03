@extends('layouts.main')

@section('metatags')
  <title>Rewards Unavailable</title>
  
@stop


@section('content')


  <section class="content-header">
    <h1><i class="fa fa-gift"></i> Open Access BPO Rewards <small id="points_counter"></small></h1>

    <ol class="breadcrumb">
      <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Rewards</li>
    </ol>
  </section>

  <section class="content" id='holder'>
    <div class="row">
      <div class="col-xs-12">
        <div class="box"  style="background:url('<?php echo url("/"); ?>/storage/uploads/COFFEE_prm.jpg')center center no-repeat rgba(256, 256, 256, 0.4);background-size: cover; min-height: 1200px;padding:50px">
          <div class="box-heading"></div>
          <div class="box-body">
            <h1 class="text-center text-primary" style="background: rgba(256, 256, 256, 0.4);padding: 30px;margin-top: 100px;text-shadow: 2px 2px #fff">Rewards Currently Unavailable</h1>
            <h4 class="text-center" style="background: rgba(256, 256, 256, 0.4);padding: 30px">{!! $msg !!} </h4>
           
          </div>
        </div>
      </div>    
    </div>

		</div>
  </section>    
	

	
@stop

@section('footer-scripts')


	<script>
		window.selected_reward_id = 0;
		$(function() {

      
  

  


			
		});
	</script>
@stop
