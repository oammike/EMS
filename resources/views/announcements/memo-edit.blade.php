@extends('layouts.main')


@section('metatags')
  <title>Announcements - Edit | EMS </title>
    <meta name="description" content="Announcements and Memo Manager">
@stop


@section('content')
  <section class="content-header"  style="margin-bottom:50px">

    <h1>
     <a href="{{action('AnnouncementController@index')}} ">Announcements </a>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{action('HomeController@index')}}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="{{action('AnnouncementController@index')}}">Announcements Manager</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>

    <!-- Main content -->
  <section class="content">

      <div class="row">
        <div class="col-xs-7">
          <div class="box">
            <div class="box-heading"></div>
            <div class="box-body" style="min-height: 1100px; background: url('../storage/uploads/solutions_wall.jpg')bottom center no-repeat; background-size: 100%">

              <p class="text-left"><i class="fa fa-info-circle"></i> Edit {{ $announcement->template }}</p>

              <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10">
                  <h1 class="text-center text-primary">Edit {{ $announcement->template }}<span class="text-orange"> Form</span></h1>
                  <form id="mMemo" method="POST" class="form-" action="{{action('AnnouncementController@index')}}/{{ $announcement->id }}">

                    <div class="form-group">
                      <h4 class="text-primary">Announcement Type:</h4>
                      <span id="hint_type" class="help-block"></span>
                      <label>
                        <input type="radio" name="mType" class="minimal" value="post" @if($announcement->template=="post") checked @endif >
                        Regular Post
                      </label>
                      <label>
                        <input type="radio" name="mType" class="minimal" value="memo" @if($announcement->template=="memo") checked @endif>
                        Memo
                      </label>
                    </div>



                    <h4 class="text-primary">Title:</h4>
                    <span id="hint_title" class="help-block"></span>
                    <div class="input-group">
                      <input type="text" required="required"  name="mTitle" id="mTitle" class="form-control"  autocomplete="off" value="{{ $announcement->title }}" />

                      <div class="input-group-btn">
                        <button id="titleBreaker" type="button" class="btn btn-secondary" >Add Line-break</button>
                      </div>
                      <!-- /btn-group -->

                    </div>


                    <h4 class="text-primary">Decorative Title:</h4>
                    <span id="hint_decor" class="help-block"></span>
                    <div class="input-group">
                      <div class="input-group-btn">
                        <button class="btn btn-secondary" id="miconpicker"></button>
                      </div>
                      <input type="text" name="mDecor" id="mDecor" class="form-control"  autocomplete="off" value="{!! $announcement->decorative_title !!}" />

                      <div class="input-group-btn">
                        <button id="decorBreaker" type="button" class="btn btn-secondary" >Add Line-break</button>
                      </div>
                      <!-- /btn-group -->

                    </div>


                    <div class="row">
                      <div class="col-xs-6">
                        <h4 class="text-primary">Publish Date:</h4>
                        <span id="hint_publishDate" class="help-block"></span>
                        <input type="text" required="required" class="form-control" style="width:50%" name="mPublishDate" id="mPublishDate" placeholder="MM/DD/YYYY" value="{{ date('m/d/Y',strtotime($publishDate)) }}" />

                      </div>

                      <div class="col-xs-6">
                        <h4 class="text-primary">Expiry Date:</h4>
                        <span id="hint_expiryDate" class="help-block"></span>
                        <input type="text" class="form-control" style="width:50%" name="mExpiryDate" id="mExpiryDate" placeholder="MM/DD/YYYY" value="{{ date('m/d/Y', strtotime($publishExpire)) }}" />

                      </div>
                    </div>

                    <h4 class="text-primary">Announcement Content:</h4>
                    <span id="hint_body" class="help-block"></span>
                    <textarea id="editor" name="mBody" >{!! $announcement->message_body !!}</textarea>


                      <h4 class="text-primary">External Link:</h4>
                      <span id="hint_externalLink" class="help-block"></span>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-link"></i></span>
                        <input type="text" name="mExternalLink" id="mExternalLink" class="form-control"  autocomplete="off" value="{!! $announcement->external_link !!}" />

                      </div>

                    <h4 class="text-primary">Feature Image:</h4>
                    <span id="hint_featureImage" class="help-block"></span>
                    <div class="input-group">
                      <input type="file" id="mFeatureImage" name="mFeatureImage" autocomplete="off">
                    </div>

                    <div class="progress progress-xxs">
                      <div id="uploader_progress" style="width: 20%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="20" role="progressbar" class="progress-bar progress-bar-success progress-bar-striped">
                      </div>
                    </div>

                    <div class="form-group">
                      <h4 class="text-primary">Options:</h4>
                      <span id="hint_options" class="help-block"></span>
                      <label>
                        <input type="checkbox" name="isDraft" class="minimal" value="1" @if($announcement->isDraft=="1") checked @endif >
                        Draft
                      </label>
                      <label>
                        <input type="checkbox" name="hidden" class="minimal" value="1" @if($announcement->hidden=="1") checked @endif>
                        Hidden
                      </label>
                      <label>
                        <input type="checkbox" name="showAlways" class="minimal" value="1" @if($announcement->showAlways=="1") checked @endif>
                        Show Always
                      </label>

                    </div>

                    <div class="row" id="buttonsWrapper">
                       <br/><br/>
                        <button id="btPreview" class="btn btn-lg btn-secondary pull-left"><i class="fa fa-eye"></i> Load Preview</button>
                        <button id="btCreate" class="btn btn-lg btn-success pull-right"><i class="fa fa-save"></i> Save Changes</button>
                    </div>
                    <span id="hint_generic" class="help-block"></span>
                  </form>
                </div>
                <div class="col-lg-1"></div>
              </div>

            </div>
          </div>
        </div>


        <div class="col-xs-5">
          <div class="box box-info" style="background: rgba(256, 256, 256, 0.6)">
            <div class="box-header with-border">
              <h3 class="box-title">Preview</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
              </div>
            </div>

            <div class="box-body" id="mads">
              @if($announcement->template=='announcement')
                <div style="background: url('{{ url('/') }}/storage/uploads/announcementbg.png')top left repeat-y; background-size: 50%;background-color: #fff;padding:60px" >
              @else
                <div class="item" >
              @endif

                @if(!is_null($announcement->feature_image))
                  <img src="{{ $announcement->feature_image }}" width="100%" />
                @endif

                  <h4 class="text-orange text-center" style="line-height: 1.5em" >
                    {{ $announcement->title }} @if(!is_null($announcement->decorative_title)) <span class="text-primary"> {!! $announcement->decorative_title !!} @endif<br/>
                      <small>{{ $announcement->publishDate }}</small><br/>
                      <img src="{{ url('/') }}/storage/uploads/divider.png" />
                  </h4>

                  {!! $announcement->message_body !!}

                @if(!is_null($announcement->external_link) && $announcement->external_link!="")
                  <input style="font-weight: bold" class="form-control" type="text" id="bundylink" value="{{ $announcement->external_link }}" />
                  <button class="cp btn btn-xs btn-primary" data-link="bundylink">Copy Link <i class="fa fa-external-link"></i></button>
                @endif

                </div>
            </div>
          </div>
        </div>

      </div>

    <div class="modal" id="messengerModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="messenger_title"></h4>
          </div>
          <div class="modal-body">
            <p id="messenger_body">Confirmation Message</p>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="deny_confirm" data-dismiss="modal">Ok</button>
          </div>
        </div>
        <!-- /.modal-content -->
      </div>
      <!-- /.modal-dialog -->
    </div>

  </section>

