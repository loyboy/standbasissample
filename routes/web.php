<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//check if sync
Route::get('/', function () {
    return view('index');
});
Route::get('/', function () {
    return view('index');
});
Route::get('/index', function () {
    return view('index');
});
/******General web parts*****/
//cobkit
Route::get('/cobkit', function () {
    return view('cobkit');
});
//m&e
Route::get('/mande', function () {
    return view('m&e');
});
//lessonnote
Route::get('/lessonnote', function () {
    return view('lessonnote');
});
//about
Route::get('/about', function () {
    return view('about');
});
//products
Route::get('/product', function () {
    return view('help');
});
Route::get('testword', function () {
    return view('testword');
});
/******End General web parts*****/

/******Home web parts******/
Route::get('/homereal', function () {
    return view('home_part.home_home');
});
Route::get('/homeadvisory', function () {
    return view('home_part.home_advisory');
});
Route::get('/homestandard', function () {
    return view('home_part.home_standard');
});
Route::get('/homedirectory', function () {
    return view('home_part.home_directory');
});
Route::get('/homepackage', function () {
    return view('home_part.home_package');
});
/******END Home web parts******/


/******Attendance web + controller parts******/

//engage session +++
Route::group(['middleware' => 'web'], function () {
//
Route::get('/register_dashboard', function () { return view('att_dashboard'); })->name('reg.dashboard')->middleware('ckattendance');

Route::get('/register_stugex', function () { return view('att_student_get'); })->middleware('ckattendance');
//
Route::get('/register_stuget/{stu_id}', function ($stu_id) 
    { 
     session(['att_student_id' => $stu_id]); 
     return redirect('/register_stugex'); 
    })->name('reg.student.get')->middleware('ckattendance');

//actual attendance web view url loading with middleware
Route::get('/register', 'AttendanceController@index')->middleware('ckattendance');
//url for the actual attendance taking tab within dasboard page.
Route::get('/register_take', function () { return view('att_take'); })->name('reg.take')->middleware('ckattendance');
//url for the actual attendance taking tab within dasboard page.
Route::get('/register_rev', function () { return view('att_review'); })->name('reg.review')->middleware('ckattendance');
//
Route::get('/register_stselect', function () { return view('att_student_select'); })->name('reg.stselect')->middleware('ckattendance');
//
Route::get('/register_stview', function () { return view('att_student_view'); })->name('reg.stview')->middleware('ckattendance');
//
Route::get('/register_calselect', function () { return view('att_calendar_select'); })->name('reg.calselect')->middleware('ckattendance');
//
Route::get('/register_calview', function () { return view('att_calendar_view'); })->name('reg.calview')->middleware('ckattendance');
//
Route::get('/register_head_view', function () { return view('register_part.reg_review'); })->name('reg.head.review');
//
Route::get('/register_head_comment', function () { return view('register_part.reg_comment'); })->name('reg.head.comment');

//admin....Register page
Route::get('/regdashboard', function () {
    return view('register_part.dashboard');
})->middleware('ckregisteradmin');
//
Route::get('/regdashboard_sel/{tea_id}', function ($tea_id) {
    session(['reg_teacher_id' => $tea_id]);
    return redirect('/regdashboard_select');
})->name('reg.dashboard.select')->middleware('ckregisteradmin');

//
Route::get('/regdashboard_select', function () {
    return view('register_part.select_year_term');
})->middleware('ckregisteradmin');

//
Route::get('/regdashboard_vx/{term}', function ($term) {
    session(['reg_term' => $term]);
    return redirect('/regdashboard_view');
})->name('reg.dashboard.view')->middleware('ckregisteradmin');

//
Route::get('/regdashboard_view', function () {
    return view('register_part.att_view');
})->middleware('ckregisteradmin');

//
Route::get('/regviewsingle_vx/{rowcall}', function ($rowcall) {
    session(['reg_rowcall' => $rowcall]);
    return redirect('/regviewsingle_view');
})->name('reg.viewsingle.view')->middleware('ckregisteradmin');

//
Route::get('/regviewsingle_view', function () {
    return view('register_part.att_view_single');
})->middleware('ckregisteradmin');


//attendance admin pages
Route::get('/regdashboard_decision', function () {
    return view('register_part.approve_lsn');
})->name('reg.dashboard.approve')->middleware('ckregisteradmin');
//attendance admin pages
Route::get('/regdashboard_profile', function () {
    return view('register_part.profile');
})->name('reg.dashboard.profile')->middleware('ckregisteradmin');
//attendance admin pages
Route::get('/regdashboard_activity', function () {
    return view('register_part.activity');
})->name('reg.dashboard.activity')->middleware('ckregisteradmin');
//attendance admin pages
Route::get('/regviewsingle/{lsn_id}', function ($lsn_id) {
    session(['reg_att_id' => $lsn_id]);
    return view('register_part.reg_view_single');
})->name('reg.view.s')->middleware('ckregisteradmin');


});


//load attendance login web view
Route::get('/attlogin', function () {
    return view('att_login');
});

//load attendance login 2 web view
Route::get('/attlogin2', function () {
    return view('att_login_admin');
})->name('att.login.head');



