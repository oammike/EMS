@extends('layouts.main')

@section('metatags')
<title>New Department/Program | OAMPI Evaluation System</title>

<style type="text/css">
/* Sortable items */

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

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        New OAMPI Program/Department
       
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{action('CampaignController@index')}}"> All Departments</a></li>
        <li class="active">Add New Program/Department</li>
      </ol>
    </section>

     <section class="content">
      

          <div class="row" style="margin-top:50px">
           
            

            <div class="col-lg-12"> <!-- ******************* LEFT COLUMN ***************** -->

              <div class="box box-default">

              <!-- TABLE: LEFT -->
                <div class="box-header">

                 
                  <h2 class="text-center"> <img class="text-center" src="{{asset('public/img/logo-transparent.png')}}" width="90" /></h2>
                  <h3 class="text-center"> New OAMPI Program/Department <br/></h3>
                  

                </div>
                <div class="box-body">
                  
                  {{ Form::open(['route' => 'campaign.store', 'class'=>'col-lg-12', 'id'=> 'addCampaign','name'=>'addCampaign' ]) }}
                  <table class="table" style="width:85%; margin: 5px auto">
                    <tr>
                      <td><label>Program/Dept Name: </label><input type="text" name="name" id="name" placeholder="type in program/department name" class="form-control required" />
                                        <div id="alert-name" style="margin-top:10px"></div>

                                        
                          <br/>
                          
                          
     
                      </td>
                     

                    </tr>
                    </table><p>&nbsp;</p>

               
                  <p class="text-center"> 
                    
                    <input type="submit" class="btn btn-lg btn-primary btn-flat" name='submit' value="Save" />
                    <div id="alert-submit" style="margin-top:20px"></div>
                  </p>


                  
                 

                  
                 </form>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix" style="background:none">
                 
                  
                </div>
                <!-- /.box-footer -->
              </div><!-- /.box -->
            </div><!--end left -->


           


            
           

          
          </div><!-- end row -->

       
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

  






   $('#addCampaign').on('submit', function(e) {

      var _token = "{{ csrf_token() }}";
     
      var name = $('#name').val();
      

     

      var alertCampaign = $('#alert-name');
     


      if (!validateRequired(name,alertCampaign,"type in program/department name")) { 
        console.log('not valid Program '); e.preventDefault(); e.stopPropagation();
        return false;
      }  else {
            //save first the new position
            console.log("save pos first");
              $.ajax({
                                url:"{{action('CampaignController@store')}} ",
                                type:'POST',
                                data:{
                                  'name': name,
                                  _token:_token},

                                error: function(response2)
                                { console.log("Error saving program/department: ");
                                  console.log(response2); return false;
                                },
                                success: function(response2)
                                {
                                   $('input[name="submit"').fadeOut();
                                          var htmcode = "<span class=\"success\"> <i class=\"fa fa-save\"></i> New Program / Department  data saved. <br /><br/>";
                                         
                                          htmcode += "<a href=\"{{action('CampaignController@index')}}\" class='btn btn-sm btn-default text-black pull-right'><i class='fa fa-reply'></i> Back to All Programs </a> <br/><br/>";
                                          
                                          $('#alert-submit').addClass('alert alert-success').fadeIn().html(htmcode); 

                                }

                    }); //end ajax

          
          }//end else valid new position

        
      
      e.preventDefault(); e.stopPropagation();
      return false;
    }); //end addCampaign





   $( ".datepicker" ).datepicker();

       

   });



function validateRequired(param, availability, defaultval) {

        
        if (param == null){

          availability.addClass('alert alert-danger').fadeIn();
            availability.html('<span class="success"> <i class="fa fa-warning"></i> This field is required. </span>');  
             return false;
        }

        else if(param.length <= 0 || param === defaultval) { 
            
            availability.addClass('alert alert-danger').fadeIn();
            availability.html('<span class="success"> <i class="fa fa-warning"></i> This field is required. </span>');   
             return false;         
            

        } else{
            availability.removeClass();
            availability.html('');
            return true;
                      
        }
       

}

   


   

 
</script>
<!-- end Page script -->

<!-- <script type="text/javascript" src="{{asset('public/js/jquery.validate.js')}}"></script> -->

@stop