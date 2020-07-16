@extends('layouts.main')

@section('metatags')
<title>Open Access Organizational Chart | EMS</title>

<link href="{{URL::asset('public/css/primitives.latest.css?5000')}}" media="screen" rel="stylesheet" type="text/css" />
@endsection

@section('content')


  <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>&nbsp;</h1>
      <ol class="breadcrumb">
        <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="public/css/img/ui-icons_ef8c08_256x240.png"> All Employees</a></li>
        <li class="active">My Team</li>
      </ol>
    </section>

     <section class="content">
      <!-- ******** THE DATATABLE ********** -->
          <div class="row">
             <div class="col-lg-12">
              <div class="box box-primary"  style="background: #fff;"><!-- ; rgba(256, 256, 256, 1) -->
                      <div class="box-header ">
                        <h4 class="text-blue pull-left"><i class="fa fa-users"></i><strong> Your Entire Team </strong> </h4>
                        <img src="{{asset('storage/uploads/newexecs.jpg')}}" width="75%" class="pull-right" />
                        <div style="font-size: smaller; width: 35%;margin:30px 0 0 40px; position: absolute;left:10px; top:95px">
                          <i class="fa fa-info-circle text-primary"></i> Click on an employee node to view and expand the tree. <br/>
                          <i class="fa fa-info-circle text-primary"></i> Shortcut buttons to MOVEMENT, PROFILE, DTR,  <br/>and SCHEDULEappear on the right side of employee node<br/>
                          <i class="fa fa-info-circle text-primary"></i> Drag around the area to scroll left and right

                          <h4 id="loader" style="width:100%;margin-top: 150px">Loading all employees. <br/>Please wait... <img src="public/css/images/loadingspin.gif" /> </h4></div>
                       
                        
                        

                      </div><!--end box-header-->

                      <div class="box-body">
                        <div id="basicdiagram" style="width: 100%; height: 450px; border-style: dotted; border-width: 1px; overflow-x: scroll; margin-top:-20px" />

                        <br/><br/>
                        
                      </div>
                      
                      
              </div><!--end box-primary-->

              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4)">
                <div class="box-header ">
                        <h5 class="text-blue pull-left"><i class="fa fa-user"></i><strong> {{$user2[0]->position}} : </strong> {{$user2[0]->nickname}} {{$user2[0]->lastname}}  </h5>
                      </div>

                      <div class="box-body">
                        <div id="basicdiagram2" style="width: 100%; height: 550px; border-style: dotted; border-width: 1px; overflow-x: scroll;"></div>
                        <br/><br/>
                      </div>   
              </div><!--end box-primary--><br/><br/>


              


              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4)">
                      <div class="box-header ">
                        <h5 class="text-blue pull-left"><i class="fa fa-user"></i><strong> {{$user4[0]->position}} : </strong> {{$user4[0]->nickname}} {{$user4[0]->lastname}}  </h5>
                      </div>

                      <div class="box-body">
                        <div id="basicdiagram4" style="width: 100%; height: 550px; border-style: dotted; border-width: 1px; overflow-x: scroll;"></div>
                        <br/><br/>
                      </div>   
              </div><!--end box-primary--><br/><br/>


            

             

          </div><!--end main row-->
      </section>
          



@endsection


@section('footer-scripts')

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>




<!-- Page script -->



<script type="text/javascript" src="{{URL::asset('public/packages/jquery/jquery-3.3.1.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('public/packages/jquery-ui/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('public/js/primitives.min.js?5100')}}"></script>
<script type="text/javascript" src="{{URL::asset('public/js/primitives.jquery.min.js?5100')}}"></script>