//register admin
Route::post('/r_setyear', function() {
	// 
        $res = Request::input('year');
    //
        session(['reg_year' => $res]);// 
        session()->flash('reg_success','1');
        
        $mymsg = array(
            'http_status_res1'=> $res
        );
        return Response::json($mymsg);
	
});

//register get profile details
Route::post('/r_getprofile', function() {
	   // 
        $pro = Request::input('profile');

        $results = DB::table('users')->where([['tea_id', '=', $pro ]])->get(); 
        
        if (count($results) > 0){
                 
            foreach ($results as $s){
                session(['att_profile_email' => $s->_EMAIL]);    
                session(['att_profile_pnum' => $s->_PNUM]);    
            }
        }
        
     
        return Response::json($results);
	
});

//approve attendance record
Route::post('/r_approve', function() {
	// 
        $res = Request::input('regapp');
       
        $resx = explode(";",$res);
        DB::table('school_att')->where('_type', '=', 'Disapproved')->where('att_id', '=', intval($resx[0]))->where('user_id', '=', intval($resx[1]) )->delete();
        $att = new \App\School_Att;
        $att->_term = session('reg_term');
        $att->_schyr = session('reg_year');
        $att->att_id = $resx[0];
        $att->user_id = $resx[1];
        $att->_datetime = date('Y-m-d H:i:s');
        $att->_type = "Approved";//Disapproved
        $att->_comment = "Absence from School has been Approved";
         
        $att->save();
        session()->flash('reg_approved','1');
        
        $mymsg = array(
            'http_status_res1'=> $res
        );
        return Response::json($mymsg);
	
});

//disapprove attendance record
Route::post('/r_disapprove', function() {
	// 
        $res = Request::input('xregapp');
       
        $resx = explode(";",$res);
        DB::table('school_att')->where('_type', '=', 'Approved')->where('att_id', '=', intval($resx[0]))->where('user_id', '=', intval($resx[1]) )->delete();
        $att = new \App\School_Att;
        $att->_term = session('reg_term');
        $att->_schyr = session('reg_year');
        $att->att_id = $resx[0];
        $att->user_id = $resx[1];
        $att->_datetime = date('Y-m-d H:i:s');
        $att->_type = "Disapproved";//Approved
        $att->_comment = "Absence from School has NOT been approved. Please We will need to review this.";
         
        $att->save();
        session()->flash('reg_disapproved','1');
        
        $mymsg = array(
            'http_status_res1'=> $res
        );
        return Response::json($mymsg);
	
});

//controller function for login attempt
Route::post('/registerform', 'AttendanceController@login');
//controller function for editing profile from User
Route::post('/regeditprofile', 'AttendanceController@editprofile');
//controller function for login attempt from head
Route::post('/registerform2', 'AttendanceController@login2');
//controller function for view student records attempt
Route::post('/registercalendar_', 'AttendanceController@reviewstudent');
//controller function for view student records attempt
Route::post('/registerstview', 'AttendanceController@reviewstudent');
//controller function for view student records attempt
Route::post('/registercalview', 'AttendanceController@reviewcalendar');
//controller function for proceed attempt
Route::post('/registerproceed', 'AttendanceController@proceed');
//controller function for submit attendance attempt
Route::post('/registersubmit', 'AttendanceController@submitattendance');
//controller function for logout attempt
Route::get('/registerlogout', 'AttendanceController@logout');



/******end Attendance web parts*****/


/******Lesson note web + controller parts*****/
//engage session +++
Route::group(['middleware' => 'web'], function () {
    
Route::get('/lessonfirst', 'LessonNoteController@index2')->name('ln.blank')->middleware('cklessonfirst');

//controller function for login attempt
Route::post('/lessonloginform', 'LessonNoteController@login');

//controller function for login attempt
Route::post('/lessonloginform2', 'LessonNoteController@login2');

//controller function for submit draft of lessonnote attempt
Route::post('/submitlesson', 'LessonNoteController@submitLN');

//controller function for logout attempt
Route::get('/lessonlogout', 'LessonNoteController@logout')->name('ln.logout');

//controller function for downloading ln file
Route::get('/downloadlesson', 'LessonNoteController@downloadLN')->name('ln.downloadLN');

//controller function for proceed attempt
Route::post('/lessonproceed', 'LessonNoteController@proceed');

});
//
Route::get('/lntemplate_', function () {
    return view('ls_template');
})->name('ln.template.2')->middleware('cklessonnote');

//
Route::get('/lnviewsub', function () {
    return view('ls_view_sub');
})->name('ln.view.sub')->middleware('cklessonnote');

//
Route::get('/lnviewreview', function () {
    return view('ls_view_review');
})->name('ln.view.review')->middleware('cklessonnote');

//
Route::get('/lnviewarchive', function () {
    return view('ls_view_archived');
})->name('ln.view.archive')->middleware('cklessonnote');

//load lessonnote login2 web view
Route::get('/lnlogin', function () {
    return view('ls_login');
})->name('ln.login.tea');

//
Route::get('/lnchangeprofile', function () {
    return view('pageparts.lessonnote.changeLn');
})->name('ln.change.pro');

