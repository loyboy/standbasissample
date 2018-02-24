<!--raings massive content page -->
@include ('includes/header')
    <body>
   @include ('functions/ratings/index')
  @include ('includes/sidebar_ratings')
   <!-- Main Container -->         
  <div class="app-content container center-layout mt-2">
    <div class="content-wrapper">

      <!-- Right content -->
      <div class="content-detached content-right">
        <div class="content-body">

           <!-- Add Menu Header -->
           @include ('includes/sidebar_ratings_nav')
           <!-- end Menu Header -->
           
          <!-- Ist DIV -->
          <section id="ratingsapp" class="card" style="padding:20px;">
           
            @include ('pageparts/ratings/schools')
                                    
            @if(session()->has('profile_sch')) 
                                    
            @include ('pageparts/ratings/schprofile') 
                                    
            @endif
          </section>
         <!-- end Ist DIV -->     
        </div>
      </div>
      <!--end Right content -->

      <!-- Left content -->
       @include ('includes/ratings_stick_left') 
      <!-- end Left content -->
    
    </div>
  </div> 
   <!-- end Main Container -->        
        
      
@include ('includes/footer')
       