<script type='text/javascript'>
    jQuery(document).ready( function () {


      var wait = function(){
        $('#loader').fadeOut();
      };

      setTimeout(wait, 1000);

      var options = new primitives.orgdiagram.Config();
      //options.childrenPlacementType =  primitives.common.ChildrenPlacementType.Matrix
      options.hasSelectorCheckbox = primitives.common.Enabled.False;
      options.cursorItem = 2;
      options.linesWidth = 1;
      options.linesColor = "gray";
      options.normalLevelShift = 50;
      options.dotLevelShift = 30;
      options.lineLevelShift = 1;
      options.normalItemsInterval = 10;
      options.dotItemsInterval = 8;
      options.lineItemsInterval = 5;
      options.arrowsDirection = primitives.common.GroupByType.Children;
      options.itemTitleFirstFontColor = primitives.common.Colors.White;
      options.itemTitleSecondFontColor = primitives.common.Colors.White;
      


       var items4 = [
          <?php $l4 = strtoupper($user4[0]->lastname).", ".$user4[0]->firstname. " (".$user4[0]->nickname.")"; ?>

          //*** joy
            @if ((strlen($l4) > 23) || (strlen($user4[0]->position) > 20))

           new primitives.orgdiagram.ItemConfig({
                  id: "{{$user4[0]->id}}",
                  parent: null,
                  title: "{{$user4[0]->firstname}} ({{$user4[0]->nickname}}) {{$user4[0]->lastname}}",
                  phone: "{{$user4[0]->position}}",
                  email: "",
                  description:"{{$user4[0]->email}}",
                  image: "public/img/employees/{{$user4[0]->id}}.jpg",
                            templateName: "contactTemplate2",
                            itemTitleColor: "#0f8aca",
                            groupTitle: ""
                }),

           @else

            new primitives.orgdiagram.ItemConfig({
                  id: "{{$user4[0]->id}}",
                  parent: null,
                  title: "{{$user4[0]->firstname}} ({{$user4[0]->nickname}}) {{$user4[0]->lastname}}",
                  phone: "{{$user4[0]->position}}",
                  email: "",
                  description:"{{$user4[0]->email}}",
                  image: "public/img/employees/{{$user4[0]->id}}.jpg",
                            templateName: "contactTemplate",
                            itemTitleColor: "#0f8aca",
                            groupTitle: ""
                }),


            @endif

          //*** direct subordinates
         


          @foreach($joys as $emp)
            <?php $l = strtoupper($emp['lastname']).", ".$emp['firstname']. " (".$emp['nickname'].")"; 

            ?>

              @if ((strlen($l) > 23) || (strlen($emp['position']) > 20))
              new primitives.orgdiagram.ItemConfig({
                  id: "{{$emp['id']}}",
                  parent:"{{$user4[0]->id}}",
                  title: "{{$l}}",
                  phone: "{{$emp['position']}} ",
                  email: "{{$emp['program']}}",
                  description: "{{$emp['email']}}",
                  image: "public/img/employees/{{$emp['id']}}.jpg",
                            templateName: "contactTemplate2",
                            itemTitleColor: " {{$colorAssignment[$emp['programID']] }} ",
                            groupTitle: "{{$emp['program']}}",
                            groupTitleColor: "{{$colorAssignment[$emp['programID']]}}"
                  }),

              @else

              new primitives.orgdiagram.ItemConfig({
                  id: "{{$emp['id']}}",
                  parent:"{{$user4[0]->id}}",
                  title: "{{$l}}",
                  phone: "{{$emp['position']}} ",
                  email: "{{$emp['program']}}",
                  description: "{{$emp['email']}}",
                  image: "public/img/employees/{{$emp['id']}}.jpg",
                            templateName: "contactTemplate",
                            itemTitleColor:"{{$colorAssignment[$emp['programID']]}}",
                            groupTitle: "{{$emp['program']}}",
                            groupTitleColor: "{{$colorAssignment[$emp['programID']]}}"
                  }),

              @endif
              


          @endforeach


         @foreach($teamJoy as $emp)
         
              <?php $mem = $emp['members']; $parentID = $emp['tl_userID']; 
                ?>
                    @foreach($mem as $m)
                      new primitives.orgdiagram.ItemConfig({
                          id: "{{$m->id}}",
                          parent:"{{$parentID}}",
                          title: "{{$m->lastname}}, {{$m->firstname}}",
                          phone: "{{$m->jobTitle}} ",
                          email: "{{$m->program}}",
                          description: "{{$m->email}}",
                          image: "public/img/employees/{{$m->id}}.jpg",
                                    templateName: "contactTemplate2",
                                    itemTitleColor: "{{$colorAssignment[$m->programID]}}",
                                    groupTitle: "{{$m->program}}",
                                    groupTitleColor: "{{$colorAssignment[$m->programID]}}"
                          }),

                    @endforeach

         @endforeach 
          

      ];

      var items3 = [
          <?php $l3 = strtoupper($user3[0]->lastname).", ".$user3[0]->firstname. " (".$user3[0]->nickname.")"; ?>
            //*** LISA
            @if ((strlen($l3) > 23) || (strlen($user3[0]->position) > 20))

           new primitives.orgdiagram.ItemConfig({
                  id: "{{$user3[0]->id}}",
                  parent: null,
                  title: "{{$user3[0]->firstname}} ({{$user3[0]->nickname}}) {{$user3[0]->lastname}}",
                  phone: "{{$user3[0]->position}}",
                  email: "",
                  description:"{{$user3[0]->email}}",
                  image: "public/img/employees/{{$user3[0]->id}}.jpg",
                            templateName: "contactTemplate2",
                            itemTitleColor: "#0f8aca",
                            groupTitle: ""
                }),

           @else

            new primitives.orgdiagram.ItemConfig({
                  id: "{{$user3[0]->id}}",
                  parent: null,
                  title: "{{$user3[0]->firstname}} ({{$user3[0]->nickname}}) {{$user3[0]->lastname}}",
                  phone: "{{$user3[0]->position}}",
                  email: "",
                  description:"{{$user3[0]->email}}",
                  image: "public/img/employees/{{$user3[0]->id}}.jpg",
                            templateName: "contactTemplate",
                            itemTitleColor: "#0f8aca",
                            groupTitle: ""
                }),


            @endif

         


          @foreach($lisas as $emp)
            <?php $l = strtoupper($emp['lastname']).", ".$emp['firstname']. " (".$emp['nickname'].")"; 

                  ?>

              @if ((strlen($l) > 23) || (strlen($emp['position']) > 20))
              new primitives.orgdiagram.ItemConfig({
                  id: "{{$emp['id']}}",
                  parent:"{{$user3[0]->id}}",
                  title: "{{$l}}",
                  phone: "{{$emp['position']}} ",
                  email: "{{$emp['program']}}",
                  description: "{{$emp['email']}}",
                  image: "public/img/employees/{{$emp['id']}}.jpg",
                            templateName: "contactTemplateS",
                            itemTitleColor: " {{$colorAssignment[$emp['programID']] }} ",
                            groupTitle: "{{$emp['program']}}",
                            groupTitleColor: "{{$colorAssignment[$emp['programID']]}}"
                  }),

              @else

              new primitives.orgdiagram.ItemConfig({
                  id: "{{$emp['id']}}",
                  parent:"{{$user3[0]->id}}",
                  title: "{{$l}}",
                  phone: "{{$emp['position']}} ",
                  email: "{{$emp['program']}}",
                  description: "{{$emp['email']}}",
                  image: "public/img/employees/{{$emp['id']}}.jpg",
                            templateName: "contactTemplateS",
                            itemTitleColor: " {{$colorAssignment[$emp['programID']] }} ",
                            groupTitle: "{{$emp['program']}}",
                            groupTitleColor: "{{$colorAssignment[$emp['programID']]}}"
                  }),

              @endif
              


          @endforeach


        

         @foreach($teamLisa as $emp)
         
              <?php $mem = $emp['members']; $parentID = $emp['tl_userID']; ?>
                  

                    @foreach($mem as $m)
                      new primitives.orgdiagram.ItemConfig({
                          id: "{{$m->id}}",
                          parent:"{{$parentID}}",
                          title: "{{$m->lastname}}, {{$m->firstname}}",
                          phone: "{{$m->jobTitle}} ",
                          email: "{{$m->program}}",
                          description: "{{$m->email}}",
                          image: "public/img/employees/{{$m->id}}.jpg",
                                    templateName: "contactTemplateS",
                                    itemTitleColor: "{{$colorAssignment[$m->programID]}}",
                                    groupTitle: "{{$m->program}}",
                                    groupTitleColor: "{{$colorAssignment[$m->programID]}}"
                          }),

                    @endforeach

                 
               

         @endforeach 
          

      ];

      var items2 = [
          <?php $l = strtoupper($user2[0]->lastname).", ".$user2[0]->firstname. " (".$user2[0]->nickname.")";
                ?>


            @if ((strlen($l) > 23) || (strlen($user2[0]->position) > 20))
            //*** HENRY
           new primitives.orgdiagram.ItemConfig({
                  id: "{{$user2[0]->id}}",
                  parent: null,
                  title: "{{$user2[0]->firstname}} ({{$user2[0]->nickname}}) {{$user2[0]->lastname}}",
                  phone: "{{$user2[0]->position}}",
                  email: "",
                  description:"{{$user2[0]->email}}",
                  image: "public/img/employees/{{$user2[0]->id}}.jpg",
                            templateName: "contactTemplate2",
                            itemTitleColor: "#0f8aca",
                            groupTitle: ""
                }),

           @else

            new primitives.orgdiagram.ItemConfig({
                  id: "{{$user2[0]->id}}",
                  parent: null,
                  title: "{{$user2[0]->firstname}} ({{$user2[0]->nickname}}) {{$user2[0]->lastname}}",
                  phone: "{{$user2[0]->position}}",
                  email: "",
                  description:"{{$user2[0]->email}}",
                  image: "public/img/employees/{{$user2[0]->id}}.jpg",
                            templateName: "contactTemplate",
                            itemTitleColor: "#0f8aca",
                            groupTitle: ""
                }),


            @endif

            

          //*** direct subordinates
           @foreach($henrys as $emp)
            <?php $l = strtoupper($emp['lastname']).", ".$emp['firstname']. " (".$emp['nickname'].")"; ?>

              @if ((strlen($l) > 23) || (strlen($emp['position']) > 20))
              new primitives.orgdiagram.ItemConfig({
                  id: "{{$emp['id']}}",
                  parent:"{{$user2[0]->id}}",
                  title: "{{$l}}",
                  phone: "{{$emp['position']}} ",
                  email: "{{$emp['program']}}",
                  description: "{{$emp['email']}}",
                  image: "public/img/employees/{{$emp['id']}}.jpg",
                            templateName: "contactTemplate2",
                            itemTitleColor: " {{$colorAssignment[$emp['programID']] }} ",
                            groupTitle: "{{$emp['program']}}",
                            groupTitleColor: "{{$colorAssignment[$emp['programID']]}}"
                  }),

              @else

              new primitives.orgdiagram.ItemConfig({
                  id: "{{$emp['id']}}",
                  parent:"{{$user2[0]->id}}",
                  title: "{{$l}}",
                  phone: "{{$emp['position']}} ",
                  email: "{{$emp['program']}}",
                  description: "{{$emp['email']}}",
                  image: "public/img/employees/{{$emp['id']}}.jpg",
                            templateName: "contactTemplate",
                            itemTitleColor: " {{$colorAssignment[$emp['programID']] }} ",
                            groupTitle: "{{$emp['program']}}",
                            groupTitleColor: "{{$colorAssignment[$emp['programID']]}}"
                  }),

              @endif
              


          @endforeach


         


         @foreach($teamHenry as $emp)
         
              <?php $mem = $emp['members']; $parentID = $emp['tl_userID']; ?>
                  

                    @foreach($mem as $m)
                      new primitives.orgdiagram.ItemConfig({
                          id: "{{$m->id}}",
                          parent:"{{$parentID}}",
                          title: "{{$m->lastname}}, {{$m->firstname}}",
                          phone: "{{$m->jobTitle}} ",
                          email: "{{$m->program}}",
                          description: "{{$m->email}}",
                          image: "public/img/employees/{{$m->id}}.jpg",
                                    templateName: "contactTemplate2",
                                    itemTitleColor: "{{$colorAssignment[$m->programID]}}",
                                    groupTitle: "{{$m->program}}",
                                    groupTitleColor: "{{$colorAssignment[$m->programID]}}"
                          }),

                    @endforeach 

         @endforeach 

      ];


      var items = [

               <?php $l = strtoupper($user[0]->lastname).", ".$user[0]->firstname. " (".$user[0]->nickname.")"; ?>

                @if ((strlen($l) > 23) || (strlen($user[0]->position) > 20))

                   new primitives.orgdiagram.ItemConfig({
                          id: "{{$user[0]->id}}",
                          parent: null,
                          title: "{{$user[0]->firstname}} ({{$user[0]->nickname}}) {{$user[0]->lastname}}",
                          phone: "{{$user[0]->position}}",
                          email: "",
                          description:"{{$user[0]->email}}",
                          image: "public/img/employees/{{$user[0]->id}}.jpg",
                                    templateName: "contactTemplate2",
                                    itemTitleColor: "#0f8aca",
                                    groupTitle: ""
                        }),

                   @else

                    new primitives.orgdiagram.ItemConfig({
                          id: "{{$user[0]->id}}",
                          parent: null,
                          title: "{{$user[0]->firstname}} ({{$user[0]->nickname}}) {{$user[0]->lastname}}",
                          phone: "{{$user[0]->position}}",
                          email: "",
                          description:"{{$user[0]->email}}",
                          image: "public/img/employees/{{$user[0]->id}}.jpg",
                                    templateName: "contactTemplate",
                                    itemTitleColor: "#0f8aca",
                                    groupTitle: ""
                        }),


                @endif


                @foreach($myTree as $emp)

                <?php $mem = $emp['members']; $parentID = $emp['tl_userID'];?>
                    @if ($parentID !== 1784 ) //remove JOY

                      @foreach($mem as $m)

                        // @if($m->id !== 1784)
                        //remove JOy from Ben
                            new primitives.orgdiagram.ItemConfig({
                            id: "{{$m->id}}",
                            parent:"{{$parentID}}",
                            title: "{{$m->lastname}}, {{$m->firstname}}",
                            phone: "{{$m->jobTitle}} ",
                            email: "{{$m->program}}",
                            description: "{{$m->email}}",
                            image: "public/img/employees/{{$m->id}}.jpg",
                                      templateName: "contactTemplateS",
                                      itemTitleColor: "{{$colorAssignment[$m->programID]}}",
                                      groupTitle: "{{$m->program}}",
                                      groupTitleColor: "{{$colorAssignment[$m->programID]}}"

                            }),

                        // @endif
                        

                      @endforeach

                    @endif
                    

                @endforeach
       
      ];

      var buttons = [];

      buttons.push(new primitives.orgdiagram.ButtonConfig("home", "ui-icon-home", "Home"));
      buttons.push(new primitives.orgdiagram.ButtonConfig("wrench", "ui-icon-wrench", "Wrench"));

      options.buttons = buttons;


      options.items = items;
      options.cursorItem = 0;
      options.templates = [getContactTemplate(),getContactTemplateS(), getContactTemplate2(),getContactTemplate3()];
      options.onItemRender = onTemplateRender;
      options.horizontalAlignment = 'center';

      //*************** HENRY
      var options2 = new primitives.orgdiagram.Config();
      //options.childrenPlacementType =  primitives.common.ChildrenPlacementType.Matrix
      options2.hasSelectorCheckbox = primitives.common.Enabled.False;
      options2.cursorItem = 2;
      options2.linesWidth = 1;
      options2.linesColor = "gray";
      options2.normalLevelShift = 50;
      options2.dotLevelShift = 30;
      options2.lineLevelShift = 1;
      options2.normalItemsInterval = 10;
      options2.dotItemsInterval = 2;
      options2.lineItemsInterval = 5;
      options2.arrowsDirection = primitives.common.GroupByType.Children;
      options2.itemTitleFirstFontColor = primitives.common.Colors.White;
      options2.itemTitleSecondFontColor = primitives.common.Colors.White;

       //*************** JOY
      var options3 = new primitives.orgdiagram.Config();
      //options3.childrenPlacementType =  primitives.common.ChildrenPlacementType.Matrix
      options3.hasSelectorCheckbox = primitives.common.Enabled.False;
      options3.cursorItem = 2;
      options3.linesWidth = 1;
      options3.linesColor = "gray";
      options3.normalLevelShift = 50;
      options3.dotLevelShift = 30;
      options3.lineLevelShift = 1;
      options3.normalItemsInterval = 10;
      options3.dotItemsInterval = 8;
      options3.lineItemsInterval = 5;
      options3.arrowsDirection = primitives.common.GroupByType.Children;
      options3.itemTitleFirstFontColor = primitives.common.Colors.White;
      options3.itemTitleSecondFontColor = primitives.common.Colors.White;

       //*************** JOY
      var options4 = new primitives.orgdiagram.Config();
      //options4.childrenPlacementType =  primitives.common.ChildrenPlacementType.Vertical
      options4.hasSelectorCheckbox = primitives.common.Enabled.False;
      options4.cursorItem = 2;
      options4.linesWidth = 1;
      options4.linesColor = "gray";
      options4.normalLevelShift = 50;
      options4.dotLevelShift = 30;
      options4.lineLevelShift = 1;
      options4.normalItemsInterval = 10;
      options4.dotItemsInterval = 2;
      options4.lineItemsInterval = 5;
      options4.arrowsDirection = primitives.common.GroupByType.Children;
      options4.itemTitleFirstFontColor = primitives.common.Colors.White;
      options4.itemTitleSecondFontColor = primitives.common.Colors.White;



      

      var buttons2 = [];

      buttons2.push(new primitives.orgdiagram.ButtonConfig("home", "ui-icon-home", "Home"));
      buttons2.push(new primitives.orgdiagram.ButtonConfig("wrench", "ui-icon-wrench", "Wrench"));

      options2.buttons = buttons2;


      options2.items = items2;
      options2.cursorItem = 0;
      options2.templates = [getContactTemplate(), getContactTemplate2()];
      options2.onItemRender = onTemplateRender;
      options2.horizontalAlignment = 'center';

      var buttons3 = [];

      buttons3.push(new primitives.orgdiagram.ButtonConfig("home", "ui-icon-home", "Home"));
      buttons3.push(new primitives.orgdiagram.ButtonConfig("wrench", "ui-icon-wrench", "Wrench"));

      options3.buttons = buttons3;


      options3.items = items3;
      options3.cursorItem = 0;
      options3.templates = [getContactTemplateS(),getContactTemplate(), getContactTemplate2()];
      options3.onItemRender = onTemplateRender;
      options3.horizontalAlignment = 'center';

      var buttons4 = [];

      buttons4.push(new primitives.orgdiagram.ButtonConfig("home", "ui-icon-home", "Home"));
      buttons4.push(new primitives.orgdiagram.ButtonConfig("wrench", "ui-icon-wrench", "Wrench"));

      options4.buttons = buttons4;


      options4.items = items4;
      options4.cursorItem = 0;
      options4.templates = [getContactTemplate(), getContactTemplate2()];
      options4.onItemRender = onTemplateRender;
      options4.horizontalAlignment = 'left';

      //*************** END HENRY
      //options.hasButtons = primitives.common.Enabled.True;
      options.onCursorChanged = function (e, data) {
        console.log(data);
        var _token = "{{ csrf_token() }}";
        $.ajax({
                  url:'logAction/C?viewed='+data.context.title+' ['+data.context.id+']', 
                  type:'GET',
                  data:{
                    
                    _token:_token},

                  error: function(response)
                  { console.log(response); return false;
                  },
                  success: function(response4)
                  {
                    console.log(response4);
                   
                  }//end success
              });
        console.log( "User clicked on item '" + data.context.title + "'.");
      };

      options.onButtonClick = function (e, data) {
        var message =""; //"User clicked '" + data.name + "' button for item '" + data.context.title + "'.";
        //alert(message);
        console.log('clicked');
        console.log(data);

        var url = "";

        switch(data.name){
          case "movement":{ url = "movement/changePersonnel/"+data.context.id; message="Open Movement Page for: "+data.context.title; }break;
          case "profile":{ url = "user/"+data.context.id; }break;
          case "DTR": { url = "user_dtr/"+data.context.id; }break;
          case "schedule": { url = "user/"+data.context.id+"/createSchedule"; }break;

        }
        //alert(message);
        var win = window.open(url, '_blank');
        win.focus();
      };

      options2.onCursorChanged = function (e, data) {
        console.log(data);
        var _token = "{{ csrf_token() }}";
        $.ajax({
                  url:'logAction/C?viewed='+data.context.title+' ['+data.context.id+']', 
                  type:'GET',
                  data:{
                    
                    _token:_token},

                  error: function(response)
                  { console.log(response); return false;
                  },
                  success: function(response4)
                  {
                    console.log(response4);
                   
                  }//end success
              });
        console.log( "User clicked on item '" + data.context.title + "'.");
      };
      options2.onButtonClick = function (e, data) {
        var message ="User clicked '" + data.name + "' button for item '" + data.context.title + "'.";
        //alert(message);

        console.log(data);

        var url = "";

        switch(data.name){
          case "movement":{ url = "movement/changePersonnel/"+data.context.id; message="Open Movement Page for: "+data.context.title; }break;
          case "profile":{ url = "user/"+data.context.id; }break;
          case "DTR": { url = "user_dtr/"+data.context.id; }break;
          case "schedule": { url = "user/"+data.context.id+"/createSchedule"; }break;

        }
        //alert(message);
        var win2 = window.open(url, '_blank');
        win2.focus();
      };

      options3.onCursorChanged = function (e, data) {
        console.log(data);
        var _token = "{{ csrf_token() }}";
        $.ajax({
                  url:'logAction/C?viewed='+data.context.title+' ['+data.context.id+']', 
                  type:'GET',
                  data:{
                    
                    _token:_token},

                  error: function(response)
                  { console.log(response); return false;
                  },
                  success: function(response4)
                  {
                    console.log(response4);
                   
                  }//end success
              });
        console.log( "User clicked on item '" + data.context.title + "'.");
      };
      options3.onButtonClick = function (e, data) {
        var message =""; //"User clicked '" + data.name + "' button for item '" + data.context.title + "'.";
        //alert(message);

        console.log(data);

        var url = "";

        switch(data.name){
          case "movement":{ url = "movement/changePersonnel/"+data.context.id; message="Open Movement Page for: "+data.context.title; }break;
          case "profile":{ url = "user/"+data.context.id; }break;
          case "DTR": { url = "user_dtr/"+data.context.id; }break;
          case "schedule": { url = "user/"+data.context.id+"/createSchedule"; }break;

        }
        //alert(message);
        var win3 = window.open(url, '_blank');
        win3.focus();
      };

      options4.onCursorChanged = function (e, data) {
        console.log(data);
        var _token = "{{ csrf_token() }}";
        $.ajax({
                  url:'logAction/C?viewed='+data.context.title+' ['+data.context.id+']', 
                  type:'GET',
                  data:{
                    
                    _token:_token},

                  error: function(response)
                  { console.log(response); return false;
                  },
                  success: function(response4)
                  {
                    console.log(response4);
                   
                  }//end success
              });
        console.log( "User clicked on item '" + data.context.title + "'.");
      };
      options4.onButtonClick = function (e, data) {
        var message =""; //"User clicked '" + data.name + "' button for item '" + data.context.title + "'.";
        //alert(message);

        console.log(data);

        var url = "";

        switch(data.name){
          case "movement":{ url = "movement/changePersonnel/"+data.context.id; message="Open Movement Page for: "+data.context.title; }break;
          case "profile":{ url = "user/"+data.context.id; }break;
          case "DTR": { url = "user_dtr/"+data.context.id; }break;
          case "schedule": { url = "user/"+data.context.id+"/createSchedule"; }break;

        }
        //alert(message);
        var win3 = window.open(url, '_blank');
        win3.focus();
      };

      jQuery("#basicdiagram").orgDiagram(options);
      jQuery("#basicdiagram2").orgDiagram(options2);
      jQuery("#basicdiagram3").orgDiagram(options3);
      jQuery("#basicdiagram4").orgDiagram(options4);


      function onTemplateRender(event, data) {
        switch (data.renderingMode) {
          case primitives.common.RenderingMode.Create:
            /* Initialize widgets here */
            break;
          case primitives.common.RenderingMode.Update:
            /* Update widgets here */
            break;
        }

        var itemConfig = data.context;

        if (data.templateName == "contactTemplate2") {
          data.element.find("[name=photo]").attr({ "src": itemConfig.image, "alt": itemConfig.title });
          data.element.find("[name=titleBackground]").css({ "background": itemConfig.itemTitleColor });

          var fields = ["title", "description", "phone", "email"];
          for (var index = 0; index < fields.length; index++) {
            var field = fields[index];

            var element = data.element.find("[name=" + field + "]");
            if (element.text() != itemConfig[field]) {
              element.text(itemConfig[field]);
            }
          }
        } else if (data.templateName == "contactTemplate") {
          data.element.find("[name=photo]").attr({ "src": itemConfig.image, "alt": itemConfig.title });
          data.element.find("[name=titleBackground]").css({ "background": itemConfig.itemTitleColor });

          var fields = ["title", "description", "phone", "email"];
          for (var index = 0; index < fields.length; index++) {
            var field = fields[index];

            var element = data.element.find("[name=" + field + "]");
            if (element.text() != itemConfig[field]) {
              element.text(itemConfig[field]);
            }
          }
        } else if (data.templateName == "contactTemplate3") {
          data.element.find("[name=photo]").attr({ "src": itemConfig.image, "alt": itemConfig.title });
          data.element.find("[name=titleBackground]").css({ "background": itemConfig.itemTitleColor });

          var fields = ["title", "description", "phone", "email"];
          for (var index = 0; index < fields.length; index++) {
            var field = fields[index];

            var element = data.element.find("[name=" + field + "]");
            if (element.text() != itemConfig[field]) {
              element.text(itemConfig[field]);
            }
          }
        } else if (data.templateName == "contactTemplateS") {
          data.element.find("[name=photo]").attr({ "src": itemConfig.image, "alt": itemConfig.title });
          data.element.find("[name=titleBackground]").css({ "background": itemConfig.itemTitleColor });

          var fields = ["title", "description", "phone", "email"];
          for (var index = 0; index < fields.length; index++) {
            var field = fields[index];

            var element = data.element.find("[name=" + field + "]");
            if (element.text() != itemConfig[field]) {
              element.text(itemConfig[field]);
            }
          }
        }
      }

      

      function getContactTemplate3() {
        var result = new primitives.orgdiagram.TemplateConfig();
        result.name = "contactTemplate3";

        var buttons = [];
        buttons.push(new primitives.orgdiagram.ButtonConfig("movement", "ui-icon-transferthick-e-w", "Movement"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("profile", "ui-icon-person", "View Profile"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("DTR", "ui-icon-clock", "DTR"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("schedule", "ui-icon-calendar", "Plot Work Schedule"));
        result.buttons = buttons;

        result.itemSize = new primitives.common.Size(300, 290);
        result.minimizedItemSize = new primitives.common.Size(9, 9);
        result.highlightPadding = new primitives.common.Thickness(2, 2, 2, 2);


        var itemTemplate = jQuery(
          '<div class="bp-item bp-corner-all bt-item-frame">'
          + '<div name="titleBackground" class="bp-item bp-corner-all bp-title-frame" style="top: 2px; left: 2px; width: 296px; height: 20px; color:#333">'
            + '<div name="title" class="bp-item bp-title" style="top: 3px; left: 6px; width: 290px; height: 18px; font-size:smaller; font-weight:bold">'
            + '</div>'
          + '</div>'
          + '<div class="bp-item bp-photo-frame" style="top: 26px; left: 2px; width: 180px; height: 180px;">'
            + '<img name="photo" style="height:180px; width:180px;" />'
          + '</div>'
          + '<div name="phone" class="bp-item" style="top: 26px; left: 100px; width: 296px; height: 18px; font-size: 12px;"></div>'
          + '<div name="email" class="bp-item" style="top: 44px; left: 100px; width: 296px; height: 18px; font-size: 12px;"></div>'
          + '<div name="description" class="bp-item" style="top: 62px; left: 100px; width: 296px; height: 36px; font-size: 10px;"></div>'
        + '</div>'
        ).css({
          width: result.itemSize.width + "px",
          height: result.itemSize.height + "px"
        }).addClass("bp-item bp-corner-all bt-item-frame");
        result.itemTemplate = itemTemplate.wrap('<div>').parent().html();

        return result;
      }


      function getContactTemplate2() {
        var result = new primitives.orgdiagram.TemplateConfig();
        result.name = "contactTemplate2";

        var buttons = [];
        buttons.push(new primitives.orgdiagram.ButtonConfig("movement", "ui-icon-transferthick-e-w", "Movement"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("profile", "ui-icon-person", "View Profile"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("DTR", "ui-icon-clock", "DTR"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("schedule", "ui-icon-calendar", "Plot Work Schedule"));
        result.buttons = buttons;

        result.itemSize = new primitives.common.Size(300, 120);
        result.minimizedItemSize = new primitives.common.Size(5, 5);
        result.highlightPadding = new primitives.common.Thickness(2, 2, 2, 2);


        var itemTemplate = jQuery(
          '<div class="bp-item bp-corner-all bt-item-frame">'
          + '<div name="titleBackground" class="bp-item bp-corner-all bp-title-frame" style="top: 2px; left: 2px; width: 296px; height: 20px; color:#333">'
            + '<div name="title" class="bp-item bp-title" style="top: 3px; left: 6px; width: 290px; height: 18px; font-size:smaller; font-weight:bold">'
            + '</div>'
          + '</div>'
          + '<div class="bp-item bp-photo-frame" style="top: 26px; left: 2px; width: 80px; height: 80px;">'
            + '<img name="photo" style="height:80px; width:80px;" />'
          + '</div>'
          + '<div name="phone" class="bp-item" style="top: 26px; left: 100px; width: 296px; height: 18px; font-size: 12px;"></div>'
          + '<div name="email" class="bp-item" style="top: 44px; left: 100px; width: 296px; height: 18px; font-size: 12px; font-weight:bolder"></div>'
          + '<div name="description" class="bp-item" style="top: 62px; left: 100px; width: 296px; height: 36px; font-size: 10px;"></div>'
        + '</div>'
        ).css({
          width: result.itemSize.width + "px",
          height: result.itemSize.height + "px"
        }).addClass("bp-item bp-corner-all bt-item-frame");
        result.itemTemplate = itemTemplate.wrap('<div>').parent().html();

        return result;
      }

      function getContactTemplate() {
        var result = new primitives.orgdiagram.TemplateConfig();
        result.name = "contactTemplate";

        var buttons = [];
        buttons.push(new primitives.orgdiagram.ButtonConfig("movement", "ui-icon-transferthick-e-w", "Movement"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("profile", "ui-icon-person", "View Profile"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("DTR", "ui-icon-clock", "DTR"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("schedule", "ui-icon-calendar", "Plot Work Schedule"));
        result.buttons = buttons;

        result.itemSize = new primitives.common.Size(250, 120);
        result.minimizedItemSize = new primitives.common.Size(5, 5);
        result.highlightPadding = new primitives.common.Thickness(2, 2, 2, 2);


        var itemTemplate = jQuery(
          '<div class="bp-item bp-corner-all bt-item-frame">'
          + '<div name="titleBackground" class="bp-item bp-corner-all bp-title-frame" style="top: 2px; left: 2px; width: 216px; height: 20px; color:#333">'
            + '<div name="title" class="bp-item bp-title" style="top: 3px; left: 6px; width: 208px; height: 18px; font-size:smaller; font-weight:bold">'
            + '</div>'
          + '</div>'
          + '<div class="bp-item bp-photo-frame" style="top: 26px; left: 2px; width: 80px; height: 80px;">'
            + '<img name="photo" style="height:80px; width:80px;" />'
          + '</div>'
          + '<div name="phone" class="bp-item" style="top: 26px; left: 100px; width: 162px; height: 18px; font-size: 12px; overflow:visible"></div>'
          + '<div name="email" class="bp-item" style="top: 44px; left: 100px; width: 162px; height: 18px; font-size: 12px; font-weight:bolder"></div>'
          + '<div name="description" class="bp-item" style="top: 62px; left: 100px; width: 162px; height: 36px; font-size: 10px;"></div>'
        + '</div>'
        ).css({
          width: result.itemSize.width + "px",
          height: result.itemSize.height + "px"
        }).addClass("bp-item bp-corner-all bt-item-frame");
        result.itemTemplate = itemTemplate.wrap('<div>').parent().html();

        return result;
      }

      function getContactTemplateS() {
        var result = new primitives.orgdiagram.TemplateConfig();
        result.name = "contactTemplateS";

        var buttons = [];
        buttons.push(new primitives.orgdiagram.ButtonConfig("movement", "ui-icon-transferthick-e-w", "Movement"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("profile", "ui-icon-person", "View Profile"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("DTR", "ui-icon-clock", "DTR"));
        buttons.push(new primitives.orgdiagram.ButtonConfig("schedule", "ui-icon-calendar", "Plot Work Schedule"));
        result.buttons = buttons;

        result.itemSize = new primitives.common.Size(300, 120);
        result.minimizedItemSize = new primitives.common.Size(9, 9);
        result.highlightPadding = new primitives.common.Thickness(2, 2, 2, 2);


        var itemTemplate = jQuery(
          '<div class="bp-item bp-corner-all bt-item-frame">'
          + '<div name="titleBackground" class="bp-item bp-corner-all bp-title-frame" style="top: 2px; left: 2px; width: 296px; height: 20px; color:#333">'
            + '<div name="title" class="bp-item bp-title" style="top: 3px; left: 6px; width: 290px; height: 18px; font-size:smaller; font-weight:bold">'
            + '</div>'
          + '</div>'
          + '<div class="bp-item bp-photo-frame" style="top: 26px; left: 2px; width: 80px; height: 80px;">'
            + '<img name="photo" style="height:80px; width:80px;" />'
          + '</div>'
          + '<div name="phone" class="bp-item" style="top: 26px; left: 100px; width: 296px; height: 18px; font-size: 12px;"></div>'
          + '<div name="email" class="bp-item" style="top: 44px; left: 100px; width: 296px; height: 18px; font-size: 12px; font-weight:bolder"></div>'
          + '<div name="description" class="bp-item" style="top: 62px; left: 100px; width: 296px; height: 36px; font-size: 10px;"></div>'
        + '</div>'
        ).css({
          width: result.itemSize.width + "px",
          height: result.itemSize.height + "px"
        }).addClass("bp-item bp-corner-all bt-item-frame");
        result.itemTemplate = itemTemplate.wrap('<div>').parent().html();

        return result;
      }

      

    });

</script>
<!-- end Page script -->



@stop