//
Route::post('/l_getlntemplate', function() { 
$filex = asset("storage/template.doc");
$word = new COM("word.application") or die("Unable to instanciate Word"); 
print "Loaded Word, version {$word->Version}\n"; 
$word->Visible = 1; 
//$input = asset("storage/demo.doc"); 
$word->Documents->Open($filex); 
$mymsg = array(
    'http_status_lntemplate'=>1
);
return Response::json($mymsg);
});

//
Route::get('/lnviewdoc/{lsn_path}', function ($lsn_path) { 
    $lnu = str_replace('+', '/', $lsn_path);
    $filex = asset("storage/". $lnu);
    $word = new COM("word.application") or die("Unable to instanciate Word"); 
    print "Loaded Word, version {$word->Version}\n"; 
    $word->Visible = 1; 
  
    $word->Documents->Open($filex); 
    $mymsg = array(
        'http_status_lntemplate'=>1
    );
    return Response::json($mymsg);
})->name('ln.view.doc');


//
Route::get('/lnview', function () {
     session()->forget('ln_update');//forget about the updated file...
     session()->forget('ln_update_form');//forget the stored variables in text field...
    return view('ls_view');
})->name('ln.view')->middleware('cklessonnote');

//
Route::get('/lnwordview/{lsn_url}', function ($lsn_url) {
    $lnu = str_replace('*', '//', $lsn_url);
    $lnu2 = str_replace('+', '/', $lnu);
    session(['ln_lsn_url' => $lnu2]);
    return view('ls_word_view');
})->name('ln.wordview')->middleware('cklessonnote');


//
Route::get('/lnactive', function () {
     session()->forget('ln_update');//forget about the updated file...
     session()->forget('ln_update_form');//forget the stored variables in text field...
    return view('ls_active');
})->name('ln.active')->middleware('cklessonnote');


//
Route::get('/lndelete/{lsn_id}', function ($lsn_id) {

    DB::table('lessonnote_')->where('lsn_id', '=', intval($lsn_id))->delete();
    session()->flash('ln_delete', '1');         
    return redirect('/lnview');
    
})->name('ln.delete')->middleware('cklessonnote');


//
Route::get('/lnlaunch/{lsn_id}/{tea_id}/{sub_id}/{cls_id}', function ($lsn_id,$tea_id,$sub_id,$cls_id) {
    //close all other lessonnotes that are active
    App\LessonNote::where('TEA_ID', $tea_id)->where('CLASS_ID', $cls_id)->where('SUB_ID', $sub_id)->where('_TERM',session('ln_term'))->where('_LAUNCH','!=', '1970-10-10 00:00:00')->update(['_CLOSURE' =>  date("Y-m-d H:i:s") ]);
    //make all the Lessonnotes have their active sate reset to zero
    App\LessonNote::where('TEA_ID', $tea_id)->where('SUB_ID', $sub_id)->where('CLASS_ID', $cls_id)->update(['_LAUNCH' =>  '1970-10-10 00:00:00' ]);
    //make the particular lessonnote have its lauch set to something
    App\LessonNote::where('LSN_ID', $lsn_id)->update(['_LAUNCH' =>  date("Y-m-d H:i:s") ]);
   
   session()->flash('ln_launch', '1');      
   return redirect('/lnviewsub');
    
})->name('ln.launch')->middleware('cklessonnote');

//make the lessonnote enter the redraft side---------NOT USED
Route::get('/lnredraft/{lsn_id}', function ($lsn_id) {
    $results =  DB::table('lessonnote_')->where([['lsn_id', '=', $lsn_id]])->delete();
   
    session()->flash('lsn_id_redraft',$lsn_id);
      
   return redirect('/lntemplate_');
    
})->name('ln.redraft')->middleware('cklessonnote');



//make a lessonnote enter review column
Route::get('/lnclose/{lsn_id}', function ($lsn_id) {
    
    App\LessonNote::where('LSN_ID', $lsn_id)->update(['_CLOSURE' =>  date("Y-m-d H:i:s") ]);
    //echo "Email Sent with attachment. Check your inbox.";
   session()->flash('ln_close', '1');      
   return redirect('/lnviewreview');
    
})->name('ln.close')->middleware('cklessonnote');

///make a lessonnote enter archived column
Route::get('/lndraft/{lsn_id}', function ($lsn_id) {
    
    App\LessonNote::where('LSN_ID', $lsn_id)->update(['_DRAFT' =>  date("Y-m-d H:i:s") ]);
 
   session()->flash('ln_archive', '1');      
   return redirect('/lnviewarchive');
    
})->name('ln.archive')->middleware('cklessonnote');

//view a pdf whenever you click a div
Route::get('/lnviewpdf/{res_path}', function ($res_path) {
$pathx = str_replace('+', '/', $res_path);
$path = storage_path($pathx);

return response()->file($path);
})->name('ln.downloadLNview');



