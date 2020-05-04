@extends('layouts.main')

@section('metatags')
<title> {{$engagement[0]->activity}} | EMS</title>
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>&nbsp;</h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('UserController@index')}}"> All Employees</a></li>
        <li class="active">My Team</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">

              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4);padding:30px">

               

                {!! $engagement[0]->content!!}


               
              

                <hr/>

                
                <div class="row">
                  
                   
                  
                    
                  

                  
                  
                </div>

               




              </div><!--end box-primary-->


             

             

          </div><!--end main row-->
      </section>

   

      
 

    



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>

<script type="text/javascript" src="../public/js/jquery.form.min.js"></script>

<!-- Page script -->
<script>

   $(document).ready(function(){


  

   
   

     
    });






</script>
<!-- end Page script -->



@stop