@extends('layouts.main')

@section('metatags')
<title>Performance Appraisal | EMS</title>
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
                <h1>Performance Appraisal Form</h1>
                <p>Below are the appraisal forms you created for your team(s):<br/><br/><br/>
                  <a href="{{action('NewPA_Form_Controller@create')}}" class="pull-right btn btn-md btn-success"><i class="fa fa-plus"></i> Setup New Form </a>
                </p>
                
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Form</th>
                      <th>Description</th>
                      <th style="width: 10%"> </th>
                    </tr>
                  </thead>

                  <tbody>
                    @foreach($forms as $form)
                    <tr>
                      <td style="font-size: larger;"><a href="{{action('NewPA_Form_Controller@preview',$form->id)}}"><i class="fa fa-file-o"></i> {{$form->name}} </a></td>
                      <td style="font-size: smaller;"> {{$form->description}} </td>
                      <td>
                        <a class="btn btn-xs btn-default"><i class="fa fa-pencil"></i> </a>
                        <a href="{{action('NewPA_Form_Controller@preview',$form->id)}} " class="btn btn-xs btn-default"><i class="fa fa-eye"></i> </a>
                        <a class="btn btn-xs btn-default"><i class="fa fa-trash"></i> </a>
                      </td>
                    </tr>

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
<script>
 
  $(function () {
   'use strict';

   

   

  

  
       
        
      
      
   });

   

</script>
<!-- end Page script -->



@stop