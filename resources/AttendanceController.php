<?php

namespace App\Http\Controllers;
use DatePeriod;
use DateTime;
use DateInterval;
use Illuminate\Http\Request;
use App\Users;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class AttendanceController extends Controller
{
    
    /**
     * Show the attendance module for the given user.
     *
     * @param  nothing
     * @return Response
     */
    public function index()
    {
        return redirect('/register_take');
    }
    public function login2(Request $request){

        $username = trim($request->input('att_username'));
        $password = md5($request->input('att_userpass'));
        
        //$user = DB::table('users')->where([ ['_user', '=', $username] ,['_pass', '=', $password] ])->first(); 
            $user = DB::select("SELECT * FROM users WHERE _user = :id AND _pass = :pass and _admin = :ad" , ["id" => $username,"pass" => $password,"ad" => 1] );
       if (count($user) > 0){
            session(['att_login_head' => '1']);
            
            //try and set the session objects for many things here!!!
            //1.get the class this teacher belongs to
            foreach ($user as $u){
               $teaid = $u->TEA_ID; //GET THIS TEACHER'S id
               $schid = $u->SCH_ID;// Get this teachers's school ID
            }
            session(['att_head_id' => $teaid]);
       
            //1.END get the class this teacher belongs to
          //2. get the school name this teacher belongs to.
          $school_ = DB::select("SELECT * FROM school WHERE sch_id = :id " , ["id" => $schid] );
          if (count($school_) > 0){
                 
              foreach ($school_ as $s){
                  session(['att_school' => $s->_NAME]);    
                  session(['att_school_id' => $s->SCH_ID]);    
              }
          }
        
        //2. END get the school name this teacher belongs to. 
           //3. Get the Teacher's Name..
           $headname_ = DB::select("SELECT CONCAT(_LNAME,' ',_FNAME) as myname FROM school_head WHERE schhead_id = :id " , [ "id" => $teaid ] ); //get each pupil's PARTICULAR attendance 
           foreach ($headname_ as $hn){
               $headnam = $hn->myname;
           }
           session(['att_head' => $headnam]);
           //3. END Get the Teacher's Name
           return redirect('/regdashboard'); 
        } 
        else{ 
             //login failed mehn!
             $request->session()->flash('loginfailed', '1');
            
             return view('att_login_admin');
         }
    }
    /**
     * Allow this user to try and login.(teacher)
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request){
        
        $username = trim($request->input('att_username'));
        $password = md5($request->input('att_userpass'));
        
        //$user = DB::table('users')->where([ ['_user', '=', $username] ,['_pass', '=', $password] ])->first(); 
            $user = DB::select("SELECT * FROM users WHERE _user = :id AND _pass = :pass AND _admin = :ad" , ["id" => $username,"pass" => $password,"ad" => 0] );
       if (count($user) > 0){
            session(['att_login' => '1']);
            
            //try and set the session objects for many things here!!!
            //1.get the class this teacher belongs to
            foreach ($user as $u){
               $teaid = $u->TEA_ID; //GET THIS TEACHER'S id
               $schid = $u->SCH_ID;// Get this teachers's school ID
            }
           
           session(['att_teacher_id' => $teaid]);
           
           $class_ = DB::select("SELECT * FROM class WHERE tea_id = :id AND school_sch_id = :pass " , ["id" => $teaid,"pass" => $schid] );
            if (count($class_) > 0){
                   
                foreach ($class_ as $c){
                    session(['att_class' => $c->_TITLE]);    
                    session(['att_class_id' => $c->CLS_ID]);
                    
                }
            }
            
          //1.END get the class this teacher belongs to
          //2. get the school name this teacher belongs to.
            $school_ = DB::select("SELECT * FROM school WHERE sch_id = :id " , ["id" => $schid] );
            if (count($school_) > 0){
                   
                foreach ($school_ as $s){
                    session(['att_school' => $s->_NAME]);    
                    session(['att_school_id' => $s->SCH_ID]);    
                }
            }
            //Get the school time of resumption
            $school_time = DB::select("SELECT * FROM school_time WHERE sch_id = :id " , ["id" => $schid] );
            if (count($school_time) > 0){
                   
                foreach ($school_time as $s){
                    $year = date('Y', strtotime($s->_RESUMEDATE));
                    session(['att_school_time' => $year]);
                    session(['att_school_term' => $s->_TERM]);    
                      
                }
            }
           //2. END get the school name this teacher belongs to. 
           //3. Get the Teacher's Name..
            $teachername_ = DB::select('CALL getTEAname_(?,?);', [$teaid,$schid]);
            if (count($teachername_) > 0){
                   
                foreach ($teachername_ as $t){
                    session(['att_teacher' => $t->Namez]);    
                        
                }
            }
             //get the pupils that belong to that Class and School...
            $pupilname_ = DB::select('CALL getPUPname_(?,?);', [session('att_class_id'),session('att_school_id')]);
            if (count($pupilname_) > 0){
            session(['att_pupil' => $pupilname_ ]);    
            }
            //generate register id
            $att_id = base_convert(hash('md5', mt_rand(10, 10000)),10,36);
            session(['att_genid' => $att_id]); 
            
            $morning = strtotime("08:00");
            $afternoon = strtotime("11:00");
            $close = strtotime("13:00");
            $timenow = strtotime(date('H:i'));
            
            if ($timenow <= $morning){
                $text = "MORNING";
                session(['att_time' => $text]); 
            }
            else if ($timenow > $morning && $timenow <= $afternoon){
                $text = "BREAK";
                session(['att_time' => $text]); 
            }
            else if ($timenow > $afternoon && $timenow <= $close){
                $text = "CLOSE";
                session(['att_time' => $text]); 
            }
            else{
                $text = "CLOSED PERIOD";
                session(['att_time' => $text]);
            }
           //3. END Get the Teacher's Name 
            
            return redirect('/register_take');
        }
        
        else{
            //login failed mehn!
            $request->session()->flash('loginfailed', '1');
            
            return view('att_login');
        }
        
    }
    /**
     * Allow this user to choose the period he/she is taking the attendance.
     *
     * @param  Request  $request
     * @return View
     */
    public function reviewstudent(Request $request){
        $term = $request->input('att_st_term');
        $year = $request->input('att_st_year');
        //$attname_ = DB::select('SELECT * FROM attendance WHERE school_sch_id = :id AND tea_id = :tea AND class_id = :cls AND _desc = :des AND _datetime = :dat ', ["id"=>session('att_school_id'),"tea"=>session('att_teacher_id'),"cls"=>session('att_class_id')]);
        $results =  DB::table('attendance')->where([['school_sch_id', '=',session('att_school_id')],['tea_id', '=',session('att_teacher_id')],['class_id', '=',session('att_class_id')],['_desc', 'like','%'.$term.'%'],['_datetime', 'like','%'.$year.'%']])->get();
        
        if ($results === null){
            session()->flash('att.failed', "1");
            return redirect('/register_stselect');
        }
        
        $st_result = array();
        $pup_att_ = array();
        $pup_id_ = array();
       
        $i = 0; $ik = 0;
        
        foreach($results as $r){
            $st_result[$i] = $r->ATT_ID;//get the attendance that are for that Term and Year
        
            $i++;
        }
        
        $pup_id = DB::select("SELECT * FROM pupil WHERE class_id = :cls AND school_sch_id = :sch " , ["cls"=>session('att_class_id'),"sch"=>session('att_school_id')] );//get the pupil id that belong to that Class
        
        foreach($pup_id as $p){
            $pup_id_[$ik] = $p->PUP_ID;//the individual ID of the Pupil ID
        
            $ik++;
        }
      
        for ($k = 0;$k < count($st_result);$k++){
        
        $pup_att_[$k] = DB::select("SELECT * FROM rowcall WHERE ATT_ID = :id " , ["id" => $st_result[$k]] ); //get each pupil's PARTICULAR attendance 
        
        }
          
          session()->flash('att.myids', $st_result);//get the att_id of the search result
          session()->flash('att.pupid', $pup_id_);  //get the pupil ID of those in that class
          session()->flash('att.attid', $pup_att_); //get the rowcall array data based on search result
          session()->flash('att.review.term', $term);
          session()->flash('att.review.year', $year);
          //session()->flash('att.results', $results);//get the 
         
        return redirect('/register_stview');
        
    }
    /**
     * .
     *
     * @param  Request  $request
     * @return View
     */
    private function date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d' ) {

    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);

    while( $current <= $last ) {

        $dates[] = date($output_format, $current);
        $current = strtotime($step, $current);
    }

    return $dates;
    }
    /**
     * .
     *
     * @param  Request  $request
     * @return View
     */
    public function reviewcalendar(Request $request){
        $term = $request->input('att_st_term');
        $year = $request->input('att_st_year');
        
        $te = array("1st Term"=>"1","2nd Term"=>"2","3rd Term"=>"3");
        
        $results = DB::table('attendance')->where([['school_sch_id', '=',session('att_school_id')],['tea_id', '=',session('att_teacher_id')],['class_id', '=',session('att_class_id')],['_desc', 'like','%'.$term.'%'],['_datetime', 'like','%'.$year.'%']])->get();
        
        $cal_result = array();
        $i = 0;
        
        foreach($results as $r){
            $cal_result[$i] = $r->_DATETIME;//get the days he did take attendance
        
            $i++;
        }
         //get resumption date
        $resume_ = DB::select("SELECT _RESUMEDATE FROM school_time WHERE _TERM = :t AND SCH_ID = :sch ",["t" => $te[$term], "sch" => session('att_school_id')]);
        
        if (empty($resume_)) {
            session()->flash('att.failed.2', "1");
            return redirect('/register_calselect');
        }
        
        
        foreach($resume_ as $r){
             $resume_date = $r->_RESUMEDATE;
        }
        
        
        
       // $mydaterange = date_range($resume_date,date('Y-m-d'));//get the range of days...till the present date
        $myrange = new DatePeriod(
            new DateTime($resume_date),
            new DateInterval('P1D'),
            new DateTime(date('Y-m-d'))
        );
        $mydaterange = iterator_to_array($myrange);
        //set the range of days + actual days he took the attendance...
        session(['att.calendar.range' => $mydaterange]);
        session(['att.calendar.days' => $cal_result]);
        //
         return redirect('/register_calview');
    }
    
    /**
     * Allow this user to choose the period he/she is taking the attendance.
     *
     * @param  Request  $request
     * @return View
     */
    public function proceed(Request $request){
         $time =  $request->input('att_time');
         $att_id = base_convert(hash('md5', mt_rand(10, 10000)),10,36);
        
         //get the pupils that belong to that Class and School...
         $pupilname_ = DB::select('CALL getPUPname_(?,?);', [session('att_class_id'),session('att_school_id')]);
            if (count($pupilname_) > 0){
            session(['att_pupil' => $pupilname_ ]);    
          }
         session(['att_time' => $time]); 
         session(['att_genid' => $att_id]); 
         
         return redirect('/register_take');
    }
    /**
     * Allow this user to edit his profile.
     *
     * @param  Request  $request
     * @return View
     */
    public function editprofile(Request $request){
      
        $email =  $request->input('emailx');
        $ph =  $request->input('phonex');
        DB::table('users')->where('TEA_ID', session('att_teacher_id'))->update(['_EMAIL' =>  $email,'_PNUM' => $ph ]);
       //Users::where('TEA_ID', session('att_teacher_id'))->update(['_EMAIL' =>  $email,'_PNUM' => $ph ]);
        session()->flash('att.editprofile','1');
        return redirect('/regdashboard_profile');
   }
     /**
     * Allow this user to submit the attendance.
     *
     * @param  Request  $request
     * @return View
     */
    public function submitattendance(Request $request){
         $picture = "";
         if (null != $request->input('picblob')){
              $picture = $request->input('picblob');
         }
         
         $preab = array();//store those present pupil ID
         $preid = array();//store the remarks from html form
         $preabs = array();//store the absentees pupil ID
         
         $present = $request->input('present');//get those present in checkbox array..
         $presentx = implode(";",$present);//turn into a concatenated string
         $presentarray = explode(";", $presentx);//break string above into an array...
         
         $totalpupils = session('att_pupil');
         $i = 0;
         foreach ($totalpupils as $to) {
            if (in_array($to->PUP_ID,$presentarray)){
                $preab[$i] = $to->PUP_ID;
                $i++;
                //those present
            }
         }
         $j = 0;
         foreach ($totalpupils as $to) {
            if (!in_array($to->PUP_ID,$presentarray)){
                $preabs[$j] = $to->PUP_ID;
                $j++;
                //those absent
            }
         }
         $k = 0;
         foreach ($preabs as $to) {
                $rem = "stud".$to;
                $preid[$k] = $request->input($rem);
                $k++;
                //remarks about absentees
         }
          session(['att_pupil_test' => $preab ]);    
          session(['att_pupil_test2' => $preid ]);    
         
         $period = array('MORNING'=>'M','BREAK'=>'B','CLOSE'=>'C','CLOSED PERIOD'=>'CP');
         $period2 = array('1'=>"1ST TERM",'2'=>"2ND TERM",'3'=>"3RD TERM");
         
        //get the week the school has begun in based on the resume date...
         $time = DB::select('CALL getSCHweek_(?);', [session('att_school_id')]);
         foreach ($time as $t){
            $weeksout = $t->weeksout;
            $term = $t->_TERM;
         }
         
         $attend = new \App\Attendance;
         $attend->_DATETIME = date("Y-m-d H:i:s");
         $attend->_PERIOD = $period[session('att_time')];
         $attend->_DESC = "WEEK ".$weeksout." ".$period2[$term];
         $attend->_IMAGE = $picture;
         $attend->CLASS_ID = session('att_class_id');
         $attend->TEA_ID = session('att_teacher_id');
         $attend->SCHOOL_SCH_ID = session('att_school_id');   
         $attend->save();//save into attendance table
         
         $att_id = DB::select("SELECT MAX(ATT_ID) As Att FROM attendance WHERE TEA_ID = :tea",["tea"=>session('att_teacher_id')]);
         
         foreach($att_id as $at){
             $att_idd = $at->Att;
         }
         $m = 0;
         foreach ($totalpupils as $to) {
            if (in_array($to->PUP_ID,$presentarray)){
         $rowcall = new \App\RowCall;
         $rowcall->ATT_ID = $att_idd; 
         $rowcall->PUPIL_ID = $to->PUP_ID;
         $rowcall->PUPIL_NAME = $to->Namez;
         $rowcall->_STATUS = '1';
         $rowcall->_REMARK = " ";
         $rowcall->save(); //store this pupil as 1 if he exists in the present array
                    }
           else{
         $rowcall = new \App\RowCall;
         $rowcall->ATT_ID = $att_idd; 
         $rowcall->PUPIL_ID = $to->PUP_ID;
         $rowcall->PUPIL_NAME = $to->Namez;
         $rowcall->_STATUS = '0';
         if ($preid[$m] !== null){
         $rowcall->_REMARK = $preid[$m];}
         else{
         $rowcall->_REMARK = "no excuse";    
         }
         $rowcall->save(); //store this pupil as 0 if he not exists in the present array  
          $m++; }         
                }
         $request->session()->flash('att_submit',1);
         return redirect('/register_take');
    }
    /**
     * Allow this user to logout from attendance module.
     *
     * @param  Request  $request
     * @return View
     */
    public function logout(Request $request){
        $request->session()->forget('att_login');
        $request->session()->forget('att_login_head');
        $request->session()->forget('att_submit');
        $request->session()->forget('att_pupil');
        $request->session()->forget('att_pupil_test');
        $request->session()->forget('att_pupil_test2');
        $request->session()->forget('att_class');
        $request->session()->forget('att_class_id');
        $request->session()->forget('att_school');
        $request->session()->forget('att_school_id');
        $request->session()->forget('att_time');
        $request->session()->forget('att_genid');
        $request->session()->forget('att_teacher');
        $request->session()->forget('att_head');
        $request->session()->forget('att_head_id');
        $request->session()->forget('att_teacher_id');
        return redirect('/register');
    }
}

