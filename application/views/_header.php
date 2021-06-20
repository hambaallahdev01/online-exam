<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="shortcut icon" href="<?=base_url('assets/template/')?>img/favicon.png">

	<title><?=$this->breadcrumb->get_last_title()?> - Cokro</title>

	<!-- Icons -->
	<link href="<?php echo base_url('assets/template/css/font-awesome.min.css');?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/template/css/simple-line-icons.css');?>" rel="stylesheet">

	<!-- Main styles for this application -->
	<link href="<?php echo base_url('assets/template/css/style.css');?>" rel="stylesheet">

	<!-- datatable -->
	<link rel="stylesheet" href="<?php echo base_url('assets/css/jquery.dataTables.min.css');?>">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">

	<!-- select2 -->
	<link rel="stylesheet" href="<?php echo base_url('assets/select2/css/select2.min.css');?>">

	<!-- clockpicker -->
	<link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap-clockpicker.min.css');?>">

	<!-- datepicker -->
	<link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap-datepicker3.min.css');?>">

	<!-- stars -->
	<link rel="stylesheet" href="<?php echo base_url('assets/css/rating.min.css');?>">

	<link rel="stylesheet" href="<?php echo base_url('assets/daterangepicker/daterangepicker.css');?>">

	<link rel="stylesheet" href="<?php echo base_url('assets/fullcalendar/fullcalendar.min.css');?>">

	<!-- js jquery -->
	<script src="<?php echo base_url('assets/template/bower_components/jquery/dist/jquery.min.js');?>" type="text/javascript"></script>

	<script src="<?php echo base_url('assets/js/');?>jquery.maskMoney.min.js" type="text/javascript"></script>
	<!-- stars -->
	<script src="<?php echo base_url('assets/js/rating.min.js');?>" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">
	if (location.hostname !== "localhost" && location.protocol !== 'https:') {
	  location.replace(`https:${location.href.substring(location.protocol.length)}`);
	}
		var base_url = "<?=base_url()?>";
	</script>

	<!-- custom.css -->
	<link rel="stylesheet" href="<?= base_url('assets/template/css/custom.css?v=1')?>">

	<?php
	if ($this->router->class == 'blog') {
		?>
		<!-- include summernote css/js -->
		<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-lite.min.css" rel="stylesheet">
		<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.16/dist/summernote-lite.min.js"></script>
		<?php
	}
	?>
	
</head>

<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
	<header class="app-header navbar">
		<button class="navbar-toggler mobile-sidebar-toggler d-lg-none" type="button">☰</button>
		<a class="navbar-brand" target="_blank" href="<?=base_url()?>" style="background-image: url('<?php echo base_url('/assets/image/logo/logo.svg');?>')"></a>
		<ul class="nav navbar-nav d-md-down-none">
			<li class="nav-item">
				<a class="nav-link navbar-toggler sidebar-toggler" href="#">☰</a>
			</li>
		</ul>
		<ul class="nav navbar-nav ml-auto">
			<?php
			// print_r($this->session);
			$nama_admin = $this->session->logged_in['nama_admin'];
			$foto_profile = $this->session->logged_in['foto_profil'];
			$jabatan = 'Admin';
			$notifications = new stdClass();
			$notifications->result = [];
			$notifications->count = 0;
			?>
			<li class="nav-item dropdown">
				<a class="nav-link nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
					<i class="icon-bell"></i><span class="badge badge-pill badge-danger" id="total-notifikasi"><?php echo $notifications->count?></span>
				</a>
				<div class="dropdown-menu dropdown-menu-right" id="list-notifikasi">
					<?php
					foreach ($notifications->result as $notif) {
						$terbaca = 'not-read';
						if ($notif->terbaca == 1) {
							$terbaca = 'was-read';
						}
						echo '<a class="dropdown-item '.$terbaca.'" href="'.$notif->href.'"><i class="fa fa-tasks"></i> '.$notif->isi.'</a>';
					}
					?>
					<div class="user-footer d-flex justify-content-between align-items-center">
						<a class="dropdown-item" href="#" onclick="set_baca_notifikasi(1)">Hapus yang telah dibaca</a>
						<a class="dropdown-item" href="#" onclick="set_baca_notifikasi(0)">Hapus semua notifikasi</a>
					</div>
				</div>
			</li>

			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
					<!-- image placeholder -->
					<!-- <img src="https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png" class="img-avatar" alt="User"> -->
					<span class="d-md-down-none"><?=$nama_admin?> (<?=$jabatan?>)</span>
				</a>
				<div class="dropdown-menu dropdown-menu-right">

          <!-- <div class="dropdown-header text-center">
            <strong>Account</strong>
        </div> -->
        <div class="user-header d-flex justify-content-center align-items-center text-center">
        	<div class="user-header__wrapper"> 
        		<img src="<?=$foto_profile?>" class="img-avatar" alt="Profile" style="height: auto !important;">
        		<p><?=$nama_admin?></p>
        	</div>
        </div>
        <div class="user-footer d-flex justify-content-between align-items-center m-row-column">
        	<a class="dropdown-item text-center c-mw-1" href="<?php echo base_url('profil');?>"><i class="fa fa-user"></i> Profil</a>
        	<a class="dropdown-item text-center c-mw-1" id="for-logout" href="<?php echo base_url('adminbism/logout');?>"><i class="fa fa-sign-out"></i> Keluar</a>
        </div>

    </div>