//////////////////////////////////////////////////////////////
//lessonnote admin pages
Route::get('/lndashboard', function () {
    return view('lessonnote_part.dashboard');
})->middleware('cklessonadmin');
//lessonnote admin pages
Route::get('/lndashboard_select/{lsn_id}', function ($tea_id) {
    session(['ln_teacher_id' => $tea_id]);
    return view('lessonnote_part.select_year_term');
})->name('ln.dashboard.select')->middleware('cklessonadmin');
//lessonnote admin pages
Route::get('/lndashboard_viewlsn/{term}', function ($term) {
    session(['ln_term' => $term]);
    return view('lessonnote_part.lsn_view');
})->name('ln.dashboard.view')->middleware('cklessonadmin');
//lessonnote admin pages
Route::get('/lndashboard_decision', function () {
    return view('lessonnote_part.approve_lsn');
})->name('ln.dashboard.approve')->middleware('cklessonadmin');
//lessonnote admin pages
Route::get('/lndashboard_profile', function () {
    return view('lessonnote_part.profile');
})->name('ln.dashboard.profile')->middleware('cklessonadmin');
//lessonnote admin pages
Route::get('/lndashboard_activity', function () {
    return view('lessonnote_part.activity');
})->name('ln.dashboard.activity')->middleware('cklessonadmin');
//
Route::get('/lnviewsingle/{lsn_id}', function ($lsn_id) {
    session(['ln_lsn_id' => $lsn_id]);
    return view('lessonnote_part.ls_view_single');
})->name('ln.view.s')->middleware('cklessonadmin');

//load lessonnote login 2 web view
Route::get('/lnlogin2', function () {
    return view('ls_login2');
})->name('ln.login.head');

//
Route::post('/l_setyear', function() {
	// 
        $res = Request::input('year');
    //
        session(['ln_year' => $res]);//set the 
        
        $mymsg = array(
            'http_status_res1'=> $res
        );
        return Response::json($mymsg);
	
});

//lsn comment 
Route::post('/mnecomment', function() {
	// pass back some data 
        $com = Request::input('comment');
        
        if ($com === null){
            $com = " ";
        }
        
        session(['mne_comment_save' => $com]);
       
        $mymsg = array(
            'http_status'=>$com
        );
        return Response::json($mymsg);
       
}); 

// edit LN profile Userid
Route::post('/editLN', function() {
	// pass back some data 
        $proid = Request::input('proid');
        
        $results  = \App\Lnprofile::where("LN_PRO","=",$proid)->get();

        foreach ($results as $r){
            
            session(['e_ln_pro_name' => $r->_NAME]);
            session(['e_ln_pro_sub' => $r->_SUBJECT]);
            session(['e_ln_pro_class' => $r->_CLASS]);
            session(['e_ln_pro_term' => $r->_TERM]);

        }
        
        $mymsg = array(
            'http_status'=>"1"
        );
        return Response::json($mymsg);
       
}); 
//

Route::get('/mnelsnapprove/{lsn_id}', function ($lsn_id) {

         session()->flash('ln_approved', "1");
         $ln = new \App\School_LN;
         $ln->LN_ID = $lsn_id;
         $ln->_ACTION = 2;
         $ln->_COMMENT = session('mne_comment_save');
         $ln->_COMMENT_TEA = " ";
         $ln->_DATETIME = date("Y-m-d H:i:s");
         $ln->user_id = session('ln_head_id'); 
         $ln->SCHOOL_SCH_ID = session('ln_school_id');   
         $ln->save();
    App\LessonNote::where('LSN_ID', $lsn_id)->update(['_DRAFT' => '1970-10-10 00:00:00','_REVERT' => '1970-10-10 00:00:00','_APPROVAL' => date("Y-m-d H:i:s") ]);
    return redirect('/lndashboard');
    
})->name('mne.lsn.approve')->middleware('cklessonadmin');
//
Route::get('/mnelsnreject/{lsn_id}', function ($lsn_id) {

         session()->flash('mne_rejected', "1");
         $ln = new \App\School_LN;
         $ln->LN_ID = $lsn_id;
         $ln->_ACTION = 1;
         $ln->_COMMENT = session('mne_comment_save');
         $ln->_COMMENT_TEA = " ";
         $ln->_DATETIME = date("Y-m-d H:i:s");
         $ln->user_id = session('ln_head_id'); 
         $ln->SCHOOL_SCH_ID = session('ln_school_id');   
         $ln->save();
    App\LessonNote::where('LSN_ID', $lsn_id)->update(['_DRAFT' => '1970-10-10 00:00:00','_SUBMISSION' => '1970-10-10 00:00:00','_REVERT' => date("Y-m-d H:i:s") ]);
   
    return redirect('/lndashboard');
    
})->name('mne.lsn.reject')->middleware('cklessonadmin');


/******end Lesson note web + controller parts*****/


