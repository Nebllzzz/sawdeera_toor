 <!-- Sidebar Menu -->
 <nav class="mt-2">
     <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
         <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
         <li class="nav-item">
             <a href="/dashboard" class="nav-link">
                 <i class="nav-icon fas fa-home"></i>
                 <p>
                     Dashboard
                 </p>
             </a>
         </li>

         @php $role = auth()->user()->role; @endphp

         @if ($role === 'jemaah')
         <li class="nav-item">
             <a href="/profile" class="nav-link">
                 <i class="nav-icon fas fa-user"></i>
                 <p>
                     Profile
                 </p>
             </a>
         </li>
         @endif

         @if ($role === 'admin')
             <ul class="nav nav-pills nav-sidebar flex-column">

                 <li class="nav-item">
                     <a href="/user" class="nav-link">
                         <i class="nav-icon fas fa-user-shield"></i>
                         <p>Data Admin</p>
                     </a>
                 </li>

             </ul>
         @endif

         @if (in_array($role, ['admin', 'operator']))
             <ul class="nav nav-pills nav-sidebar flex-column">

                 <li class="nav-item">
                     <a href="/jemaah" class="nav-link">
                         <i class="nav-icon fas fa-users"></i>
                         <p>Data Jemaah</p>
                     </a>
                 </li>

                 <li class="nav-item">
                     <a href="/hotel" class="nav-link">
                         <i class="nav-icon fas fa-hotel"></i>
                         <p>Data Hotel</p>
                     </a>
                 </li>

                 <li class="nav-item">
                     <a href="/paket-umrah" class="nav-link">
                         <i class="nav-icon fas fa-box"></i>
                         <p>Data Paket Umrah</p>
                     </a>
                 </li>

                 <li class="nav-item">
                     <a href="/maskapai" class="nav-link">
                         <i class="nav-icon fas fa-plane"></i>
                         <p>Data Maskapai</p>
                     </a>
                 </li>

                 <li class="nav-item">
                     <a href="/tour-leader" class="nav-link">
                         <i class="nav-icon fas fa-user-tie"></i>
                         <p>Tour Leader</p>
                     </a>
                 </li>

                 <li class="nav-item">
                     <a href="/keberangkatan" class="nav-link">
                         <i class="nav-icon fas fa-plane-departure"></i>
                         <p>Jadwal Keberangkatan</p>
                     </a>
                 </li>

                 <li class="nav-item">
                     <a href="/admin/dokumen" class="nav-link">
                         <i class="nav-icon fas fa-file-alt"></i>
                         <p>Verifikasi Dokumen</p>
                     </a>
                 </li>

             </ul>
         @endif

         @if ($role === 'jemaah')
             <ul class="nav nav-pills nav-sidebar flex-column">

                 <li class="nav-item">
                     <a href="/dokumen" class="nav-link">
                         <i class="nav-icon fas fa-file-upload"></i>
                         <p>Upload Dokumen</p>
                     </a>
                 </li>

                 <li class="nav-item">
                     <a href="/keberangkatan-jemaah" class="nav-link">
                         <i class="nav-icon fas fa-plane"></i>
                         <p>Keberangkatan Saya</p>
                     </a>
                 </li>

                 <li class="nav-item">
                     <input type="hidden" id="hasActiveJadwal" value="{{ $hasActiveJadwal ?? 0 }}">
                 </li>

             </ul>
         @endif

     </ul>
 </nav>
 <!-- /.sidebar-menu -->
