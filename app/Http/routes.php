<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/getAllPosts/{id}', array(
      'as'=> 'getAllPosts',
      'uses'=>'EngagementPublicController@getAllPosts') );

Route::auth();

Route::get('/barista/{code?}', 'RewardsHomeController@barista');
Route::get('/check_rewards_points/{code?}', 'RewardsHomeController@check_points');
Route::get('/fetch_coffees', 'RewardsHomeController@fetch_coffees');
Route::post('/create-order', 'RewardsHomeController@create_order');

Route::group( [ 'middleware' => ['auth'] ], function ()
  {
    Route::get('/', 'HomeController@index');
    Route::get('/home', 'HomeController@home');



    Route::resource('immediateHead', 'ImmediateHeadController');
    Route::resource('immediateHeadCampaign', 'ImmediateHeadCampaignController');
    Route::resource('evalForm', 'EvalFormController');
    Route::resource('evalSetting', 'EvalSettingController');
    Route::resource('campaign', 'CampaignController');
    Route::resource('page', 'HomeController');
    Route::resource('user','UserController');
    Route::resource('userType','UserTypeController');
    //Route::resource('userType_role','UserTypeRController');
    Route::resource('manpower','ManpowerController');
    Route::resource('movement','MovementController');
    Route::resource('position','PositionController');
    Route::resource('role','RoleController');
    Route::resource('notification','NotificationController');

    Route::resource('schedule','ScheduleController');
    Route::resource('cutoffs','CutoffsController');
    Route::resource('biometrics','BiometricsController');
    Route::resource('tempUpload','TempUploadController');
    Route::resource('logs','LogsController');
    Route::resource('user_dtr','DTRController');
    Route::resource('user_dtrp','UserDTRPController');
    Route::resource('user_cws','UserCWSController');
    Route::resource('user_vl','UserVLController');
    Route::resource('user_sl','UserSLController');
    Route::resource('user_obt','UserOBTController');
    Route::resource('user_lwop','UserLWOPController');
    Route::resource('user_memo','UserMemoController');
    Route::resource('user_fl','UserFamilyleaveController');
    Route::resource('user_notification','UserNotificationController');
    Route::resource('user_ot','UserOTController');
    Route::resource('fixedSchedule','FixedScheduleController');
    Route::resource('monthlySchedule','MonthlyScheduleController');

    Route::resource('resource','ResourceController');
    Route::resource('formBuilder','FormBuilderController');
    Route::resource('formSubmissions','FormSubmissionsController');
    Route::resource('category','CategoryController');
    Route::resource('approver','UserLeaderController');

    Route::resource('survey','SurveyController');
    Route::resource('employeeEngagement','EngagementController');
    Route::resource('usergallery','GalleryController');
    Route::resource('user_forms','UserFormController');

    Route::resource('task','TaskController');



    Route::get('/logout', 'HomeController@logout');

    Route::get('/health', array(
      'as'=>'page.health',
      'uses'=>'HomeController@health') );
    Route::get('/healthForm', array(
      'as'=>'page.healthForm',
      'uses'=>'HomeController@healthForm') );
    Route::get('/getAllHealthForm', array(
      'as'=>'page.healthForm_getAll',
      'uses'=>'HomeController@healthForm_getAll') );


    Route::get('/healthForm_download', array(
        'as'=> 'page.healthForm_download',
        'uses'=>'HomeController@healthForm_download') );

    Route::post('/healthForm_process', array(
      'as'=>'page.healthForm_process',
      'uses'=>'HomeController@healthForm_process') );
    Route::get('/healthForm_report', array(
      'as'=>'page.healthForm_report',
      'uses'=>'HomeController@healthForm_report') );

    /********** NEW PA   **************/

    Route::resource('performance','NewPA_Form_Controller');
    Route::resource('newPA_evals','NewPA_Evals_Controller');
    Route::resource('newPA_goal','NewPA_Goal_Controller');
    Route::resource('newPA_formGoal','NewPA_Form_Goal_Controller');
    Route::resource('newPA_form_user','NewPA_Form_User_Controller');

    Route::get('/newPA/allForms', array(
      'as'=>'performance.allForms',
      'uses'=>'NewPA_Form_Controller@allForms') );


    Route::get('/newPA/getFormTypeSettings', array(
      'as'=>'performance.getFormTypeSettings',
      'uses'=>'NewPA_Form_Controller@getFormTypeSettings') );

    Route::post('/newPA/process', array(
      'as'=>'performance.process',
      'uses'=>'NewPA_Form_Controller@process') );

    Route::get('/newPA/preview/{id}', array(
      'as'=>'performance.preview',
      'uses'=>'NewPA_Form_Controller@preview') );

    Route::get('/newPA/evaluate/{id}', array(
      'as'=>'performance.evaluate',
      'uses'=>'NewPA_Form_Controller@evaluate') );

    Route::post('/newPA_evals/process', array(
      'as'=>'newPA_evals.process',
      'uses'=>'NewPA_Evals_Controller@process') );



    /********** ------------ END NEW PA ------------  **************/


    //********************** BIR FORMS -----------------------
    Route::get('/allPendingForms', array(
        'as'=> 'user_forms.allPending',
        'uses'=>'UserFormController@allPending') );

    Route::get('/getAllForms', array(
        'as'=> 'user_forms.getAllForms',
        'uses'=>'UserFormController@getAllForms') );

    Route::get('/getAllFormAccess', array(
        'as'=> 'user_forms.getAllFormAccess',
        'uses'=>'UserFormController@getAllFormAccess') );

    Route::get('/auditTrailForms', array(
        'as'=> 'user_forms.auditTrail',
        'uses'=>'UserFormController@auditTrail') );

    Route::get('/userDisqualified', array(
        'as'=> 'user_forms.userTriggered',
        'uses'=>'UserFormController@userTriggered') );


    Route::post('/user_forms/uploadFile', array(
        'as'=> 'user_forms.uploadFile',
        'uses'=>'UserFormController@uploadFile') );

    Route::post('/user_forms/bulkUploadFile', array(
        'as'=> 'user_forms.bulkUploadFile',
        'uses'=>'UserFormController@bulkUploadFile') );


    Route::post('/user_forms/disqualifyForFiling', array(
        'as'=> 'user_forms.disqualifyForFiling',
        'uses'=>'UserFormController@disqualifyForFiling') );


    Route::get('/bulkCreate', array(
        'as'=> 'user_forms.bulkCreate',
        'uses'=>'UserFormController@bulkCreate') );

    Route::post('/user_forms/deleteSignedForm', array(
        'as'=> 'user_forms.deleteSignedForm',
        'uses'=>'UserFormController@deleteSignedForm') );


    Route::get('/downloadUserForm', array(
        'as'=> 'user_forms.downloadUserForm',
        'uses'=>'UserFormController@downloadUserForm') );

    Route::get('/viewUserForm', array(
        'as'=> 'user_forms.viewUserForm',
        'uses'=>'UserFormController@viewUserForm') );



    //********************** NDY TASK TRACKER ***********************

      Route::get('/allTasks', array(
      'as'=>'task.allTasks',
      'uses'=>'TaskController@allTasks') );

      Route::get('/myTasks', array(
      'as'=>'task.myTasks',
      'uses'=>'TaskController@myTasks') );

      Route::get('/downloadTasks', array(
      'as'=>'task.download',
      'uses'=>'TaskController@download') );

      Route::post('/startTask', array(
      'as'=>'task.startTask',
      'uses'=>'TaskController@startTask') );
      Route::post('/startBreak', array(
      'as'=>'task.startBreak',
      'uses'=>'TaskController@startBreak') );
      Route::post('/endBreak', array(
      'as'=>'task.endBreak',
      'uses'=>'TaskController@endBreak') );
      Route::post('/endTask', array(
      'as'=>'task.endTask',
      'uses'=>'TaskController@endTask') );


      Route::get('/logAction/{action}', array(
      'as'=>'page.logAction',
      'uses'=>'HomeController@logAction') );
    //-- END NDY


    /********** GALLERIES   **************/
    Route::get('/gallery', array(
      'as'=>'page.gallery',
      'uses'=>'HomeController@gallery') );

    Route::get('/videogallery', array(
      'as'=>'page.videogallery',
      'uses'=>'HomeController@videogallery') );


    Route::get('/gallery/getImages', array(
      'as'=>'page.getImages',
      'uses'=>'HomeController@getImages') );

    Route::post('/usergallery/upload', array(
      'as'=>'usergallery.upload',
      'uses'=>'GalleryController@upload') );

    Route::get('/usergallery/{id}/contribute', array(
      'as'=>'usergallery.contribute',
      'uses'=>'GalleryController@contribute') );

    Route::get('/getImgUploads', array(
      'as'=>'usergallery.getUploads',
      'uses'=>'GalleryController@getUploads') );

     /********** GALLERIES   **************/




    Route::get('/oampi-resources', array(
      'as'=> 'resource.index',
      'uses'=>'ResourceController@index') );

    Route::get('/oampi-waiver', array(
      'as'=> 'resource.waiver',
      'uses'=>'ResourceController@waiver') );

    Route::post('/oampi-resources/viewItem', array(
      'as'=> 'resource.viewItem',
      'uses'=>'ResourceController@viewItem') );

    Route::get('/oampi-resources/viewFile/{id}', array(
      'as'=> 'resource.viewFile',
      'uses'=>'ResourceController@viewFile') );

    Route::get('/oampi-resources/item/{id}', array(
      'as'=> 'resource.item',
      'uses'=>'ResourceController@item') );

    Route::get('/oampi-resources/track/{id}', array(
      'as'=> 'resource.track',
      'uses'=>'ResourceController@track') );

    Route::get('/oampi-resources/download/{id}', array(
      'as'=> 'resource.download',
      'uses'=>'ResourceController@download') );


    /********** emp ENGAGEMENT  **************/

      Route::post('/employeeEngagement/disqualify', array(
      'as'=> 'employeeEngagement.disqualify',
      'uses'=>'EngagementController@disqualify') );

      Route::post('/employeeEngagement/report', array(
      'as'=> 'employeeEngagement.report',
      'uses'=>'EngagementController@reportEntry') );

      Route::post('/employeeEngagement/next', array(
      'as'=> 'employeeEngagement.next',
      'uses'=>'EngagementController@next') );

      Route::post('/employeeEngagement/prev', array(
      'as'=> 'employeeEngagement.prev',
      'uses'=>'EngagementController@prev') );

      Route::get('/employeeEngagement/{id}/wall', array(
      'as'=> 'employeeEngagement.wall',
      'uses'=>'EngagementController@wall') );

      Route::get('/employeeEngagement/{id}/voteNow', array(
      'as'=> 'employeeEngagement.voteNow',
      'uses'=>'EngagementController@voteNow') );

      Route::post('/employeeEngagement/cancelEntry/{id}', array(
      'as'=> 'employeeEngagement.cancelEntry',
      'uses'=>'EngagementController@cancelEntry') );

      Route::post('/employeeEngagement/castvote/{id}', array(
      'as'=> 'employeeEngagement.castvote',
      'uses'=>'EngagementController@castvote') );

      Route::post('/employeeEngagement/deleteComment/{id}', array(
      'as'=> 'employeeEngagement.deleteComment',
      'uses'=>'EngagementController@deleteComment') );

      Route::post('/employeeEngagement/deleteEntryComment/', array(
      'as'=> 'employeeEngagement.deleteEntryComment',
      'uses'=>'EngagementController@deleteEntryComment') );

      Route::post('/employeeEngagement/deleteReply/{id}', array(
      'as'=> 'employeeEngagement.deleteReply',
      'uses'=>'EngagementController@deleteReply') );

      Route::post('/employeeEngagement/deletePost/{id}', array(
      'as'=> 'employeeEngagement.deletePost',
      'uses'=>'EngagementController@deletePost') );


      Route::post('/employeeEngagement/like/', array(
      'as'=> 'employeeEngagement.like',
      'uses'=>'EngagementController@like') );
      Route::post('/employeeEngagement/unlike/', array(
      'as'=> 'employeeEngagement.unlike',
      'uses'=>'EngagementController@unlike') );

      Route::post('/employeeEngagement/postEntryComment', array(
      'as'=> 'employeeEngagement.postEntryComment',
      'uses'=>'EngagementController@postEntryComment') );

      Route::post('/employeeEngagement/postComment/{id}', array(
      'as'=> 'employeeEngagement.postComment',
      'uses'=>'EngagementController@postComment') );

      Route::post('/employeeEngagement/postReply/{id}', array(
      'as'=> 'employeeEngagement.postReply',
      'uses'=>'EngagementController@postReply') );

      Route::post('/employeeEngagement/saveEntry', array(
      'as'=> 'employeeEngagement.saveEntry',
      'uses'=>'EngagementController@saveEntry') );

      Route::post('/employeeEngagement/saveEntry2', array(
      'as'=> 'employeeEngagement.saveEntry2',
      'uses'=>'EngagementController@saveEntry2') );

      Route::post('/employeeEngagement/saveTriggers', array(
      'as'=> 'employeeEngagement.saveTriggers',
      'uses'=>'EngagementController@saveTriggers') );


      Route::get('/employeeEngagement/{id}/tallyVotes', array(
      'as'=> 'employeeEngagement.tallyVotes',
      'uses'=>'EngagementController@tallyVotes') );


      Route::post('/employeeEngagement/uncastvote/{id}', array(
      'as'=> 'employeeEngagement.uncastvote',
      'uses'=>'EngagementController@uncastvote') );


      Route::post('/employeeEngagement/updateEntry', array(
      'as'=> 'employeeEngagement.updateEntry',
      'uses'=>'EngagementController@updateEntry') );

    //-- END EMPLOYEE ENGAGEMENTS

    /********** FORM BUILDER **************/

      Route::get('/widgets', array(
      'as'=> 'formSubmissions.widgets',
      'uses'=>'FormSubmissionsController@widgets') );

      Route::post('/widgets/updateStatus/{id}', array(
      'as'=> 'formSubmissions.updateStatus',
      'uses'=>'FormSubmissionsController@updateStatus') );


      Route::post('/formSubmissions/process', array(
      'as'=> 'formSubmissions.process',
      'uses'=>'FormSubmissionsController@process') );

      Route::post('/formSubmissions/deleteDupes', array(
      'as'=> 'formSubmissions.deleteDupes',
      'uses'=>'FormSubmissionsController@deleteDupes') );

      Route::post('/formSubmissions/deleteThis/{id}', array(
      'as'=> 'formSubmissions.deleteThis',
      'uses'=>'FormSubmissionsController@deleteThis') );


      Route::post('/formSubmissions/downloadCSV/{id}', array(
      'as'=> 'formSubmissions.downloadCSV',
      'uses'=>'FormSubmissionsController@downloadCSV') );

      Route::get('/formSubmissions/fetchFrom/{id}', array(
      'as'=> 'formSubmissions.fetchFrom',
      'uses'=>'FormSubmissionsController@fetchFrom') );

      Route::get('/formSubmissions/fetchRanking/{id}', array(
      'as'=> 'formSubmissions.fetchRanking',
      'uses'=>'FormSubmissionsController@fetchRanking') );

      Route::get('/formSubmissions/getRanking/{type}', array(
      'as'=> 'formSubmissions.getRanking',
      'uses'=>'FormSubmissionsController@getRanking') );

      Route::post('/formSubmissions/getAll/{id}', array(
      'as'=> 'formSubmissions.getAll',
      'uses'=>'FormSubmissionsController@getAll') );

      Route::get('/formSubmissions/getEscalations/{id}', array(
      'as'=> 'formSubmissions.getEscalations',
      'uses'=>'FormSubmissionsController@getEscalations') );

      Route::get('/formSubmissions/getOrderStatus/{id}', array(
      'as'=> 'formSubmissions.getOrderStatus',
      'uses'=>'FormSubmissionsController@getOrderStatus') );


      Route::get('/formSubmissions/rawData/{id}', array(
      'as'=> 'formSubmissions.rawData',
      'uses'=>'FormSubmissionsController@rawData') );



      Route::post('/formSubmissions/uploadCSV', array(
      'as'=> 'formSubmissions.uploadCSV',
      'uses'=>'FormSubmissionsController@uploadCSV') );

    /********** FORM BUILDER **************/



    Route::get('/module', array(
      'as'=> 'page.module',
      'uses'=>'HomeController@module') );

    //----- WFH and LOGS --------

      Route::post('/saveBioLog', array(
      'as'=> 'logs.saveBioLog',
      'uses'=>'LogsController@saveBioLog') );

      Route::post('/saveDailyUserLogs', array(
        'as'=> 'logs.saveDailyUserLogs',
        'uses'=>'LogsController@saveDailyUserLogs') );

      Route::post('/deleteBio/{id}', array(
        'as'=> 'logs.deleteBio',
        'uses'=>'LogsController@deleteBio') );


      Route::get('/view-raw-biometrics-data/{id}', array(
        'as'=> 'logs.saveDailyUserLogs',
        'uses'=>'LogsController@viewRawBiometricsData') );

      Route::post('/saveDashboardLog', array(
        'as'=> 'logs.saveDashboardLog',
        'uses'=>'LogsController@saveDashboardLog') );

       Route::get('/getAllLogs', array(
        'as'=> 'logs.getAllLogs',
        'uses'=>'LogsController@getAllLogs') );

       Route::get('/allLogs', array(
        'as'=> 'logs.allLogs',
        'uses'=>'LogsController@allLogs') );

       Route::get('/track_EMSaccess', array(
        'as'=> 'logs.emsAccess',
        'uses'=>'LogsController@emsAccess') );

        Route::get('/getAllEMSaccess', array(
        'as'=> 'logs.getAllEMSaccess',
        'uses'=>'LogsController@getAllEMSaccess') );

      Route::get('/allLogs_download', array(
        'as'=> 'logs.allLogs_download',
        'uses'=>'LogsController@allLogs_download') );

      Route::get('/emsAccess_download', array(
        'as'=> 'logs.emsAccess_download',
        'uses'=>'LogsController@emsAccess_download') );

       Route::get('/getWFH', array(
        'as'=> 'logs.getWFH',
        'uses'=>'LogsController@getWFH') );

      Route::get('/wfh', array(
        'as'=> 'logs.wfh',
        'uses'=>'LogsController@wfh') );

      Route::get('/wfh_download', array(
        'as'=> 'logs.wfh_download',
        'uses'=>'LogsController@wfh_download') );


    //---- end WFH AND LOGS


    //---- LEAVE MANAGEMENT -------------
      Route::get('/leave_management', array(
      'as'=> 'leaveMgt',
      'uses'=>'UserController@leaveMgt') );

      Route::post('/leave_management_download', array(
      'as'=> 'leave_management_download',
      'uses'=>'UserController@leaveMgt_dl') );

      Route::get('/leave_management_earnings', array(
      'as'=> 'leaveMgt_earnings',
      'uses'=>'UserController@leaveMgt_earnings') );

       Route::get('/leave_management_summary', array(
      'as'=> 'leaveMgt_summary',
      'uses'=>'UserController@leaveMgt_summary') );


     //---- SCHED MANAGEMENT -------------
      Route::get('/schedule_management', array(
      'as'=> 'schedMgt',
      'uses'=>'UserController@schedMgt') );



    //--- NOTIFS
      Route::post('/user_cws/process', array(
      'as'=> 'user_cws.process',
      'uses'=>'UserCWSController@process') );

      Route::get('/deleteAllNotifs', array(
      'as'=> 'user_notification.deleteAll',
      'uses'=>'UserNotificationController@deleteAll') );


      Route::post('/user_notification/deleteRequest/{id}', array(
      'as'=> 'user_notification.deleteRequest',
      'uses'=>'UserNotificationController@deleteRequest') );

      Route::post('/user_notification/deleteNotif', array(
      'as'=> 'user_notification.deleteNotif',
      'uses'=>'UserNotificationController@deleteNotif') );

      Route::get('/user_notification/getApprovalNotifications/{id}', array(
      'as'=> 'user_notification.getApprovalNotifications',
      'uses'=>'UserNotificationController@getApprovalNotifications') );
    // -- END NOTIFS


    /*********** DTRP  ROUTES ************/

      Route::get('new_DTRP',array(
        'as'=> 'user_dtr.newDTRP',
        'uses'=>'UserDTRPController@newDTRP'
      ));

      Route::get('DTRP_management',array(
        'as'=> 'user_dtr.manage',
        'uses'=>'UserDTRPController@manage'
      ));

      Route::post('process_DTRP',array(
        'as'=> 'user_dtr.newDTRP_process',
        'uses'=>'UserDTRPController@newDTRP_process'
      ));

      Route::post('validate_DTRP',array(
        'as'=> 'user_dtr.newDTRP_validate',
        'uses'=>'UserDTRPController@newDTRP_validate'
      ));

      Route::post('/user_dtr/manage', array(
      'as'=> 'user_dtr.manage',
      'uses'=>'DTRController@manage') );

      Route::post('/user_dtrp/process', array(
      'as'=> 'user_dtrp.process',
      'uses'=>'UserDTRPController@process') );

      Route::post('/user_dtrp/deleteThisDTRP/{id}', array(
      'as'=> 'user_dtrp.deleteThisDTRP',
      'uses'=>'UserDTRPController@deleteThisDTRP') );


      Route::post('/user_ot/process', array(
      'as'=> 'user_ot.process',
      'uses'=>'UserOTController@process') );

      Route::post('/user_ot/deleteOT/{id}', array(
      'as'=> 'user_ot.deleteOT',
      'uses'=>'UserOTController@deleteOT') );

      Route::post('/user_notification/process', array(
      'as'=> 'user_notification.process',
      'uses'=>'UserNotificationController@process') );
    //--- END DTRP


    /*********** CWS **********/

      Route::post('/user_cws/deleteCWS', array(
      'as'=> 'user_cws.deleteCWS',
      'uses'=>'UserCWSController@deleteCWS') );

      Route::post('/user_cws/deleteThisCWS/{id}', array(
      'as'=> 'user_cws.deleteThisCWS',
      'uses'=>'UserCWSController@deleteThisCWS') );

      Route::post('/user_cws/requestCWS', array(
      'as'=> 'user_cws.requestCWS',
      'uses'=>'UserCWSController@requestCWS') );

    /*********** CWS **********/



      /*********** VL **********/

        Route::post('/user_vl/requestVL', array(
        'as'=> 'user_vl.requestVL',
        'uses'=>'UserVLController@requestVL') );

        Route::post('/user_vl/getCredits', array(
        'as'=> 'user_vl.getCredits',
        'uses'=>'UserVLController@getCredits') );

        Route::post('/user_vl/addCredits', array(
        'as'=> 'user_vl.addCredits',
        'uses'=>'UserVLController@addCredits') );

        Route::post('/user_vl/addVLearnings', array(
        'as'=> 'user_vl.addVLearnings',
        'uses'=>'UserVLController@addVLearnings') );


        Route::post('/user_vl/addVLupdate', array(
        'as'=> 'user_vl.addVLupdate',
        'uses'=>'UserVLController@addVLupdate') );

        Route::post('/user_vl/checkExisting', array(
        'as'=> 'user_vl.checkExisting',
        'uses'=>'UserVLController@checkExisting') );


         Route::post('/user_vl/deleteCredit/{id}', array(
        'as'=> 'user_vl.deleteCredit',
        'uses'=>'UserVLController@deleteCredit') );

        Route::post('/user_vl/deleteEarning/{id}', array(
        'as'=> 'user_vl.deleteVLearning',
        'uses'=>'UserVLController@deleteVLearning') );

         Route::post('/user_vl/editCredits/{id}', array(
        'as'=> 'user_vl.editCredits',
        'uses'=>'UserVLController@editCredits') );

         Route::get('/user_vl/showCredits/{id}', array(
        'as'=> 'user_vl.showCredits',
        'uses'=>'UserVLController@showCredits') );

        Route::post('/user_vl/deleteThisVL/{id}', array(
        'as'=> 'user_vl.deleteThisVL',
        'uses'=>'UserVLController@deleteThisVL') );

        Route::post('/user_vl/process', array(
        'as'=> 'user_vl.process',
        'uses'=>'UserVLController@process') );

        Route::post('/user_vl/uploadCredits', array(
        'as'=> 'user_vl.uploadCredits',
        'uses'=>'UserVLController@uploadCredits') );

        Route::get('/updateVLCredits', array(
        'as'=> 'user_vl.updateCredits',
        'uses'=>'UserVLController@updateCredits') );
       /*********** VL **********/



      /*********** VTO **********/

        Route::get('/VTO/new', array(
        'as'=> 'user_vl.VTO_new',
        'uses'=>'UserVLController@VTO_new') );

        Route::post('/user_vl/deleteThisVTO/{id}', array(
        'as'=> 'user_vl.deleteThisVTO',
        'uses'=>'UserVLController@deleteThisVTO') );

        Route::get('/showVTO/{id}', array(
        'as'=> 'user_vl.showVTO',
        'uses'=>'UserVLController@showVTO') );

        Route::post('/VTO/checkExisting', array(
        'as'=> 'user_vl.checkExisting_VTO',
        'uses'=>'UserVLController@checkExisting_VTO') );

        Route::post('/VTO/process', array(
        'as'=> 'user_vl.processVTO',
        'uses'=>'UserVLController@processVTO') );

        Route::post('/VTO/requestVTO', array(
        'as'=> 'user_vl.requestVTO',
        'uses'=>'UserVLController@requestVTO') );

      /*********** VTO **********/


      /*********** SL **********/

        Route::post('/user_sl/editCredits/{id}', array(
        'as'=> 'user_sl.editCredits',
        'uses'=>'UserSLController@editCredits') );


        Route::post('/user_sl/addCredits', array(
        'as'=> 'user_sl.addCredits',
        'uses'=>'UserSLController@addCredits') );

        Route::post('/user_sl/addSLupdate', array(
        'as'=> 'user_sl.addSLupdate',
        'uses'=>'UserSLController@addSLupdate') );

         Route::post('/user_sl/addSLearnings', array(
        'as'=> 'user_sl.addSLearnings',
        'uses'=>'UserSLController@addSLearnings') );

        Route::post('/user_sl/checkExisting', array(
        'as'=> 'user_sl.checkExisting',
        'uses'=>'UserSLController@checkExisting') );

        Route::post('/user_sl/deleteAdvSL/{id}', array(
        'as'=> 'user_sl.deleteAdvSL',
        'uses'=>'UserSLController@deleteAdvSL') );

        Route::post('/user_sl/deleteCredit/{id}', array(
        'as'=> 'user_sl.deleteCredit',
        'uses'=>'UserSLController@deleteCredit') );

        Route::post('/user_sl/deleteEarning/{id}', array(
        'as'=> 'user_sl.deleteSLearning',
        'uses'=>'UserSLController@deleteSLearning') );

        Route::post('/user_sl/requestSL', array(
        'as'=> 'user_sl.requestSL',
        'uses'=>'UserSLController@requestSL') );

        Route::post('/user_sl/getCredits', array(
        'as'=> 'user_sl.getCredits',
        'uses'=>'UserSLController@getCredits') );

        Route::get('/user_sl/showCredits/{id}', array(
        'as'=> 'user_sl.showCredits',
        'uses'=>'UserSLController@showCredits') );

        Route::post('/user_sl/deleteThisSL/{id}', array(
        'as'=> 'user_sl.deleteThisVL',
        'uses'=>'UserSLController@deleteThisSL') );

        Route::post('/user_sl/process', array(
        'as'=> 'user_sl.process',
        'uses'=>'UserSLController@process') );

        Route::get('/updateSLCredits', array(
        'as'=> 'user_sl.updateCredits',
        'uses'=>'UserSLController@updateCredits') );

        Route::post('/user_sl/uploadCredits', array(
        'as'=> 'user_sl.uploadCredits',
        'uses'=>'UserSLController@uploadCredits') );

        Route::get('/user_sl/medCert/{id}', array(
        'as'=> 'user_sl.item',
        'uses'=>'UserSLController@item') );

      //---- end SL



      /*********** LWOP **********/

        Route::post('/user_lwop/checkExisting', array(
        'as'=> 'user_lwop.checkExisting',
        'uses'=>'UserLWOPController@checkExisting') );

         Route::post('/user_lwop/getCredits', array(
        'as'=> 'user_lwop.getCredits',
        'uses'=>'UserLWOPController@getCredits') );

         Route::post('/user_lwop/requestLWOP', array(
        'as'=> 'user_lwop.requestLWOP',
        'uses'=>'UserLWOPController@requestLWOP') );

         Route::post('/user_lwop/deleteThisLWOP/{id}', array(
        'as'=> 'user_lwop.deleteThisLWOP',
        'uses'=>'UserLWOPController@deleteThisLWOP') );

        Route::post('/user_lwop/process', array(
        'as'=> 'user_lwop.process',
        'uses'=>'UserLWOPController@process') );
      //-- END LWOP




      /*********** OT **********/
        Route::post('/user_ot/requestPSOT', array(
        'as'=> 'user_ot.requestPSOT',
        'uses'=>'UserOTController@requestPSOT') );

        Route::get('/getPSOTworkedhours/{id}', array(
        'as'=> 'user_ot.getPSOTworkedhours',
        'uses'=>'UserOTController@getPSOTworkedhours') );

        Route::get('/getPSOTLogsForThisDate/{id}', array(
        'as'=> 'user_ot.getPSOTLogsForThisDate',
        'uses'=>'UserOTController@getPSOTLogsForThisDate') );
      //--- END OT


      /*********** OBT **********/

        Route::post('/user_obt/checkExisting', array(
        'as'=> 'user_obt.checkExisting',
        'uses'=>'UserOBTController@checkExisting') );

        Route::post('/user_obt/requestVL', array(
        'as'=> 'user_obt.requestVL',
        'uses'=>'UserOBTController@requestOBT') );

        Route::post('/user_obt/getCredits', array(
        'as'=> 'user_obt.getCredits',
        'uses'=>'UserOBTController@getCredits') );

        Route::post('/user_obt/addCredits', array(
        'as'=> 'user_obt.addCredits',
        'uses'=>'UserOBTController@addCredits') );


         Route::post('/user_obt/deleteCredit/{id}', array(
        'as'=> 'user_obt.deleteCredit',
        'uses'=>'UserOBTController@deleteCredit') );

         Route::post('/user_obt/editCredits/{id}', array(
        'as'=> 'user_obt.editCredits',
        'uses'=>'UserOBTController@editCredits') );

         Route::get('/user_obt/showCredits/{id}', array(
        'as'=> 'user_obt.showCredits',
        'uses'=>'UserOBTController@showCredits') );

          Route::post('/user_obt/deleteThisOBT/{id}', array(
        'as'=> 'user_obt.deleteThisOBT',
        'uses'=>'UserOBTController@deleteThisOBT') );

        Route::post('/user_obt/process', array(
        'as'=> 'user_obt.process',
        'uses'=>'UserOBTController@process') );
      //--- END OBT


      /*********** FAMILY LEAVES **********/

        Route::post('/user_fl/editCredits/{id}', array(
        'as'=> 'user_fl.editCredits',
        'uses'=>'UserFamilyleaveController@editCredits') );


        Route::post('/user_fl/addCredits', array(
        'as'=> 'user_fl.addCredits',
        'uses'=>'UserFamilyleaveController@addCredits') );

        Route::post('/user_fl/checkExisting', array(
        'as'=> 'user_fl.checkExisting',
        'uses'=>'UserFamilyleaveController@checkExisting') );

        Route::post('/user_fl/deleteCredit/{id}', array(
        'as'=> 'user_fl.deleteCredit',
        'uses'=>'UserFamilyleaveController@deleteCredit') );

        Route::post('/user_fl/requestFL', array(
        'as'=> 'user_fl.requestFL',
        'uses'=>'UserFamilyleaveController@requestFL') );

        Route::post('/user_fl/getCredits', array(
        'as'=> 'user_fl.getCredits',
        'uses'=>'UserFamilyleaveController@getCredits') );

        Route::get('/user_fl/showCredits/{id}', array(
        'as'=> 'user_fl.showCredits',
        'uses'=>'UserFamilyleaveController@showCredits') );

        Route::post('/user_fl/deleteThisSL/{id}', array(
        'as'=> 'user_fl.deleteThisVL',
        'uses'=>'UserFamilyleaveController@deleteThisSL') );

        Route::post('/user_fl/process', array(
        'as'=> 'user_fl.process',
        'uses'=>'UserFamilyleaveController@process') );

        Route::post('/user_fl/uploadCredits', array(
        'as'=> 'user_fl.uploadCredits',
        'uses'=>'UserFamilyleaveController@uploadCredits') );

        Route::get('/user_fl/requirements/{id}', array(
        'as'=> 'user_fl.item',
        'uses'=>'UserFamilyleaveController@item') );
      //--- END FAMILY



    /*********** DTR ROUTES ************/

      Route::post('/doUnlock/{id}', array(
      'as'=> 'user_dtr.unlock',
      'uses'=>'DTRController@unlock') );

      Route::post('/downloadDTRsheet', array(
      'as'=> 'user_dtr.downloadDTRsheet',
      'uses'=>'DTRController@downloadDTRsheet') );

      Route::post('/downloadDTRLockReport', array(
      'as'=> 'user_dtr.downloadDTRLockReport',
      'uses'=>'DTRController@downloadDTRLockReport') );

      Route::post('/downloadLeaveSummary', array(
      'as'=> 'user_dtr.downloadLeaveSummary',
      'uses'=>'DTRController@downloadLeaveSummary') );

      Route::post('/dtrSheet/{id}', array(
      'as'=> 'user_dtr.processSheet',
      'uses'=>'DTRController@processSheet') );

      Route::get('/financeReports', array(
      'as'=> 'financeReports',
      'uses'=>'DTRController@financeReports') );

      Route::get('/finance_JPS', array(
      'as'=> 'finance_JPS',
      'uses'=>'DTRController@finance_JPS') );


      Route::post('/finance_getJPS', array(
      'as'=> 'user_dtr.finance_getJPS',
      'uses'=>'DTRController@finance_getJPS') );

      Route::post('/finance_dlJPS', array(
      'as'=> 'user_dtr.finance_dlJPS',
      'uses'=>'DTRController@finance_dlJPS') );


      Route::get('/wfm_DTRsummary', array(
      'as'=> 'wfm_DTRsummary',
      'uses'=>'DTRController@wfm_DTRsummary') );


      Route::get('/myDTR', array(
      'as'=> 'user_dtr.myDTR',
      'uses'=>'DTRController@myDTR') );

      Route::post('/overrideRD/{id}', array(
      'as'=> 'user_dtr.overrideRD',
      'uses'=>'DTRController@overrideRD') );

      Route::post('/plotSchedule', array(
      'as'=> 'monthlySchedule.plot',
      'uses'=>'MonthlyScheduleController@plot') );

      Route::get('/seen-unlockRequest/{id}', array(
      'as'=> 'user_dtr.seenzoned',
      'uses'=>'DTRController@seenzoned') );

      Route::get('/seen-unlockPDRequest/{id}', array(
      'as'=> 'user_dtr.seenzonedPD',
      'uses'=>'DTRController@seenzonedPD') );

      Route::post('/unlock/{id}', array(
      'as'=> 'user_dtr.requestUnlock',
      'uses'=>'DTRController@requestUnlock') );

      Route::post('/unlockByApprover/{id}', array(
      'as'=> 'user_dtr.unlockByApprover',
      'uses'=>'DTRController@unlockByApprover') );


      Route::post('/userPreshift/{id}', array(
      'as'=> 'user_dtr.usePreshift',
      'uses'=>'DTRController@usePreshift') );

      Route::post('/disableUserPreshift/{id}', array(
      'as'=> 'user_dtr.disablePreshift',
      'uses'=>'DTRController@disablePreshift') );

      Route::get('/sheets', array(
      'as'=> 'user_dtr.dtrSheets',
      'uses'=>'DTRController@dtrSheets') );

      Route::get('/DTRlockReport', array(
      'as'=> 'user_dtr.lockReport',
      'uses'=>'DTRController@lockReport') );

      Route::post('/getAllNotLocked', array(
      'as'=> 'user_dtr.getAllNotLocked',
      'uses'=>'DTRController@getAllNotLocked') );

      

      Route::post('/sheets/getValidated', array(
      'as'=> 'user_dtr.getValidatedDTRs',
      'uses'=>'DTRController@getValidatedDTRs') );


      Route::post('/zendesk', array(
      'as'=> 'user_dtr.zendesk',
      'uses'=>'DTRController@zendesk') );
    // --------- end DTR



     /*********** BIOMETRICS ROUTES ************/

        Route::post('/tempUpload/purge', array(
        'as'=> 'tempUpload.purge',
        'uses'=>'TempUploadController@purge') );

        Route::post('/tempUpload/purgeThis', array(
        'as'=> 'tempUpload.purgeThis',
        'uses'=>'TempUploadController@purgeThis') );

        Route::post('/biometrics/upload', array(
        'as'=> 'biometrics.upload',
        'uses'=>'BiometricsController@upload') );

        Route::post('/biometrics/uploadFinanceCSV', array(
        'as'=> 'biometrics.uploadFinanceCSV',
        'uses'=>'BiometricsController@uploadFinanceCSV') );

        Route::post('/biometrics/uploadECQ', array(
        'as'=> 'biometrics.uploadECQ',
        'uses'=>'BiometricsController@uploadECQ') );

        Route::get('/ecq_upload', array(
        'as'=> 'biometrics.ecq_upload',
        'uses'=>'BiometricsController@ecq_upload') );


        Route::post('/ecq_update', array(
        'as'=> 'dtr.updateECQ',
        'uses'=>'DTRController@updateECQ') );


        Route::post('/biometrics/uploadSched', array(
        'as'=> 'biometrics.uploadSched',
        'uses'=>'BiometricsController@uploadSched') );

        Route::get('/workSched_upload', array(
        'as'=> 'biometrics.workSched_upload',
        'uses'=>'BiometricsController@workSched_upload') );

        Route::post('/dtrSheet/upload', array(
        'as'=> 'biometrics.uploadDTR',
        'uses'=>'BiometricsController@uploadDTR') );

        Route::get('/setupBiometricUserLogs', array(
        'as'=> 'biometrics.setupBiometricUserLogs',
        'uses'=>'BiometricsController@setupBiometricUserLogs') );

    //--- END BIOMETRICS






    /*********** EVAL FORM ROUTES ************/

      Route::get('/pendingEvals', array(
      'as'=> 'allPendings',
      'uses'=>'EvalFormController@allPendings') );

      Route::get('/approvedEvals', array(
      'as'=> 'allApproved',
      'uses'=>'EvalFormController@allApproved') );

      Route::get('/deniedEvals', array(
      'as'=> 'allDenied',
      'uses'=>'EvalFormController@allDenied') );

      Route::get('/evalForm/new/{user_id}/{evalType_id}', array(
      'as'=> 'newEvaluation',
      'uses'=>'EvalFormController@newEvaluation') );

      Route::get('/evalForm/newAnnual/{user_id}/{evalType_id}', array(
      'as'=> 'newAnnualEvaluation',
      'uses'=>'EvalFormController@newAnnualEvaluation') );

      Route::get('/evalForm/newContractual/{user_id}/{evalType_id}', array(
      'as'=> 'newContractual',
      'uses'=>'EvalFormController@newContractual') );

      Route::post('/evalForm/grabAllWhosUpFor', array(
      'as'=> 'evalForm.grabAllWhosUpFor',
      'uses'=>'EvalFormController@grabAllWhosUpFor') );


      Route::post('/evalForm/approveThisEval/{id}', array(
      'as'=> 'evalForm.approveThisEval',
      'uses'=>'EvalFormController@approveThisEval') );

      Route::post('/evalForm/rejectThisEval/{id}', array(
      'as'=> 'evalForm.rejectThisEval',
      'uses'=>'EvalFormController@rejectThisEval') );


      Route::post('/evalForm/deleteThisEval/{id}', array(
      'as'=> 'evalForm.deleteThisEval',
      'uses'=>'EvalFormController@deleteThisEval') );

      Route::get('/evalForm/review/{id}', array(
      'as'=> 'evalForm.review',
      'uses'=>'EvalFormController@review') );

      Route::get('/getAllEval', array(
      'as'=> 'evalForm.getAllEval',
      'uses'=>'EvalFormController@getAllEval') );

      Route::get('/getAllApprovedEvals', array(
      'as'=> 'evalForm.getAllApprovedEvals',
      'uses'=>'EvalFormController@getAllApprovedEvals') );

      Route::get('/getAllDeniedEvals', array(
      'as'=> 'evalForm.getAllDeniedEvals',
      'uses'=>'EvalFormController@getAllDeniedEvals') );


      Route::get('/getAllPendingEvals', array(
      'as'=> 'evalForm.getAllPendingEvals',
      'uses'=>'EvalFormController@getAllPendingEvals') );

      Route::post('/evalForm/updateReview/{id}', array(
      'as'=> 'evalForm.updateReview',
      'uses'=>'EvalFormController@updateReview') );

      Route::post('/evalForm/updatePeriod', array(
      'as'=> 'evalForm.updatePeriod',
      'uses'=>'EvalFormController@updatePeriod') );


      Route::get('/download', array(
      'as'=> 'downloadReport',
      'uses'=>'EvalFormController@downloadReport') );

      Route::get('/downloadAllUsers', array(
      'as'=> 'downloadAllUsers',
      'uses'=>'UserController@downloadAllUsers') );



      Route::get('/evalForm/blank/{id}', array(
      'as'=> 'printBlankEval',
      'uses'=>'EvalFormController@printBlankEval') );

      Route::get('/evalForm/blankEmployee/{id}', array(
      'as'=> 'printBlankEmployee',
      'uses'=>'EvalFormController@printBlankEmployee') );



      Route::get('/evalForm/print/{id}', array(
      'as'=> 'printEval',
      'uses'=>'EvalFormController@printEval') );

    //--- END EVAL FORMS


    Route::get('/immediateHead/{id}/members', array(
          'as'=> 'getMembers',
          'uses'=>'ImmediateHeadController@getMembers') );

    Route::get('/campaign/{id}/leaders', array(
          'as'=> 'getAllLeaders',
          'uses'=>'CampaignController@getAllLeaders') );

    Route::post('/immediateHeadCampaign/disable/{id}', array(
      'as'=> 'immediateHeadCampaign.disable',
      'uses'=>'ImmediateHeadCampaignController@disable') );

    Route::post('/immediateHeadCampaign/editTier/{id}', array(
      'as'=> 'immediateHeadCampaign.editTier',
      'uses'=>'ImmediateHeadCampaignController@editTier') );



     Route::get('/getOtherTeams', array(
          'as'=> 'getOtherTeams',
          'uses'=>'ImmediateHeadController@getOtherTeams') );

    Route::get('/getAllCampaigns', array(
          'as'=> 'getAllCampaigns',
          'uses'=>'CampaignController@getAllCampaigns') );

    Route::get('/orgChart', array(
          'as'=> 'orgChart',
          'uses'=>'CampaignController@orgChart') );

     Route::post('/updateMovement', array(
          'as'=> 'updateMovement',
          'uses'=>'MovementController@updateMovement') );


    Route::get('/movement/createNew/{id}', array(
          'as'=> 'createNew',
          'uses'=>'MovementController@createNew') );

    Route::post('/movement/approve/{id}', array(
          'as'=> 'movement.approve',
          'uses'=>'MovementController@approve') );

     Route::post('/movement/noted/{id}', array(
          'as'=> 'noted',
          'uses'=>'MovementController@noted') );

      Route::get('/movement/printPDF/{id}', array(
      'as'=> 'printPDF',
      'uses'=>'MovementController@printPDF') );



    Route::get('/getAllMovements', array(
          'as'=> 'getAllMovements',
          'uses'=>'MovementController@getAllMovements') );


     Route::get('/movement/changePersonnel/{id}', array(
          'as'=> 'changePersonnel',
          'uses'=>'MovementController@changePersonnel') );


     Route::post('/movement/findInstances', array(
          'as'=> 'findInstances',
          'uses'=>'MovementController@findInstances') );


     Route::post('/moveToTeam', array(
          'as'=> 'toTeam',
          'uses'=>'MovementController@toTeam') );

     Route::post('/employeeMovement', array(
          'as'=> 'moveToTeam',
          'uses'=>'UserController@moveToTeam') );

     Route::get('/user/{id}/createSchedule/', array(
          'as'=> 'createSchedule',
          'uses'=>'UserController@createSchedule') );


     Route::get('/user/{id}/editShifts/', array(
          'as'=> 'editShifts',
          'uses'=>'UserController@editShifts') );

     Route::post('/user/deleteThisUser/{id}', array(
          'as'=> 'user.deleteThisUser',
          'uses'=>'UserController@deleteThisUser') );

     Route::post('/user/updateBday/{id}', array(
          'as'=> 'user.updateBday',
          'uses'=>'UserController@updateBday') );


     Route::post('/user/grantAccess', array(
          'as'=> 'user.grantAccess',
          'uses'=>'UserController@grantAccess') );

     Route::post('/user/claimedcard', array(
          'as'=> 'user.claimedcard',
          'uses'=>'UserController@claimedcard') );



     Route::get('/immediateHead/{id}/members', array(
          'as'=> 'getMembers',
          'uses'=>'ImmediateHeadController@getMembers') );

     Route::get('/getAllUsers', array(
      'as'=> 'getAllUsers',
      'uses'=>'UserController@getAllUsers') );

     Route::get('/getAllActiveUsers', array(
      'as'=> 'getAllActiveUsers',
      'uses'=>'UserController@getAllActiveUsers') );

     Route::get('/getAllInactiveUsers', array(
      'as'=> 'getAllInactiveUsers',
      'uses'=>'UserController@getAllInactiveUsers') );

     Route::get('/getAllFloatingUsers', array(
      'as'=> 'getAllFloatingUsers',
      'uses'=>'UserController@getAllFloatingUsers') );


     Route::get('/getAllPendingBdays', array(
      'as'=> 'getAllPendingBdays',
      'uses'=>'UserController@getAllPendingBdays') );

     Route::get('/birthdayCelebrators', array(
      'as'=> 'birthdayCelebrators',
      'uses'=>'UserController@birthdayCelebrators') );

     Route::get('/workAnnivCelebrators', array(
      'as'=> 'workAnnivCelebrators',
      'uses'=>'UserController@workAnnivCelebrators') );


     Route::post('/enableWFH', array(
      'as'=> 'enableWFH',
      'uses'=>'UserController@wfh') );



     Route::get('/listAllActive', array(
      'as'=> 'listAllActive',
      'uses'=>'UserController@listAllActive') );


      Route::get('/inactives', array(
      'as'=> 'inactives',
      'uses'=>'UserController@index_inactive') );

      Route::get('/floating', array(
      'as'=> 'floating',
      'uses'=>'UserController@index_floating') );

      Route::get('/pendings', array(
      'as'=> 'pendings',
      'uses'=>'UserController@index_pendings') );


     Route::get('/editUser/{id}', array(
      'as'=> 'editUser',
      'uses'=>'UserController@editUser') );

     Route::get('/editSchedule/{id}', array(
      'as'=> 'editSchedule',
      'uses'=>'UserController@editSchedule') );

      Route::get('/editContact/{id}', array(
      'as'=> 'editContact',
      'uses'=>'UserController@editContact') );

       Route::post('/updateContact/{id}', array(
      'as'=> 'updateContact',
      'uses'=>'UserController@updateContact') );

       Route::post('/updateSchedule/{id}', array(
      'as'=> 'user.updateSchedule',
      'uses'=>'UserController@updateSchedule') );


       Route::post('/updateCoverPhoto', array(
      'as'=> 'user.updateCoverPhoto',
      'uses'=>'UserController@updateCoverPhoto') );


     Route::get('/myProfile', array(
      'as'=> 'myProfile',
      'uses'=>'UserController@myProfile') );

     Route::get('/myEvals', array(
      'as'=> 'myEvals',
      'uses'=>'UserController@myEvals') );

     Route::get('/mySubordinates', array(
      'as'=> 'mySubordinates',
      'uses'=>'UserController@mySubordinates') );

     Route::get('/myTeam', array(
      'as'=> 'myTeam',
      'uses'=>'UserController@myTeam') );

     Route::get('/updateProfilepic/{id}', array(
      'as'=> 'updateProfilepic',
      'uses'=>'UserController@updateProfilepic') );


//********  MANPOWER *****************************

     Route::post('/manpower/deleteRequest', array(
      'as'=> 'manpower.deleteRequest',
      'uses'=>'ManpowerController@deleteRequest') );

     Route::post('/manpower/saveRequest', array(
      'as'=> 'manpower.saveRequest',
      'uses'=>'ManpowerController@saveRequest') );

     Route::post('/manpower/updateCount', array(
      'as'=> 'manpower.updateCountt',
      'uses'=>'ManpowerController@updateCount') );

     Route::post('/manpower/updateNotes', array(
      'as'=> 'manpower.updateNotes',
      'uses'=>'ManpowerController@updateNotes') );

     Route::post('/manpower/updateRequest', array(
      'as'=> 'manpower.updateRequest',
      'uses'=>'ManpowerController@updateRequest') );



      Route::get('/myRequests/{id}', array(
      'as'=> 'myRequests',
      'uses'=>'UserController@myRequests') );

      Route::get('/userRequests/{id}', array(
      'as'=> 'userRequests',
      'uses'=>'UserController@userRequests') );

       Route::get('/getMyRequests/{id}', array(
      'as'=> 'getMyRequests',
      'uses'=>'UserController@getMyRequests') );


     Route::get('/changePassword', array(
      'as'=> 'changePassword',
      'uses'=>'UserController@changePassword') );

     Route::post('/checkCurrentPassword', array(
      'as'=> 'user.checkCurrentPassword',
      'uses'=>'UserController@checkCurrentPassword') );

     Route::post('/updatePassword', array(
      'as'=> 'user.updatePassword',
      'uses'=>'UserController@updatePassword') );

     Route::post('/updateContact/{id}', array(
      'as'=> 'user.updateContact',
      'uses'=>'UserController@updateContact') );

    Route::get('/user/{id}/getWorkSched/', array(
          'as'=> 'getWorkSched',
          'uses'=>'UserController@getWorkSched') );

    Route::post('/user/{id}/getWorkSchedForTheDay/', array(
          'as'=> 'user.getWorkSchedForTheDay',
          'uses'=>'UserController@getWorkSchedForTheDay') );


    /* ---------- SURVEY ---------------*/

    Route::post('/saveSurvey', array(
      'as'=> 'survey.saveSurvey',
      'uses'=>'SurveyController@saveSurvey') );

    Route::get('/survey/download/{id}', array(
      'as'=> 'survey.downloadRaw',
      'uses'=>'SurveyController@downloadRaw') );

    Route::get('/survey/intro/{id}', array(
      'as'=> 'survey.intro',
      'uses'=>'SurveyController@intro') );

    Route::get('/survey/report/{id}', array(
      'as'=> 'survey.report',
      'uses'=>'SurveyController@report') );

    Route::get('/survey/participants/{id}', array(
      'as'=> 'survey.participants',
      'uses'=>'SurveyController@participants') );

    Route::get('/survey/category/{id}', array(
      'as'=> 'survey.showCategory',
      'uses'=>'SurveyController@showCategory') );

    Route::post('/bePart', array(
      'as'=> 'survey.bePart',
      'uses'=>'SurveyController@bePart') );

    Route::get('/surveyResults/{id}', array(
      'as'=> 'survey.surveyResults',
      'uses'=>'SurveyController@surveyResults') );

    Route::post('/saveItem', array(
      'as'=> 'survey.saveItem',
      'uses'=>'SurveyController@saveItem') );

    Route::post('/getPrevious', array(
      'as'=> 'survey.getPrevious',
      'uses'=>'SurveyController@getPrevious') );



    /* ---------- MEMO ---------------*/
    Route::post('/saveUserMemo', array(
          'as'=> 'user_memo.saveUserMemo',
          'uses'=>'UserMemoController@saveUserMemo') );


    Route::get('/program/{id}/widgets/', array(
          'as'=> 'widgets',
          'uses'=>'CampaignController@widgets') );


    /* ---------- FOR OPERATIONS stat reports-------------------*/

    Route::get('/campaignStats/{id}', 'CampaignController@showStats');
    Route::post('/getStats', 'CampaignController@getStats');
    Route::post('/getScheds', 'CampaignController@getScheds');

    Route::get('/agentStats/{id}', 'CampaignController@showAgentStats');
    Route::post('/getAgentStats', 'CampaignController@getAgentStats');
    Route::post('/getAgentScheds', 'CampaignController@getAgentScheds');

    Route::post('/getIndividualStat', 'CampaignController@getIndividualStat');
    Route::post('/exportAgentActivity', 'CampaignController@exportAgentActivity');


    /* ---------- ID PRINTING ---------------*/

    Route::get('/camera/', 'IDController@index');
    Route::get('/trainee/', 'IDController@trainee');
    Route::get('/camera_back/', 'IDController@camera_back');
    Route::get('/camera/single/{id}', 'IDController@load_single');
    Route::get('/camera/print/{id}', 'IDController@print_single');
    Route::get('/camera/get_qr/{id}', 'IDController@get_qr');
    Route::get('/camera/get_qr_front/{user_id?}', 'IDController@get_qr_front');
    Route::get('/camera/by_campaign/{id}', 'IDController@load_campaign');
    Route::post('/export_id', 'IDController@export_id');
    Route::post('/archive', 'IDController@archive');
    Route::post('/save_signature', 'IDController@save_signature');
    Route::post('/upload_signature', 'IDController@upload_signature');
    Route::post('/rename_id','IDController@rename_id');
    Route::post('/save_portrait','IDController@save_portrait');


    /* ---------- REWARDS SYSTEM ---------------*/
    Route::get('/manage-rewards-exclusives', 'RewardController@manage_exclusives');
    Route::get('/manage-rewards-exclusives/list/{page?}', 'RewardController@list_exclusives');
    Route::post('/add-rewards-exclusive', 'RewardController@add_exclusive');
    Route::post('/edit-rewards-exclusive', 'RewardController@edit_exclusive');
    Route::post('/claim-exclusive/{reward_id?}', 'RewardsHomeController@claim_exclusive');
    Route::get('/manage-exclusive-claims', 'RewardExclusiveClaimsController@index');
    Route::get('/manage-exclusive-claims/list/{show_redeemed?}', 'RewardExclusiveClaimsController@list_claims');
    Route::post('/approve-exclusive-claim/{claim_id?}', 'RewardExclusiveClaimsController@approve_claim');
    Route::post('/deny-exclusive-claim', 'RewardExclusiveClaimsController@deny_claim');


    Route::get('/manage-rewards/list/{page?}', 'RewardController@list_rewards');
    Route::resource('/manage-rewards', 'RewardController');
    Route::get('/manage-categories/list/{page?}', 'RewardsCategoryController@list_categories');
    Route::get('/manage-categories/fetch_tiers/{id}', 'RewardsCategoryController@list_category_tiers');
    Route::resource('/manage-categories', 'RewardsCategoryController');
    Route::get('/rewards-catalog', 'RewardsHomeController@rewards_catalog');
    Route::get('/rewards-catalog/list/{page?}', 'RewardsHomeController@rewards_catalog_list');
    Route::post('/claim-reward/{reward_id?}', 'RewardsHomeController@claim_reward');



    Route::get('/get_qr/{user_id?}', 'RewardsHomeController@get_qr');

    Route::resource('/orders', 'OrdersController');

    Route::get('/print-order/{code?}', 'RewardsHomeController@print_order');
    Route::get('/print-qr/{employee_id?}', 'RewardsHomeController@print_qr');
    Route::post('/cancel-order/{id?}', 'RewardsHomeController@cancel_order');


    Route::get('/manage-vouchers/list/{page?}', 'RewardVoucherController@list_vouchers');
    Route::resource('/manage-vouchers', 'RewardVoucherController');
    Route::post('/claim-voucher/{voucher_id?}', 'RewardsHomeController@claim_voucher');

    Route::get('/manage-voucher-claims', 'RewardVoucherClaimsController@index');
    Route::get('/manage-voucher-claims/list/{show_redeemed}', 'RewardVoucherClaimsController@list_claims');
    Route::get('/export-voucher-claims', 'RewardVoucherClaimsController@export_claims');
    Route::post('/confirm-voucher-claim/{claim_id?}', 'RewardVoucherClaimsController@confirm_claim');
    Route::post('/deny-voucher-claim/{claim_id?}', 'RewardVoucherClaimsController@deny_claim');

    Route::get('/manage-donations/list/{page?}', 'RewardDonationsController@list_donations');
    Route::resource('/manage-donations', 'RewardDonationsController');
    Route::post('/donate-reward-points', 'RewardsHomeController@donate_reward_points');

    Route::get('/manage-donation-intents', 'RewardDonationIntentController@index');
    Route::get('/manage-donation-intents/list', 'RewardDonationIntentController@list_intents');
    //Route::get('/export-donation-intents', 'RewardVoucherClaimsController@export_claims');
    Route::post('/confirm-donation-intent', 'RewardDonationIntentController@approve_intent');
    Route::post('/deny-donation-intent', 'RewardDonationIntentController@deny_intent');

    Route::get('/coffeeshop-stats/{start?}/{end?}', 'RewardsHomeController@statistics');


    //----------- USER-based reward actions

    Route::get('/initializeRewards/{floor_id}', array(
      'as'=> 'initializeRewards',
      'uses'=>'UserController@initializeRewards') );


    Route::post('/rewardsFeedback', array(
      'as'=> 'rewardsFeedback',
      'uses'=>'UserController@rewards_feedback') );

     Route::get('/rewardTransactions', array(
      'as'=> 'myTransactions',
      'uses'=>'UserController@rewards_transactions') );


    Route::get('/userRewards', array(
      'as'=> 'userRewards',
      'uses'=>'UserController@rewards') );

    Route::get('/awardPoints', array(
      'as'=> 'awardPoints',
      'uses'=>'UserController@rewards_award') );

    Route::get('/awardHistory', array(
      'as'=> 'awardHistory',
      'uses'=>'UserController@rewards_award_history') );


    Route::get('/awardTenure', array(
      'as'=> 'awardTenure',
      'uses'=>'UserController@rewards_tenure') );



    Route::get('/userRewards/about', array(
      'as'=> 'rewards_about',
      'uses'=>'UserController@rewards_about') );

    Route::post('/rewardsTransfer', array(
      'as'=> 'rewardsTransfer',
      'uses'=>'UserController@rewardsTransfer') );

    Route::post('/grantRewardPoints', array(
      'as'=> 'grantRewardPoints',
      'uses'=>'UserController@rewards_grantPoints') );

    Route::post('/coffeeshop', array(
      'as'=> 'rewardsCofeeshop',
      'uses'=>'UserController@rewards_coffeeshop') );

    Route::get('/userRewards/barista', array(
      'as'=> 'rewards_barista',
      'uses'=>'UserController@rewards_barista') );


  });

