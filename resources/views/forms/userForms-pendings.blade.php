@extends('layouts.main')

@section('metatags')
<title>All Pending Forms | OAMPI Evaluation System</title>

<style type="text/css">
    /* Sortable items */

    #qr-code-container{
      margin: 0 auto;
      width: 480px;
        height: 480px;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: 50% 50%;
    }

    .sortable-list {
      background: none; /* #fcedc6;*/
      list-style: none;
      margin: 0;
      min-height: 60px;
      padding: 10px;
    }
    .sortable-item {
      background-color: #fcedc6;
      
      cursor: move;
      
      font-weight: bold;
      margin: 2px;
      padding: 10px 0;
      text-align: center;
    }

    /* Containment area */

    #containment {
      background-color: #FFA;
      height: 230px;
    }


    /* Item placeholder (visual helper) */

    .placeholder {
      background-color: #ccc;
      border: 3px dashed #fcedc0;
      min-height: 150px;
      width: 180px;
      float: left;
      margin-bottom: 5px;
      padding: 45px;
    }
</style>
@endsection

@section('bodyClasses')
<!--sidebar-collapse-->
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        All Users with Pending BIR 2316 forms 
        
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        
        <li class="active">Employees</li>
      </ol>
    </section>

     <section class="content">
     

          <div class="row" style="margin-top:50px">
           
            

            <div class="col-lg-12"> <!-- ******************* LEFT COLUMN ***************** -->

              <!-- TABLE: LEFT -->
              <div class="box-body">

                <!-- Custom Tabs -->
                                              <div class="nav-tabs-custom">
                                                <ul class="nav nav-tabs">
                                                  <li><a href="{{action('UserFormController@index')}}"><strong class="text-primary ">Signed BIR 2316  </strong></a></li>

                                                  <li><a href="{{action('UserFormController@userTriggered')}}" ><strong class="text-primary">User-specified Disqualifications <span id="inactives"></span></strong></a></li>

                                                  <li class="active"><a href="{{action('UserFormController@allPending')}}" ><strong class="text-primary">All Pendings <span id="inactives"></span> &nbsp; <span class="label label-warning" style="font-size: small;"> ({{count($allDisq)}}) </span> </strong></a></li>
                                                  


                                                </ul>
                                                

                                                <div class="tab-content">
                                                  <div class="tab-pane active" id="tab_1"> <!-- ACTIVE EMPLOYEES -->
                                                    <div class="row" style="margin-top:50px">

                                                       
                                                           <table class="table no-margin table-bordered table-striped" id="active" width="95%" style="margin:0 auto;" >

                                                            <thead>
                                                              <th>Employee</th>
                                                              <th>Program</th>
                                                              
                                                            </thead>
                                                            <tbody>
                                                              @foreach($allDisq as $a)
                                                              <tr>
                                                                <td><a href="{{ action('UserController@show',['id'=>$a->userID] )}}" target="_blank"> {{$a->lastname}}, {{$a->firstname}} <em>({{$a->nickname}} )</em></a>
                                                                   </td>

                                                               
                                                                <td><strong style="font-size: small">{{$a->program}}</strong> </td>
                                                                
                                                              </tr>
                                                              @endforeach
                                                            </tbody>
                                                            
                                                          </table>
                                                         

                                                          
                                                       
                                                    </div>
                                                      <!-- /.row -->
                                                    

                                                  </div><!--end pane1 -->
                                                  <!-- /.tab-pane -->



                                                 



                                                 

                                                 


                                                </div>
                                                <!-- /.tab-content -->
                                              </div>
                                              <!-- nav-tabs-custom -->


                 
              </div>
                <!-- /.box-body -->
              <div class="box-footer clearfix" style="background:none">
               
                
              </div>
                <!-- /.box-footer -->
              <!-- /.box -->
            </div><!--end left -->


           


            
           

          
          </div><!-- end row -->

       
     </section>
          
  


@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>



<!-- Page script <small> '+full.employeeNumber+'</small> -->

 
 <script type="text/javascript">

 $(function () {
   'use strict';

   $("#active").DataTable({
      "responsive":true,
      "scrollX":true,
      "stateSave": false,
       "processing":true,
       "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
      //"order": [[ 0, "desc" ]],
     
      "lengthChange": true,
      "oLanguage": {
         "sSearch": "<small>To refine search, simply type-in</small><br> any values you want to look for:",
         "class": "pull-left"
       }
    });
  
  

    



   
});

 </script>



<!-- end Page script -->


@stop