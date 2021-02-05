@extends('layouts.main')

@section('metatags')
<title>All Disqualified | OAMPI Evaluation System</title>

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
        All Disqualified Users 
        
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

                                                  <li class="active"><a href="{{action('UserFormController@userTriggered')}}" ><strong class="text-primary">User-specified Disqualifications <span id="inactives"></span></strong></a></li>
                                                  


                                                </ul>
                                                

                                                <div class="tab-content">
                                                  <div class="tab-pane active" id="tab_1"> <!-- ACTIVE EMPLOYEES -->
                                                    <div class="row" style="margin-top:50px">

                                                       
                                                           <table class="table no-margin table-bordered table-striped" id="active" width="95%" style="margin:0 auto;" >

                                                            <thead>
                                                              <th>Employee</th>
                                                              <th>Program</th>
                                                              <th>Reason for Disqualification</th>
                                                              <th>Date Submitted</th>
                                                              <th>Action</th>
                                                            </thead>
                                                            <tbody>
                                                              @foreach($allDisq as $a)
                                                              <tr>
                                                                <td>{{$a->lastname}}, {{$a->firstname}} <em>({{$a->nickname}} )</em>
                                                                   </td>

                                                               
                                                                <td><strong style="font-size: small">{{$a->program}}</strong> </td>
                                                                <?php switch ($a->reasonID) {
                                                                  case '1': $r ="Individual deriving other non-business, non-profession-related income in addition to compensation not otherwise subject to final tax.";
                                                                    # code...
                                                                    break;
                                                                  
                                                                  case '2': $r ="Individual deriving purely compensation income from a single employer, although the income of which has been correctly subjected to withholding tax, but whose spouse is not entitled to substituted filing.";
                                                                    # code...
                                                                    break;

                                                                  case '3': $r ="Non-resident alien engaged in trade or business in the Philippines deriving purely compensation income or compensation income and other business or profession related income.";
                                                                    # code...
                                                                    break;
                                                          
                                                                }
                                                                 ?>
                                                                <td><strong>{{$a->reasonID}}:</strong> <em style="font-size: smaller;">{{$r}} </em>  </td>
                                                                <td>{{ date('Y-m-d h:i A',strtotime($a->dateSubmitted)) }} </td>
                                                                <td><a target="_blank" class="btn btn-sm btn-danger"  href="viewUserForm?s=1&f=BIR2316&u={{$a->id}}"><i class="fa fa-download"></i> Download Signed 2316</a> </td>
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
      "stateSave": true,
       "processing":true,
       "dom": '<"col-xs-1"f><"col-xs-11 text-right"l><"clearfix">rt<"bottom"ip><"clear">',
      "order": [[ 2, "DESC" ]],
     
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