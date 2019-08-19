@extends('layouts.main')

@section('metatags')
<title>My Team | OAMPI Evaluation System</title>

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
              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4)">
                      <div class="box-header ">
                        <h4 class="text-blue pull-left"><i class="fa fa-users"></i><strong> Program Tree </strong> {{$campaigns}}</h4>
                        

                      </div><!--end box-header-->

                      <div class="box-body">

                        <div id="basicdiagram" style="width: 100%; height: 500px; border-style: dotted; border-width: 1px; overflow-x: scroll;" />

                        <br/><br/>

                        @if (!is_null($leadershipcheck))
                        
                        @else



                        @endif



                        
                      </div>
                      
                      
              </div><!--end box-primary--><br/><br/>


              @if (!empty($mySubordinates))
              <div class="box box-primary"  style="background: rgba(256, 256, 256, 0.4)">
                      <div class="box-header ">
                        <h2> My Team </h2>
                      </div><!--end box-header-->
                      
                      <div class="box-body">
                        <?php $ctr=1; ?>


                        @foreach( $mySubordinates as $myMen)


                        <div class="col-xs-6 pull-left">

                         
                            <!-- ******** collapsible box ********** -->
                                                <div class="box box-default collapsed-box">
                                                <div class="box-header with-border">
                                                      <!-- /.info-box -->
                                                      <div class="info-box bg-gray">
                                                        <span class="info-box-icon">
                                                          <a href="{{action('UserController@show', $myMen['id'])}} ">

                                                      @if ( file_exists('public/img/employees/'.$myMen['id'].'.jpg') )
                                                        <img src={{asset('public/img/employees/'.$myMen['id'].'.jpg')}} class="img-circle pull-left" alt="User Image" width="95%" style="margin-left:2px;margin-top:2px">
                                                        @else
                                                          <img src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-32x32.png')}}" class="img-circle pull-left" alt="Employee Image"style="padding-right:5px">

                                                          @endif

                                                        </a>
                                                      </span>

                                                        <div class="info-box-content">
                                                          <div class="row">
                                                            <div class="col-lg-9">
                                                              <strong>{{$myMen['lastname']}}, {{$myMen['firstname']}} </strong><br/>
                                                              <small>{{$myMen['position']}}</small> 
                                                            </div>
                                                            <div class="col-lg-3">
                                                              @if ($myMen['logo'] != "white_logo_small.png")
                                                              <img src="{{asset('public/img/'.$myMen['logo'])}}" width="80%" />
                                                              @endif
                                                            </div>
                                                          </div>

                                                          <div class="progress"></div>
                                                                <span class="progress-description">
                                                                 
                                                                </span>

                                                        </div>
                                                        <!-- /.info-box-content -->
                                                      </div>
                                                      <!-- /.info-box -->
                                                      
                                                     
                                                      <!--<a href="{{action('UserController@show', $myMen['id'])}}" class="btn btn-xs btn-success pull-right"  style="margin-left:5px"><i class="fa fa-user"></i> Profile </a> 
                                                      <a href="{{action('UserController@userRequests',$myMen['id'])}}" class="btn btn-xs btn-warning pull-right" style="margin-left:5px"><i class="fa fa-clipboard"></i>  Requests</a>
                                                      <a href="{{action('DTRController@show',$myMen['id'])}}" class="btn btn-xs btn-primary pull-right" style="margin-left:5px"><i class="fa fa-clock-o"></i> View DTR</a>
                                                      <a href="{{action('MovementController@changePersonnel',$myMen['id'])}}" class="btn btn-xs btn-danger pull-right"><i class="fa fa-exchange"></i> Movement </a>-->
                                                      <a target="_blank" href="{{action('UserController@show',$myMen['id'])}}" class="btn btn-xs bg-purple"><i class="fa fa-address-card-o"></i> View Profile </a>
                                                      <a target="_blank"  href="{{action('MovementController@changePersonnel',$myMen['id'])}}" class="btn btn-xs btn-warning pull-right" style="margin:2px"><i class="fa fa-exchange"></i> Movement </a>

                                                      <a target="_blank"  href="{{action('UserController@userRequests',$myMen['id'])}}" class="btn btn-xs btn-success pull-right" style="margin:2px"><i class="fa fa-clipboard"></i>  Requests</a>
                                                      <a target="_blank"  href="{{action('DTRController@show',$myMen['id'])}}" class="btn btn-xs btn-primary pull-right" style="margin:2px"><i class="fa fa-calendar-o"></i>  DTR</a>
                                                      <a  target="_blank" href="{{action('UserController@createSchedule',$myMen['id'])}}" class="btn btn-xs btn-danger pull-right" style="margin:2px"><i class="fa fa-calendar-plus-o"></i>  Plot Sched</a>
                                                      






                                                  


                                                </div>
                                                <!-- /.box-header -->

                                                <div class="box-body">

                                                 
                                                </div>
                                                <!-- /.box-body -->
                                              </div>
                          <!-- ******** end collapsible box ********** -->






                              



                               


                        </div><!--end col-xs-6 -->





                         



                        
                        

                        @endforeach
                      </div><!--end box-body-->
              </div><!--end box-primary-->

              @endif

             

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
      var options = new primitives.orgdiagram.Config();
      //options.childrenPlacementType =  primitives.common.ChildrenPlacementType.Matrix
      options.hasSelectorCheckbox = primitives.common.Enabled.False;
      options.cursorItem = 2;
      options.linesWidth = 2;
      options.linesColor = "gray";
      options.normalLevelShift = 50;
      options.dotLevelShift = 100;
      options.lineLevelShift = 20;
      options.normalItemsInterval = 50;
      options.dotItemsInterval = 10;
      options.lineItemsInterval = 5;
      options.arrowsDirection = primitives.common.GroupByType.Children;


      var items = [

      @if (count($mySubordinates) > 0) 

       new primitives.orgdiagram.ItemConfig({
              id: "{{$user[0]->id}}",
              parent: null,
              title: "{{$user[0]->firstname}} ({{$user[0]->nickname}}) {{$user[0]->lastname}}",
              phone: "{{$user[0]->position}}",
              email: "{{$user[0]->email}}",
              description: " ",
              image: "public/img/employees/{{$user[0]->id}}.jpg",
                        templateName: "contactTemplate",
                        itemTitleColor: "#0f8aca",
                        groupTitle: ""
            }),

      @endif

      @foreach($mySubordinates as $emp)
      <?php $l = strtoupper($emp['lastname']).", ".$emp['firstname']. "(".$emp['nickname'].")"; ?>

        @if ((strlen($l) > 23) || (strlen($emp['position']) > 20))
        new primitives.orgdiagram.ItemConfig({
            id: "{{$emp['id']}}",
            parent:"{{$user[0]->id}}",
            title: "{{$emp['lastname']}}, {{$emp['firstname']}}",
            phone: "{{$emp['position']}} ",
            email: "{{$emp['program']}}",
            description: "{{$emp['email']}}",
            image: "public/img/employees/{{$emp['id']}}.jpg",
                      templateName: "contactTemplate2",
                      itemTitleColor: "#333",
                      groupTitle: "{{$emp['program']}}"
            }),

        @else

        new primitives.orgdiagram.ItemConfig({
            id: "{{$emp['id']}}",
            parent:"{{$user[0]->id}}",
            title: "{{$emp['lastname']}}, {{$emp['firstname']}}",
            phone: "{{$emp['position']}} ",
            email: "{{$emp['program']}}",
            description: "{{$emp['email']}}",
            image: "public/img/employees/{{$emp['id']}}.jpg",
                      templateName: "contactTemplate",
                      itemTitleColor: "#333",
                      groupTitle: "{{$emp['program']}}"
            }),

        @endif
        


      @endforeach

      @foreach($allData as $emp2)

      
          <?php $parent = collect($mySubordinates)->where('leaderID',$emp2->TLid); 

                if (count($parent) > 0)
                {
                  $parentID = $parent->first()['id']; $l = $emp2->lastname." ".$emp2->firstname." ".$emp2->nickname."()"; ?>

                  @if ((strlen($l) > 23) || strlen($emp2->position) > 20)

                  new primitives.orgdiagram.ItemConfig({
                      id: "{{$emp2->id}}",
                      parent:"{{$parentID}}",
                      title: "{{strtoupper($emp2->lastname)}}, {{$emp2->firstname}} ({{$emp2->nickname}})",
                      phone: "{{$emp2->position}}",
                      image: "public/img/employees/{{$emp2->id}}.jpg",
                                templateName: "contactTemplate2",
                                itemTitleColor: "#333",
                                groupTitle: "{{$emp2->program}}"
                      }),

                  @else

                  new primitives.orgdiagram.ItemConfig({
                      id: "{{$emp2->id}}",
                      parent:"{{$parentID}}",
                      title: "{{strtoupper($emp2->lastname)}}, {{$emp2->firstname}} ({{$emp2->nickname}})",
                      phone: "{{$emp2->position}}",
                      image: "public/img/employees/{{$emp2->id}}.jpg",
                                templateName: "contactTemplate",
                                itemTitleColor: "#333",
                                groupTitle: "{{$emp2->program}}"
                      }),

                  @endif
                  


          <?php } else { /*do nothing */ } ?>
      @endforeach
      
        
       
      ];


      var buttons = [];

      buttons.push(new primitives.orgdiagram.ButtonConfig("home", "ui-icon-home", "Home"));
      buttons.push(new primitives.orgdiagram.ButtonConfig("wrench", "ui-icon-wrench", "Wrench"));

      options.buttons = buttons;


      options.items = items;
      options.cursorItem = 0;
      options.templates = [getContactTemplate(), getContactTemplate2()];
      options.onItemRender = onTemplateRender;
      options.horizontalAlignment = 'left';
      //options.hasButtons = primitives.common.Enabled.True;
      options.onButtonClick = function (e, data) {
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
        var win = window.open(url, '_blank');
        win.focus();
      };

      jQuery("#basicdiagram").orgDiagram(options);


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
        }
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
        result.minimizedItemSize = new primitives.common.Size(3, 3);
        result.highlightPadding = new primitives.common.Thickness(2, 2, 2, 2);


        var itemTemplate = jQuery(
          '<div class="bp-item bp-corner-all bt-item-frame">'
          + '<div name="titleBackground" class="bp-item bp-corner-all bp-title-frame" style="top: 2px; left: 2px; width: 296px; height: 20px; color:#333">'
            + '<div name="title" class="bp-item bp-title" style="top: 3px; left: 6px; width: 290px; height: 18px;">'
            + '</div>'
          + '</div>'
          + '<div class="bp-item bp-photo-frame" style="top: 26px; left: 2px; width: 80px; height: 80px;">'
            + '<img name="photo" style="height:80px; width:80px;" />'
          + '</div>'
          + '<div name="phone" class="bp-item" style="top: 26px; left: 100px; width: 162px; height: 18px; font-size: 12px;"></div>'
          + '<div name="email" class="bp-item" style="top: 44px; left: 100px; width: 162px; height: 18px; font-size: 12px;"></div>'
          + '<div name="description" class="bp-item" style="top: 62px; left: 100px; width: 162px; height: 36px; font-size: 10px;"></div>'
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
        result.minimizedItemSize = new primitives.common.Size(3, 3);
        result.highlightPadding = new primitives.common.Thickness(2, 2, 2, 2);


        var itemTemplate = jQuery(
          '<div class="bp-item bp-corner-all bt-item-frame">'
          + '<div name="titleBackground" class="bp-item bp-corner-all bp-title-frame" style="top: 2px; left: 2px; width: 216px; height: 20px; color:#333">'
            + '<div name="title" class="bp-item bp-title" style="top: 3px; left: 6px; width: 208px; height: 18px;">'
            + '</div>'
          + '</div>'
          + '<div class="bp-item bp-photo-frame" style="top: 26px; left: 2px; width: 80px; height: 80px;">'
            + '<img name="photo" style="height:80px; width:80px;" />'
          + '</div>'
          + '<div name="phone" class="bp-item" style="top: 26px; left: 100px; width: 162px; height: 18px; font-size: 12px; overflow:visible"></div>'
          + '<div name="email" class="bp-item" style="top: 44px; left: 100px; width: 162px; height: 18px; font-size: 12px;"></div>'
          + '<div name="description" class="bp-item" style="top: 62px; left: 100px; width: 162px; height: 36px; font-size: 10px;"></div>'
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