/********School Ratings web + controller parts********/
//session store
Route::group(['middleware' => 'web'], function () {

//home page of ratings
Route::get('/ratings', 'RatingsController@index');
//home page of ratings
Route::get('/ratings_e', 'RatingsController@index_1');
//home page of ratings
Route::get('/ratings_c', 'RatingsController@index_2');
//home page of ratings
Route::get('/ratings_t', 'RatingsController@index_3');

//controller function for evaluate attempt
Route::post('/evaluate', 'RatingsController@evaluate');

//controller function for compare attempt
Route::post('/compare', 'RatingsController@compare');

//controller function for find school attempt on clicking
Route::post('/schprofile', 'RatingsController@schprofile');

//Schools Ratings Evaluate Ajax Code---Sends anything typed from 'evaluate school' textfield to this place; to return matched search!!
Route::post('/r_evaluate', function() {
	//get the keyword from the textfield
        $search = Request::input('keywords');
        $results  = \App\School::where("_name","like",$search.'%')->orWhere('_state','like',$search.'%')->get();
        if (count($results) > 0){
           //return a html response back with the search results 
           return view('pageparts.ratings.response')->with("evaluate_result",$results)->render();
        }
        else{
            //return a html response back with the no search results  
           return view('pageparts.ratings.response')->render();
           }
	
});
//Schools Ratings Compare Ajax Code---Sends anything typed from textfield to this place; to return search matched!!
Route::post('/r_compare', function() {
	// pass back some data, along with the original data, just to prove it was received
        if (null !== Request::input('keywor')){
        $search = Request::input('keywor');
        
        $results  = \App\School::where("_name","like",$search.'%')->orWhere('_state','like',$search.'%')->get();
      
       
        if (count($results) > 0){
           //return a html response back with the search results 
           return view('pageparts.ratings.response_c')->with("compare_result",$results)->render();
         
           }
        else{
            //return a html response back with the no search results  
           return view('pageparts.ratings.response_c')->render();
           }
        }
        else if (null !== Request::input('keywor2') ){
        $search = Request::input('keywor2');
        
        $results  = \App\School::where("_name","like",$search.'%')->orWhere('_state','like',$search.'%')->get();
      
       
        if (count($results) > 0){
           //return a html response back with the search results 
           return view('pageparts.ratings.response_c2')->with("compare_result",$results)->render();
         
           }
        else{
            //return a html response back with the no search results  
           return view('pageparts.ratings.response_c2')->render();
           }
        }
        
});
//Schools Ratings 'Tour school' Ajax Code---Sends anything typed from textfield to this place; to return search matched!!
//---not in use yet
Route::post('/r_schprofile', function() {
	// pass back some data, along with the original data, just to prove it was received
        $search = Request::input('keywords');
        
        $results  = \App\School::where("_name","like",$search.'%')->orWhere('_state','like',$search.'%')->get();
      
        if (count($results) > 0){
           //return a html response back with the search results 
           return view('pageparts.ratings.response_c3')->with("schprofile_result",$results)->render();
         
           }
        else{
            //return a html response back with the no search results  
           return view('pageparts.ratings.response_c3')->render();
           
           
            }
});

//Schools Ratings  Search from checkbox---Sends search options from School Nav Point down here!
//---not in use
Route::get('/r_findschool', function() {
	// pass back some data, along with the original data, just to prove it was received
        $se = Request::input('findall');
         
         if (null == $se or $se == " ") {
        //->simplePaginate(10)
        
         $g = strlen(Request::input('gender'));
         $o = strlen(Request::input('operator'));
         $s = strlen(Request::input('state'));
         
         $ge = Request::input('gender');
         $op = Request::input('operator');
         $st = Request::input('state');

        if ($g > 0 and $o <= 0 and $s <= 0){
             
          $results =  DB::table('school')->where([['_gender', 'like', '%'.$ge.'%']])->get();
           session(['find_school_c' => array($ge)]); 
          
        }
        else if ($g <= 0 and $o > 0 and $s <= 0){
          $results  = DB::table('school')->where([['_owner', 'like', '%'.$op.'%']])->get();
          session(['find_school_c' => array($op)]); 
        }
        else if ($g <= 0 and $o <= 0 and $s > 0){
          $results  = DB::table('school')->where([['_state', 'like', '%'.$st.'%']])->get();
          session(['find_school_c' => array($st)]); 
        }
        else if ($g > 0 and $o > 0 and $s <= 0){
          $results =  DB::table('school')->where([['_gender', 'like', '%'.$ge.'%'],['_owner', 'like', '%'.$op.'%']])->get();
          session(['find_school_c' => array($ge,$op) ]); 
        }
        else if ($g <= 0 and $o > 0 and $s > 0){
          $results =  DB::table('school')->where([['_state', 'like', '%'.$st.'%'],['_owner', 'like', '%'.$op.'%']])->get();
          session(['find_school_c' => array($op,$st) ]);
        }
        else if ($g > 0 and $o <= 0 and $s > 0){
          $results =  DB::table('school')->where([['_gender', 'like', '%'.$ge.'%'],['_state', 'like', '%'.$st.'%']])->get();
          session(['find_school_c' => array($ge,$st) ]);
        }
        else if ($g > 0 and $o > 0 and $s > 0){
          $results = DB::table('school')->where([['_gender', 'like', '%'.$ge.'%'],['_owner', 'like', '%'.$op.'%'],['_state', 'like', '%'.$st.'%']])->get();
          session(['find_school_c' => array($ge,$op,$st) ]);
            }
            
         }  
         
        else{
       
         $results = DB::table('school')->get();
          session(['find_school_c' => null ]);
         // $results = \App\School::simplePaginate(10);
          }
         if (count($results) > 0){
            session(['find_school_r' => $results]); 
            session()->forget('metrics_perf_');
            
            $mymsg = array(
            'http_status_find'=>'finding School was Successfull'
            );
            
       // return view('ratings_part.ratings');    
        return Response::json($results);
         
            }
        
        else{
           //could not find school----------
           session()->flash('error_find_school', '1');
           session()->forget('find_school_r');
            //return a html response back with the no search results  
            $mymsg = array(
            'http_status_find'=>'Error finding School'
            );
        
      
        return Response::json($mymsg);
            }
            
        });
//////////////////////////////////////////        
//used to get the School ID within findschool view...after the user has chosen school to see its School 
//--ajax method...NOT IN USE
Route::post('/r_findschool_i', function() {
	// pass back some data, along with the original data, just to prove it was received
        $id = Request::input('findsch');
        $results =  DB::table('school')->where([['sch_id', '=', $id]])->get();
        session(['profile_sch_send' => $results]);
        session()->flash('profile_sch',1);        
        $mymsg = array(
            'http_status_find2'=>1
        );
        return Response::json($mymsg);
      
});
//////////////////////////////////////////        
//used to get back whenever  
Route::group(['middleware' => 'web'], function () {
//
Route::post('/r_goback', function() {
	$mymsg = array(
            'http_status_find2'=>3
        );
        if (session()->has('profile_sch')){
            session()->forget('profile_sch');
            //session()->flash('temp_find_school_r', session('find_school_r') );
            $mymsg = array(
            'http_status_find2'=>1
        );
        }
       
        
        return Response::json($mymsg);
      
}); });

//used to set metric_data when 'Evaluate Us'' button is clicked---ajax method
//--NOT IN USE
Route::post('/r_evaluate_j', function() {
	// pass back some data, along with the original data, just to prove it was received
        $id = Request::input('evalid');
        $statex = Request::input('evalstate');
        
        session(['evaluate_sch_id' => $id]);
        session(['evaluate_sch_st' => $statex]);
        
        session()->flash('metrics_perf_', 1);
        $status = session()->has('metrics_perf_');
        $mymsg = array(
            'http_status_evalaute'=>$status
        );
        return Response::json($mymsg);
});

//after you have picked the school, then you click "Evaluate Us" Button at the Bottom
Route::get('/r_evaluate_i/{rat_sch}/{rat_st}', function($rat_sch,$rat_st) {
	// pass back some data, along with the original data, just to prove it was received
        $id = $rat_sch;
        $statex = $rat_st;
        session(['evaluate_sch_id' => $id]);
        session(['evaluate_sch_st' => $statex]);
        
        session()->flash('metrics_perf_', 1);
        $results = DB::select( 'SELECT * FROM school WHERE SCH_ID = :SCH ', ["SCH" => $id] );
        session(['profile_sch_eval' =>  $results ]);
       
        return redirect('/ratings_e');
})->name('rat.evaluate');

//after you have picked the school, then you click "Compare Us" Button 
Route::get('/r_compare_iz/{rat_sch}/{rat_st}/{rat_na}', function($rat_sch,$rat_st,$rat_na) {
	// pass back some data, along with the original data, just to prove it was received
      
        $id = $rat_sch;
        $statex = $rat_st;
        $namex = $rat_na;
         session(['compare_sch_id_i' => $id]);
         session(['compare_sch_st_i' => $statex]);
         session()->flash('compare_sch_name_i',$namex);
       // session()->flash('c_metrics_perf_', 1);       
        return redirect('/ratings_c');
})->name('rat.compareit');

//after you have picked the school in list of schools 
Route::get('/r_schprofilez/{rat_sch}', function($rat_sch) {
	// pass back some data, along with the original data, just to prove it was received
        $id = $rat_sch;
        
        $results =  DB::table('school')->where([['sch_id', '=', $id]])->get();
        session(['profile_sch_send' => $results]);
        session()->flash('profile_sch',1);

        return redirect('/ratings');
      
})->name('rat.schprofile');

//after you have picked the school in school view list, store values in a session object
//--not used
Route::post('/r_schprofile_i', function() {
	// pass back some data, along with the original data, just to prove it was received
        $id = Request::input('evalid');
        $statex = Request::input('evalstate');
        session(['profile_sch_id' => $id]);
        session(['profile_sch_st' => $statex]);
        $status = session()->has('evaluate_sch_id');
        $mymsg = array(
            'http_status_pro'=>$status
        );
        return Response::json($mymsg);
      
}); 
//compare:: store session of 1st school
Route::post('/r_compare_i', function() {
	// pass back some data, along with the original data, just to prove it was received
        $id = Request::input('evalid');
        $namex = Request::input('evalname');        
        $statex = Request::input('evalstate');        
        session(['compare_sch_id_i' => $id]);
        session()->flash('compare_sch_name_i',$namex); 
        session(['compare_sch_st_i' => $statex]);
        $status = session()->has('compare_sch_id_i');
        $mymsg = array(
            'http_status_c'=>$status
        );
        return Response::json($mymsg);
      
}); 
//compare:: store session of 2nd school
Route::post('/r_compare_ii', function() {
	// pass back some data, along with the original data, just to prove it was received
        $id = Request::input('evalid');
        $statex = Request::input('evalstate');        
        session(['compare_sch_id_ii' => $id]);
        session(['compare_sch_st_ii' => $statex]);
        $status = session()->has('compare_sch_id_ii');
        $mymsg = array(
            'http_status_c2'=>$status
        );
        return Response::json($mymsg);
       
}); 
    
});
/********end School Ratings web + controller parts******/