</li>
</ul>
</header>
<div class="app-body">
	<div class="sidebar">
		<nav class="sidebar-nav">
			<ul class="nav">
				<li class="nav-item"><a class="nav-link" href="<?=base_url('dashboard')?>"><i class="icon-speedometer"></i> Dashboard </a></li>
				<li class="nav-item"><a class="nav-link" href="<?=base_url('mitra')?>"><i class="icon-badge"></i> Data Mitra</a></li>
				<li class="nav-item"><a class="nav-link" href="<?=base_url('customer')?>"><i class="icon-people"></i> Data Customer</a></li>
				<li class="nav-item"><a class="nav-link" href="<?=base_url('fasilitas')?>"><i class="icon-puzzle"></i> Data Fasilitas</a></li>
				<li class="nav-item"><a class="nav-link" href="<?=base_url('properti')?>"><i class="icon-home"></i> Data Properti</a></li>
				<li class="nav-item"><a class="nav-link" href="<?=base_url('transaksi')?>"><i class="icon-briefcase"></i> Data Transaksi</a></li>
				<li class="nav-item"><a class="nav-link" href="<?=base_url('booking')?>"><i class="icon-basket"></i> Data Booking</a></li>
				<li class="nav-item"><a class="nav-link" href="<?=base_url('blog-kategori')?>"><i class="icon-docs"></i> Data Kategori Blog</a></li>
				<li class="nav-item"><a class="nav-link" href="<?=base_url('blog')?>"><i class="icon-docs"></i> Data Blog</a></li>
				<li class="nav-item"><a class="nav-link" href="<?=base_url('kampus')?>"><i class="icon-graduation"></i> Data Kampus</a></li>
				<li class="nav-item"><a class="nav-link" href="https://dashboard.tawk.to/login" target="_blank"><i class="icon-bubbles"></i> Pesan</a></li>
				<li class="nav-item"><a class="nav-link" href="<?=base_url('newsletter')?>"><i class="icon-envelope-letter"></i> Newsletter</a></li>
				<li class="nav-item"><a class="nav-link" href="<?=base_url('user-account')?>"><i class="icon-user-following"></i> Akun Pengguna</a></li>
				<li class="nav-item"><a class="nav-link" href="<?=base_url('promo')?>"><i class="icon-present"></i> Promo</a></li>
				<li class="nav-item"><a class="nav-link" href="<?=base_url('refund')?>"><i class="icon-briefcase"></i> Permintaan Refund</a></li>

				<li class="nav-item nav-dropdown">
					<a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-grid"></i> PPOB</a>
					<ul class="nav-dropdown-items">
						<li class="nav-item">
							<a class="nav-link" href="<?php echo base_url('produk-alterra'); ?>"><i class="icon-docs"></i> Produk Alterra</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo base_url('admin-fee'); ?>"><i class="icon-calculator"></i> Admin Fee</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo base_url('bukti-bayar'); ?>"><i class="icon-doc"></i> Bukti Bayar</a>
						</li>
					</ul>
				</li>

				<!-- <li class="divider"></li>
				<li class="nav-title">
					PENGATURAN
				</li>
				<li class="nav-item nav-dropdown">
					<a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-grid"></i> Setup master admin</a>
					<ul class="nav-dropdown-items">
						<li class="nav-item">
							<a class="nav-link" href="<?php echo base_url('admin/user'); ?>"><i class="icon-user"></i> Master user</a>
						</li>
					</ul>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo base_url('admin/profil'); ?>"><i class="icon-user"></i> Profil</a>
				</li> -->
				<li class="nav-item nav-dropdown">
					<a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-grid"></i> Keuangan</a>
					<ul class="nav-dropdown-items">
						<li class="nav-item">
							<a class="nav-link" href="<?php echo base_url('keuangan'); ?>"><i class="icon-bag"></i> Tagihan</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo base_url('keuangan-riwayat'); ?>"><i class="icon-cursor"></i> Riwayat Pembayaran</a>
						</li>
					</ul>
				</li>
				<li class="nav-item"><a class="nav-link" href="<?=base_url('iklan')?>"><i class="icon-fire "></i> Iklan</a></li>
			</ul>
		</nav>
	</div>

	<!-- Main content -->
	<main class="main">

		<?php
		echo $this->breadcrumb->render();
		?>

		<!-- Alert Success & Error -->
		<!-- uncomment -->

		<div class="alert alert-c bg-success alert-dismissible fade show" id="alert-success" role="alert">
			<button type="button" class="close" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			Hapus data berhasil.
		</div>
		<!-- or -->
		<div class="alert alert-c bg-danger alert-dismissible fade show" id="alert-error" role="alert">
			<button type="button" class="close" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			Terjadi kesalahan, hapus data tidak berhasil.
		</div>

		<?php
		if ($error = $this->session->error) {
			?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<strong>Gagal!</strong> <?=$error?>
			</div>
			<?php
		}		
		?>

		<?php
		if ($info = $this->session->info) {
			?>
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<strong>Sukses!</strong> <?=$info?>
			</div>
			<?php
		}		
		?>

		<script>
			$('.alert').on('click','.close',function(){
				$(this).closest('.alert').slideUp();
			});
		</script>

		<!-- break -->
		<div class="mb-4"></div>
		<div class="container-fluid" style="overflow-y: auto;">