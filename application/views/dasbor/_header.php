<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="initial-scale=1, minimum-scale=1, width=device-width" name="viewport">
	<meta name="robots" content="all,follow">
	<title>DASBOR - Ujian Online by Achmad ~ Pipit</title>

	<link rel="apple-touch-icon" sizes="180x180" href="<?=site_url()?>apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?=site_url()?>favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?=site_url()?>favicon-16x16.png">
	<link rel="manifest" href="<?=site_url()?>site.webmanifest">
	<link rel="mask-icon" href="<?=site_url()?>safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">

	<!-- inject:css -->
	<link rel="stylesheet" href="<?=site_url()?>admin-template/vendors/fomantic-ui/semantic.min.css">
	<link rel="stylesheet" href="<?=site_url()?>admin-template/css/main.css">
	<!-- endinject -->
	<script src="<?=site_url()?>admin-template/vendors/jquery/jquery.min.js"></script>
	<!-- chartjs:css -->
	<link rel="stylesheet" href="<?=site_url()?>admin-template/vendors/chart.js/Chart.min.css">
	<!-- endinject -->
</head>
<body>
	<div class="ui grid">
		<div class="row">
			<div class="ui grid">
				<!-- BEGIN NAVBAR -->
				<!-- computer only navbar -->
				<div class="computer only row">
					<div class="column">
						<div class="ui top fixed menu navcolor">
							<div class="item">
								<img src="<?=site_url()?>front-template/images/logo.svg" alt="SimpleIU">
							</div>
							<div class="left menu">
								<div class="nav item">
									<strong class="navtext">DASBOR</strong>
								</div>
							</div>
							<div class="ui top pointing dropdown admindropdown link item right">
								<img class="imgrad" src="<?=site_url()?>admin-template/images/user.png" alt="">
								<span class="clear navtext"><strong>Hay, <?=$this->session->sesi_login->nama_sekolah?></strong></span>
								<i class="dropdown icon navtext"></i>
								<div class="menu">
									<div class="item"><p>
										<a href="<?=site_url()?>dasbor/pengaturan/akun" class="item"><i class="settings icon"></i>Pengaturan Akun</a>
									</p></div>
									<a href="<?=site_url()?>admin-template/login.html" class="item"><i class="sign out alternate icon"></i> Logout</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- end computer only navbar -->
				<!-- mobile and tablet only navbar -->
				<div class="tablet mobile only row">
					<div class="column">
						<div class="ui top fixed menu navcolor">
							<a id="showmobiletabletsidebar" class="item navtext"><i class="content icon"></i></a>
							<div class="right menu">
								<div class="item">
									<strong class="navtext">DASBOR</strong>
								</div>
								<div class="item">
									<img src="<?=site_url()?>front-template/images/logo.svg">
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- end mobile and tablet only navbar -->
				<!-- END NAVBAR -->

				<!-- BEGIN SIDEBAR -->
				<!-- mobile and tablet only sidebar -->
				<div class="tablet mobile only row">
					<div id="mobiletabletsidebar" class="mobiletabletsidebar animate hidden">
						<div class="ui left fixed vertical menu scrollable">
							<div class="item">
								<table>
									<tr>
										<td>
											<img class="ui mini image" src="<?=site_url()?>front-template/images/logo.svg">
										</td>
										<td>
											<span class="clear"><strong>DASBOR</strong></span>
										</td>
									</tr>
								</table>
							</div>
							<a class="item" href="<?=site_url()?>admin-template/index.html"><i class="home icon"></i> Dashboard</a>
							<a class="item" href="<?=site_url()?>admin-template/table.html"><i class="table icon"></i> Table</a>
							<!-- Begin Simple Accordion -->
							<div class="ui accordion simpleaccordion item">
								<div class="title titleaccordion item"><i class="dropdown icon"></i> Settings</div>
								<div class="content contentaccordion">
									<a class="item itemaccordion" href="#"><i class="settings icon"></i> Account Setting</a>
									<a class="item itemaccordion" href="#"><i class="settings icon"></i> Site Setting</a>
								</div>
							</div>
							<!-- End Simple Accordion -->
							<a class="item"><i class="settings icon"></i> Settings</a>	
							<a href="<?=site_url()?>admin-template/login.html" class="item"><i class="sign out alternate icon"></i> Logout</a>
							<a class="item" href="https://fomantic-ui.com/"><i class="heart icon"></i>More Components</a>
							<div class="item" id="hidemobiletabletsidebar">
								<button class="fluid ui button">
									Close
								</button>
							</div>
						</div>
					</div>
				</div>
				<!-- end mobile and tablet only sidebar -->
				<!-- computer only sidebar -->
				<div class="computer only row">
					<div class="left floated three wide computer column" id="computersidebar">
						<div class="ui vertical fluid menu scrollable" id="simplefluid">
							<div class="clearsidebar"></div>
							<div class="item">
								<img src="<?=site_url()?>admin-template/images/user.png" id="sidebar-image">
							</div>
							<a class="item" href="<?=site_url()?>admin-template/index.html"><i class="home icon"></i> Dashboard</a>
							<a class="item" href="<?=site_url()?>admin-template/table.html"><i class="table icon"></i> Table</a>
							<!-- Begin Simple Accordion -->
							<div class="ui accordion simpleaccordion item">
								<div class="title titleaccordion item"><i class="dropdown icon"></i> Settings</div>
								<div class="content contentaccordion">
									<a class="item itemaccordion" href="#"><i class="settings icon"></i> Account Setting</a>
									<a class="item itemaccordion" href="#"><i class="settings icon"></i> Site Setting</a>
								</div>
							</div>
							<!-- End Simple Accordion -->
							<a href="<?=site_url()?>admin-template/login.html" class="item"><i class="sign out alternate icon"></i> Logout</a>
							<a class="item" href="https://fomantic-ui.com/"><i class="heart icon"></i>More Components</a>
							<a class="item"></a>
						</div>
					</div>
				</div>
				<!-- end computer only sidebar -->
				<!-- END SIDEBAR -->
			</div>
		</div>
		<!-- BEGIN CONTEN -->
		<div class="right floated thirteen wide computer sixteen wide phone column" id="content">
			<div class="ui container grid">
				<div class="row">
					<div class="fifteen wide computer sixteen wide phone centered column">
						<h2><i class="<?=$icon?>"></i> <?=$page?></h2>
						<div class="ui divider"></div>
						<div class="ui grid">