/********Begin School Measurement and Evaluation web + controller parts******/

//login to mne
Route::get('/mnelogin', function () {
    return view('mne_parts.mne_login');
})->name('mne.tea.login');
Route::get('/mnelogin2', function () {
    return view('mne_parts.mne_login2');
})->name('mne.head.login');

//engage session +++
Route::group(['middleware' => 'web'], function () {

//Student stuff..........
Route::get('/mne_stu_select_att', function () { return view('mne_parts.mne_student_select'); })->name('mne.stu.select')->middleware('ckmeastudent');
//
Route::get('/mne_stu_select_ass', function () { return view('mne_parts.mne_student_select2'); })->name('mne.stu.select2')->middleware('ckmeastudent');
//
Route::get('/mne_stu_view_att', function () { return view('mne_parts.mne_student_view'); })->name('mne.stu.view')->middleware('ckmeastudent');
//
Route::get('/mne_stu_view_ass', function () { return view('mne_parts.mne_student_view2'); })->name('mne.stu.view2')->middleware('ckmeastudent');
//
Route::get('/mne_stu_review', function () { return view('mne_parts.mne_student_review'); })->name('mne.stu.review')->middleware('ckmeastudent');

//Teacher stuff..........//

//Teacher review page...
Route::get('/mne_tea_review', function () { return view('mne_parts.mne_teacher_review'); })->name('mne.tea.review')->middleware('ckmeateacher');

//Teacher select attendance to view His Attendance MNE page
Route::get('/mne_tea_select_att', function () { return view('mne_parts.mne_teacher_select'); })->name('mne.tea.select')->middleware('ckmeateacher');

//Teacher select assessment to view His Assessment MNE page
Route::get('/mne_tea_select_ass', function () { return view('mne_parts.mne_teacher_select2'); })->name('mne.tea.select2')->middleware('ckmeateacher');

//Teacher select lessonnote page
Route::get('/mne_tea_select_lsn', function () { return view('mne_parts.mne_teacher_lsn_select'); })->name('mne.tea.select.lsn')->middleware('ckmeateacher');

//Teacher select attendance of class of student to view page
Route::get('/mne_tea_select_att_stu', function () { return view('mne_parts.mne_teacher_select3'); })->name('mne.tea.select3')->middleware('ckmeateacher');

//Teacher select attendance of individual class of student to view page
Route::get('/mne_tea_select_att_stu2', function () { return view('mne_parts.mne_teacher_select4'); })->name('mne.tea.select4')->middleware('ckmeateacher');

//Teacher select lessonnote to view page
Route::get('/mne_tea_view_lsn', function () { return view('mne_parts.mne_teacher_lsn_view'); })->name('mne.tea.view.lsn')->middleware('ckmeateacher');

//Teacher show attendance of teacher
Route::get('/mne_tea_view', function () { return view('mne_parts.mne_teacher_view'); })->name('mne.tea.view')->middleware('ckmeateacher');

//Teacher show assessments of students of a class
Route::get('/mne_tea_view2', function () { return view('mne_parts.mne_teacher_view2'); })->name('mne.tea.view2')->middleware('ckmeateacher');

//Teacher show attendance class mne records overall...
Route::get('/mne_tea_view_class', function () { return view('mne_parts.mne_teacher_view_stu'); })->name('mne.tea.view.class')->middleware('ckmeateacher');

//Teacher show current attendance of students in a class----  
Route::get('/mne_tea_stat', function () { return view('mne_parts.mne_teacher_stats'); })->name('mne.tea.stat')->middleware('ckmeateacher');

//Teacher select attendance of particular student to view page----REDUNDANT
Route::get('/mne_tea_view_stu', function () { return view('mne_parts.mne_teacher_stu'); })->name('mne.tea.student')->middleware('ckmeateacher');

//Head of School stuff..........
Route::get('/mne_head_review', function () { return view('mne_parts.mne_head_review'); })->name('mne.head.review')->middleware('ckmeahead');
//
Route::get('/mne_head_view', function () { return view('mne_parts.mne_head_view'); })->name('mne.head.view')->middleware('ckmeahead');
//
Route::get('/mne_head_view_class', function () { return view('mne_parts.mne_head_view_class'); })->name('mne.head.view.class')->middleware('ckmeahead');
//
Route::get('/mne_head_view2', function () { return view('mne_parts.mne_head_view2'); })->name('mne.head.view2')->middleware('ckmeahead');
//
Route::get('/mne_head_view_ass', function () { return view('mne_parts.mne_head_view_ass'); })->name('mne.head.view.ass')->middleware('ckmeahead');
//
Route::get('/mne_head_view_lsn', function () { return view('mne_parts.mne_head_lsn_view'); })->name('mne.head.view.lsn')->middleware('ckmeahead');
//
Route::get('/mne_head_select_att', function () { return view('mne_parts.mne_head_select'); })->name('mne.head.select')->middleware('ckmeahead');
//
Route::get('/mne_head_select_att2', function () { return view('mne_parts.mne_head_select2'); })->name('mne.head.select2')->middleware('ckmeahead');
//
Route::get('/mne_head_select_ass', function () { return view('mne_parts.mne_head_select3'); })->name('mne.head.select3')->middleware('ckmeahead');
//
Route::get('/mne_head_select_lsn', function () { return view('mne_parts.mne_head_select4'); })->name('mne.head.select4')->middleware('ckmeahead');
//
Route::get('/mne_head_cal', function () { return view('mne_parts.mne_head_cal'); })->name('mne.head.calendar')->middleware('ckmeahead');


//controller function for login attempt
Route::post('/mneloginx', 'MneController@login');
//controller function for view student attendance attempt on MNE
Route::post('/s_mnereviewatt', 'MneController@reviewstudentatt');
//controller function for view teacher attendance attempt on MNE
Route::post('/t_mnereviewatt', 'MneController@reviewteacheratt');
//controller function for view teacher attendance attempt on MNE
Route::post('/t_mnereviewclass', 'MneController@reviewteacherclass');
//controller function for view teacher lessonnote attempt on MNE
Route::post('/t_mnereviewlsn', 'MneController@reviewteacherlsn');
//controller function for view teacher attendance attempt on MNE
Route::post('/t_mnereviewstats', 'MneController@reviewteacherstats');
//controller function for view teacher assessment attempt on MNE
Route::post('/t_mnereviewass', 'MneController@reviewteacherass');
//controller function for view student aseessment attempt on MNE
Route::post('/s_mnereviewass', 'MneController@reviewstudentass');
//controller function for view head attendance attempt on MNE for teachers
Route::post('/h_mnereviewatt', 'MneController@reviewheadatt');
//controller function for view head attendance attempt on MNE for class
Route::post('/h_mnereviewclass', 'MneController@reviewheadclass');
//controller function for view head assessment attempt on MNE for class
Route::post('/h_mnereviewass', 'MneController@reviewheadass');
//controller function for view head lessonnote attempt on MNE for class
Route::post('/h_mnereviewlsn', 'MneController@reviewheadlsn');
//redirect based on the user that logged in
Route::get('/mne_redirect','MneController@redirectMNE')->middleware('ckmealogger');


/**
Route::get('/mne_redirect',function() {

     $admin = session('mne_admin_type');
     $teaid = session('mne_admin_id');
     $schid = session('mne_admin_sch');

    if (!session()->has('mne_login')){
       return redirect('/mnelogin');
    }
    else if(session()->has('mne_login')) {
        
        if (session()->has('mne_teacher_id')){
            return redirect('/mne_tea_review');
         }
       
        if (session()->has('mne_head_id')){
            return redirect('/mne_head_review');
        }
        if ($admin === 0){ //if this user is a teacher
           session(['mne_teacher_id' => $teaid]);
           $class_ = DB::select("SELECT * FROM class WHERE tea_id = :id AND school_sch_id = :pass " , ["id" => $teaid,"pass" => $schid] );//get class details
            if (count($class_) > 0){
                   
                foreach ($class_ as $c){
                    session(['mne_tea_class' => $c->_TITLE]);    
                    session(['mne_tea_class_id' => $c->CLS_ID]);
                    
                }
            }
             //2. Get the Teacher's Name..
            $teachername_ = DB::select('CALL getTEAname_(?,?);', [$teaid,$schid]);
            if (count($teachername_) > 0){
                   
                foreach ($teachername_ as $t){
                    session(['mne_name' => $t->Namez]);    
                        
                }
                
            }
            //get school ID stuff
           $school_ = DB::select("SELECT * FROM school WHERE sch_id = :id " , ["id" => $schid] );
            if (count($school_) > 0){
                   
                foreach ($school_ as $s){
                    session(['mne_school' => $s->_NAME]);    
                    session(['mne_school_id' => $s->SCH_ID]);    
                }
            }
            return redirect('/mne_tea_review');
           }
      
       else if ($admin === 1){ //if this user is the head of school
           session(['mne_head_id' => $teaid]);
           
           $headname_ = DB::select("SELECT CONCAT(_LNAME,' ',_FNAME) as myname FROM school_head WHERE schhead_id = :id " , [ "id" => $teaid ] ); //get each pupil's PARTICULAR attendance 
           foreach ($headname_ as $hn){
               $headnam = $hn->myname;
           }
           session(['mne_name' => $headnam]);
           //get school ID stuff
           $school_ = DB::select("SELECT * FROM school WHERE sch_id = :id " , ["id" => $schid] );
            if (count($school_) > 0){
                   
                foreach ($school_ as $s){
                    session(['mne_school' => $s->_NAME]);    
                    session(['mne_school_id' => $s->SCH_ID]);    
                }
            }
           return redirect('/mne_head_review');
           }
           
          //2. get the school name this teacher belongs to.
            
           //2. END get the school name this teacher belongs to. 
           
    } 
});

 function() {
	// 
        if (!session()->has('mne_login')){
            return redirect('/mnelogin');
        }
        if (session()->has('mne_teacher_id')){
            return redirect('/mne_tea_review');
        }
        if (session()->has('mne_pupil_id')){
            return redirect('/mne_stu_review');
        }
        if (session()->has('mne_head_id')){
            return redirect('/mne_head_review');
        }
      
});
**/



//controller function for logout attempt
Route::get('/mneLogout', 'MneController@logout');

});
/********end School Measurement and Evaluation web + controller parts******/


/***testing ajax routes****/
Route::get('ajax',function(){
   return view('message');
});
Route::post('/getmsg','AjaxController@index');
/***END testing ajax routes****/