@stop

@section('footer-scripts')
<script>
  window.isDraft = 0;
  window.draftId = 0;
  window.editor = null;
  window.decorIcon = "";
  window.feature_image = '{{ $announcement->feature_image }}';
  window.default_date = '{{ $publishDate }}';
  window.media_directory = "{{ url('/public/storage/uploads') }}";
  window.reader = new FileReader();
  window.reader.onload = function (e) {
    window.feature_image = e.target.result;
    constructPreview();
  }


  ClassicEditor
    .create( document.querySelector( '#editor' ),{
        fontSize: {
            options: [
                'tiny',
                'default',
                'big',
                'huge'
            ]
        },
        fontColor: {
            colors: [
                {
                    color: '#666666',
                    label: 'Default'
                },
                {
                    color: '#3c763d',
                    label: 'Green'
                },
                {
                    color: '#a94442',
                    label: 'Red'
                },
                {
                    color: '#3c8dbc',
                    label: 'Blue'
                }

                // ...
            ]
        },
        toolbar: {
          items: [
            'heading',
            '|',
            'bold',
            'italic',
            'underline',
            'link',
            'fontSize',
            'fontColor',
            '|',
            'alignment',
            'insertTable',
            'imageUpload',
            'bulletedList',
            'numberedList',
            '|',
            'undo',
            'redo',
            'blockQuote',
            'horizontalLine',
            'code'
          ]
        },
        language: 'en',
        table: {
          contentToolbar: [
            'tableColumn',
            'tableRow',
            'mergeTableCells',
            'tableCellProperties',
            'tableProperties'
          ]
        },
        //plugins: [ 'SimpleUploadAdapter' ],
        simpleUpload: {
            uploadUrl: '{{ url('/announcement/attach') }}',
            withCredentials: true,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }
    } )
    .then( newEditor => {
        window.editor = newEditor;
        newEditor.on('change', function() {
          constructPreview();
        });
    } )
    .catch( error => {
        console.error( error );
    } );



  $(function () {

    $('#uploader_progress').attr('aria-valuenow', 0).css('width','0%');

  });



  $("#mPublishDate").datepicker({
    startDate: window.default_date
  })
 .on('changeDate', function(e) {
    window.default_date = $("#mPublishDate").val();
    constructPreview();
  });
  $("#mExpiryDate").datepicker({ startDate: window.default_date });

  $('#miconpicker').iconpicker({
    arrowClass: 'btn-danger',
    arrowPrevIconClass: 'fa fa-fas fa-angle-left',
    arrowNextIconClass: 'fa fa-fas fa-angle-right',
    cols: 8,
    header: true,
    iconset: 'fontawesome4',
    placement: 'bottom', // Only in button tag
    rows: 3,
    search: true,
    searchText: 'Search',
    selectedClass: 'btn-success',
    unselectedClass: ''
  })
  .on('change', function(e){
    console.log(e.icon);
    window.decorIcon = '<i class="fa '+e.icon+'"></i>';
    constructPreview();
  });

  $('#decorBreaker').on('click',function(){
    $('#mDecor').val($('#mDecor').val() + "<br/>");
    constructPreview();
  });

  $('#titleBreaker').on('click',function(){
    $('#mTitle').val($('#mTitle').val() + "<br/>");
    constructPreview();
  });



  $('#mMemo input').on('change', function() {
    constructPreview();
  });

  $('#btPreview').click(function(event){
    event.preventDefault();
    constructPreview();
  });

  $('#btDraft').click(function(event){
    window.isDraft = 1;
    $('#isDraft').val('1');
    submitForm();
  });

  $('#btCreate').click(function(event){
    $('#isDraft').val('0');
    window.isDraft=0;
    console.log('create clicked');
    submitForm();
  });


  $("#mFeatureImage").change(function() {
    readUrl(this);
  });

  function readUrl(input) {
    if (input.files && input.files[0]) {
      reader.readAsDataURL(input.files[0]);
    }
  }

  function submitForm(){
    console.log('submitting form');
    $('#mDecor').val(window.decorIcon + $('#mDecor').val());
    $('#mMemo').ajaxForm({
        type: "PUT",
        dataType: 'json',
        error: function(data, status, xhr, $form){
            $('.help-block').addClass('has-error');

            $('#buttonsWrapper').show();

            data = data.responseJSON;

            if(data == undefined){
              $('#hint_generic').text("Could not submit the form, an error has occured on the server. Please try again later.");
              return;
            }

            if(data.mTitle){
              $('#hint_title').text("Please set a descriptive title");
            }
            if(data.mType){
              $('#hint_type').text("Announcement type should only be Memo or Post");
            }
            if(data.mBody){
              $('#hint_body').text("Please fill out the contents for your Announcement");
            }
            if(data.mPublishDate){
              $('#hint_publishDate').text("Please select a proper date for publishing your announcement");
            }
            if(data.mExpiryDate){
              $('#hint_expiryDate').text("Please enter only Date values for the expiry of your announcement");
            }
            if(data.mExpiryDate){
              $('#hint_expiryDate').text("Please enter only Date values for the expiry of your announcement");
            }
            if(data.showAlways){
              $('#hint_options').text("Invalid value for Option: Show Always");
            }
            if(data.hidden){
              $('#hint_options').text("Invalid value for Option: Hidden");
            }
            if(data.isDraft){
              $('#hint_options').text("Invalid value for Option: Draft");
            }

            console.log('failed');

        },
        success: function(responseText, statusText, xhr, $form){
          $('#buttonsWrapper').show();
          if(responseText.success){
              $('#messenger_title').text("Announcement Saved");
              $('#messenger_body').text("Your changes were saved successfully");
              $('#messengerModal').modal('show');
          }else{
            $('#hint_generic').text("Could not submit the form, an error has occured on the server. Please try again later.");
          }

        },
        beforeSend: function() {
          $('#buttonsWrapper').hide();
          console.log('setting submit type to POST');
          reset_errors();
          window.submit_type = "POST";
          $('#uploader_progress').attr('aria-valuenow', 0).css('width','0%');
        },
        uploadProgress: function(event, position, total, percentComplete) {
          var percentVal = percentComplete + '%';
          $('#uploader_progress').attr('aria-valuenow', percentComplete).css('width',percentVal);
        },
      });


  }

  function reset_errors(){
    $('#uploader_progress').attr('aria-valuenow', 0).css('width','0%');
    $('.help-block').removeClass('has-error');
    $('.help-block').text('');
  }


  function constructPreview(){

    var message_body = $((window.editor.getData()));

    var type = $('input[name=mType]:checked', '#mMemo').val();

    var wrapper_div = null;
    if(type=="memo"){
      wrapper_div = $('<div style="background: url(\'{{ url('/') }}/storage/uploads/memobg.png\')top left repeat-y; background-size: 50%;background-color: #fff;padding:60px" class="item" ></div>');
    }else{
      wrapper_div = $('<div class="item" ></div>');
    }

    if(window.feature_image != ""){
      wrapper_div.append($('<img src="'+ window.feature_image +'" width="100%" style="margin: 5px auto;" />'));
    }


    wrapper_div.append($('<h4 class="text-orange text-center" style="line-height: 1.5em" >'+ $('#mTitle').val() +' <span class="text-primary">'+ window.decorIcon  + $('#mDecor').val() +'</span><br/><small>'+ window.default_date +'</small><br/><img src="{{ url('/') }}/storage/uploads/divider.png" /></h4>'));


    wrapper_div.append(message_body);

    if($('#mExternalLink').val()!=""){
      var external_link = $('<input style="font-weight: bold" class="form-control" type="text" id="bundylink" value="' + $('#mExternalLink').val() + '" /><button class="cp btn btn-xs btn-primary" data-link="bundylink">Copy Link <i class="fa fa-external-link"></i></button>');
      wrapper_div.append(external_link);

    }

    $('#mads').html(wrapper_div);



  }

</script>
@stop