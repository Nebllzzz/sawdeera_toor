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
         <li class="nav-item">
             <a href="/profile" class="nav-link">
                 <i class="nav-icon fas fa-user"></i>
                 <p>
                     Profile
                 </p>
             </a>
         </li>
         {{-- menu utama --}}
         <li class="nav-item">
             <a href="#" class="nav-link">
                 <i class="nav-icon fas fa-table"></i>
                 <p>
                     Master Data
                     <i class="fas fa-angle-left right"></i>
                 </p>
             </a>
             <ul class="nav nav-treeview">
                 <li class="nav-item">
                     <a href="/user" class="nav-link">
                         <i class="far fa-circle nav-icon"></i>
                         <p>Data User</p>
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="/jemaah" class="nav-link">
                         <i class="far fa-circle nav-icon"></i>
                         <p>Data Jemaah</p>
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="/hotel" class="nav-link">
                         <i class="far fa-circle nav-icon"></i>
                         <p>Data Hotel</p>
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="/paket-umrah" class="nav-link">
                         <i class="far fa-circle nav-icon"></i>
                         <p>Data Paket Umrah</p>
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="/maskapai" class="nav-link">
                         <i class="far fa-circle nav-icon"></i>
                         <p>Data Maskapai</p>
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="/tour-leader" class="nav-link">
                         <i class="far fa-circle nav-icon"></i>
                         <p>Data Toor Leader</p>
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="/keberangkatan" class="nav-link">
                         <i class="far fa-circle nav-icon"></i>
                         <p>Data Jadwal Keberangkatan</p>
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href="/admin/dokumen" class="nav-link">
                         <i class="far fa-circle nav-icon"></i>
                         <p>Verifikasi Dokumen Jemaah</p>
                     </a>
                 </li>
             </ul>
         </li>
         {{-- menu utama --}}
         <li class="nav-item">
             <a href="#" class="nav-link">
                 <i class="nav-icon fas fa-table"></i>
                 <p>
                     Master Data
                     <i class="fas fa-angle-left right"></i>
                 </p>
             </a>
             <ul class="nav nav-treeview">
                 <li class="nav-item">
                     <a href="/dokumen" class="nav-link">
                         <i class="far fa-circle nav-icon"></i>
                         <p>Upload Dokumen</p>
                     </a>
                 </li>
             </ul>
         </li>
     </ul>
 </nav>
 <!-- /.sidebar-menu -->
