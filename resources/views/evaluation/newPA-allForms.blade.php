@extends('layouts.main')

@section('metatags')
<title>All Performance Appraisal Forms | EMS</title>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>&nbsp;</h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('NewPA_Form_Controller@index')}}"> Performance Appraisal</a></li>
        <li class="active">Index</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">

              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4);padding:30px">
                <h1>All Performance Appraisal Forms<a href="{{action('NewPA_Form_Controller@create')}}" class="pull-right btn btn-md btn-success"><i class="fa fa-plus"></i> Setup New Form </a></h1>
                <p>Below are all appraisal forms created:<br/><br/><br/>
                  
                </p>
         
                
                <table class="table table-bordered" id="allForms">
                  <thead>
                    
                      <th style="width:25%">Form Type</th>
                      <th style="width:30%">Description</th>
                      <th style="width: 10%">Actions </th>
                      <th style="width:35%"class="text-center">Applies To</th>
                      
                    
                  </thead>

                  <tbody>
                    @foreach($forms as $form)
                    <tr>
                      <td style="font-size: small;">

                        @if ( $form->typeID == '1' || $form->typeID == '2' )
                        <a href="{{action('NewPA_Form_Controller@preview',$form->id)}}">
                          <span class="pull-left" style="background-color: #f1d61c;padding: 10px;color:#fff; font-size: x-small;font-weight: bolder;"><i class="fa fa-file-o"></i> FIC </span>&nbsp;&nbsp; <p class="pull-left" style="width: 60%;padding-left: 10px">{{$form->name}} </p></a>

                        @elseif ( $form->typeID == '3' || $form->typeID == '4' )
                        <a href="{{action('NewPA_Form_Controller@preview',$form->id)}}">
                          <span class="pull-left" style="background-color: #72a919;padding: 10px;color:#fff;  font-size: x-small;font-weight: bolder;"><i class="fa fa-file-o"></i> SIC </span>&nbsp;&nbsp; <p class="pull-left" style="width: 60%;padding-left: 10px">{{$form->name}} </p>  </a>

                        @else
                        <a href="{{action('NewPA_Form_Controller@preview',$form->id)}}" >
                          <span class="pull-left" style="background-color: #0778dc;padding: 10px;color:#fff ; font-size: x-small;font-weight: bolder;"><i class="fa fa-file-o"></i> PM </span> &nbsp;&nbsp;<p class="pull-left" style="width: 60%;padding-left: 10px">{{$form->name}} </p>   </a>

                        @endif
                        
                      </td>
                      <td style="font-size: smaller; white-space: pre;"> {!! $form->description !!} </td>

                      <td>
                        <a class="btn btn-xs btn-default"><i class="fa fa-pencil"></i> </a>
                        <a href="{{action('NewPA_Form_Controller@preview',$form->id)}} " class="btn btn-xs btn-default"><i class="fa fa-eye"></i> </a>
                        <a class="btn btn-xs btn-default" data-toggle="modal" data-target="#myModal{{$form->id}}"><i class="fa fa-trash"></i> </a>
                      </td>

                      @if($form->typeID == 5 ||  $form->typeID == 6 )
                          <td>
                            <?php $exists = collect($hasExistingForms)->where('formID',$form->id); ?>
                            <ul style="list-style: none">
                              @foreach($exists as $e)

                                @if(in_array($e->user_id,$evaluatedAlready))
                                 <?php $toshow = collect($evals)->where('user_id',$e->user_id); ?>

                                    <li> <a data-toggle="modal" data-target="#myModal{{$e->id}}"  class="pull-right"><i class="fa fa-times"></i></a><img src="{{url('/')}}/public/img/employees/{{$e->user_id}}.jpg" width="50" class="pull-left" /><strong>{{$e->lastname}}, {{$e->firstname}}</strong> <br/><em style="font-size: small;">{{$e->jobTitle}}</em> <br/>
                                      <a class="pull-right btn btn-sm btn-default" href="{{action('NewPA_Evals_Controller@show',['id'=>$toshow->first()->id])}}" target="_blank"><i class="fa fa-eye"></i>&nbsp; View Evaluation </a>
                                      <span class="label label-danger">{{$toshow->first()->finalRating}} </span> 

                                      <br/><br/><br/><br/>
                                    </li>

                                   <!--  @include('layouts.modals', [
                                    'modelRoute'=>'newPA_form_user.destroy',
                                    'modelID' => $e->id, 
                                    'modelName'=>$e->firstname." ".$e->lastname, 
                                    'modalTitle'=>'Delete', 
                                    'modalMessage'=>'Are you sure you want to remove '.$e->firstname.' '.$e->lastname.' from using this form?', 
                                    'formID'=>'deleteUserForm',
                                    'icon'=>'glyphicon-trash' ]) -->


                                @else

                                     <li> <a data-toggle="modal" data-target="#myModal{{$e->id}}"  class="pull-right"><i class="fa fa-times"></i></a><img src="{{url('/')}}/public/img/employees/{{$e->user_id}}.jpg" width="50" class="pull-left" style="padding:5px" /><strong>{{$e->lastname}}, {{$e->firstname}}</strong> <br/><em style="font-size: small;">{{$e->jobTitle}}</em> <br/>
                                      <a target="_blank" class="pull-right btn btn-sm btn-primary" href="{{action('NewPA_Form_Controller@evaluate',['id'=>$e->user_id, 'form'=>$form->id])}}"><i class="fa fa-thumbs-up"></i>&nbsp; Evaluate Now </a><br/><br/><br/><br/>
                                    </li>

                                    <!-- @include('layouts.modals', [
                                    'modelRoute'=>'newPA_form_user.destroy',
                                    'modelID' => $e->id, 
                                    'modelName'=>$e->firstname." ".$e->lastname, 
                                    'modalTitle'=>'Delete', 
                                    'modalMessage'=>'Are you sure you want to remove '.$e->firstname.' '.$e->lastname.' from using this form?', 
                                    'formID'=>'deleteUserForm',
                                    'icon'=>'glyphicon-trash' ]) -->

                                @endif
                             


                              @endforeach
                            </ul>
                          </td>
                      
                      @else
                          <td>
                            <p style="margin-left: 40px">all <em>{{$form->type}}</em> </p>
                          </td>


                      
                      @endif
                      
                     

                    </tr>

                    

                    @include('layouts.modals', [
                          'modelRoute'=>'performance.destroy',
                          'modelID' => $form->id, 
                          'modelName'=>$form->name, 
                          'modalTitle'=>'Delete', 
                          'modalMessage'=>'Are you sure you want to delete this?', 
                          'formID'=>'deleteMovement',
                          'icon'=>'glyphicon-trash' ])


                    @endforeach
                  </tbody>
                  

                </table>
               
                





             



              </div><!--end box-primary-->


             

             

          </div><!--end main row-->
      </section>

      

      
 

    



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script -->
<script type="text/javascript">
 
  $(function () {
   'use strict';


    $("#allForms").DataTable({
                "deferRender": true,
                "processing":true,
                "stateSave": true,
                "order": [ 0, "desc" ],
                "lengthMenu": [20, 100, 500],
                "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
                
                //"lengthChange": true,
                "oLanguage": {
                   "sSearch": "<strong>Refine Results</strong> <br/>To re-order entries, click on the sort icon to the right of column headers. <br/>To filter out results, just type in the search box anything you want to look for:",
                   "class": "pull-left"
                 },

                
        });

   

   

  

  
       
        
      
      
   });

   

</script>
<!-- end Page script -->



@stop