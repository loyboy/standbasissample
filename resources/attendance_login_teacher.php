<?php //-----THIS IS THE PAGE TO LOGIN FOR THE TEACHER IN ATTENDANCE ?>
<?php include 'includes/header.php' ?>
<?php include 'includes/sidebar_register.php' ?>
      
               
                <!-- BEGIN Attendance Login Form -->
         <div class="clearfix"></div>   
         <p></p>   
         <section class="flexbox-container" style="margin-bottom: 160px; margin-top: 80px;">
          <div class="col-12 d-flex align-items-center justify-content-center">

            <div class="col-md-4 col-10 box-shadow-2 p-0">
            
                 <?php
                if(session()->has('loginfailed')){
                            echo '<div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="fa fa-info-circle"></i>&nbsp;&nbsp;Your login attempt has failed.
                            </div>';
                
                }
                if(session()->has('regsuccess')) {
                            echo '<div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="fa fa-info-circle"></i>&nbsp;&nbsp;Your login attempt was successful. 
                            </div>';
                
                }?>
              <div class="row">
                <div class="card-body">
                <a href="<?php echo url('/attlogin2'); ?>" class="btn btn-block btn-primary"> Click here to Log In as a School Head</a>
                </div>
              </div>

              <div class="card border-grey border-lighten-3 m-0">
                <div class="card-header border-0">
                  <div class="card-title text-center">
                    <div class="p-1">
                      <img src="" alt="branding logo">
                    </div>
                  </div>
                  <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                    <span>Login into Standbasis Register</span>
                  </h6>
                </div>
                <div class="card-content">
                  <div class="card-body">
                    <form class="form-horizontal form-simple" role="form" method="post" accept-charset="UTF-8" action=<?php echo action('AttendanceController@login'); ?>>
                      <?php echo csrf_field(); ?>
                      <fieldset class="form-group position-relative has-icon-left mb-0">
                        <input type="text" name="att_username" class="form-control form-control-lg input-lg" id="user-name" placeholder="Your Username"
                        required>
                        <div class="form-control-position">
                          <i class="ft-user"></i>
                        </div>
                      </fieldset>
                      <fieldset class="form-group position-relative has-icon-left">
                        <input type="password" name="att_userpass" class="form-control form-control-lg input-lg" id="user-password"
                        placeholder="Enter Password" required>
                        <div class="form-control-position">
                          <i class="fa fa-key"></i>
                        </div>
                      </fieldset>
                      <div class="form-group row">
                        <div class="col-md-6 col-12 text-center text-md-left">
                          <fieldset>
                            <input type="checkbox" id="remember-me" class="chk-remember">
                            <label for="remember-me"> Remember Me</label>
                          </fieldset>
                        </div>
                       </div>
                      <button type="submit" class="btn btn-primary btn-lg btn-block"><i class="ft-unlock"></i> Login</button>
                    </form>
                  </div>
                </div>
             
              </div>
            </div>
          </div>
        </section>
                 <!-- END Login Form -->
          <!--  </div>
        </div> -->




<?php include 'includes/footer.php'; 
 //}  //else{
    
   // header("Location: main/att_home.php");
//} 




