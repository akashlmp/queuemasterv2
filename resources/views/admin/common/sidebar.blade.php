   <!-- Sidebar Start -->
  
   <div class="sidebar pe-4 pb-3">
       <nav class="navbar bg-light navbar-light">
           <a href="index.html" class="navbar-brand mx-4 mb-3">
               <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>DASHMIN</h3>
           </a>
           <div class="d-flex align-items-center ms-4 mb-4"> </div>
           <div class="navbar-nav w-100">
               <a href="{{ route('admin-index')}}" class="nav-item nav-link active"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
               <a href="{{ url('admin/user/profile/update')}}" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Update Profile</a>

               <!-- <a href="{{ url('admin/developers-index')}}" class="nav-item nav-link"><i class="fa fa-users me-2"></i>Developers</a> -->

               <a href="{{ url('admin/subscription-plan')}}" class="nav-item nav-link"><i class="fa fa-bell me-2"></i>Subscription Plan</a>
               <a href="{{ url('admin/addTermOfUse')}}" class="nav-item nav-link"><i class="fa fa-bell me-2"></i>Terms of
               use</a>
                <a href="{{ url('admin/addPrivacyPolicy')}}" class="nav-item nav-link"><i class="fa fa-bell me-2"></i>Privacy Policy</a>
           </div>
       </nav>
   </div>
   <!-- Sidebar End -->