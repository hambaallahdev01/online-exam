<?php
$this->load->view('_header');
?>

<!-- Header -->
<header id="header" class="alt">
	<span class="logo"><img src="<?=site_url()?>front-template/images/logo.svg" alt="" /></span>
	<h1>Ujian Online</h1>
	<ltr>
		بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم<br />
		اَللَّهُمَّ صَلِّ عَلَى سَيِّدِنَا مُحَمَّدٍ<br />
		اللهم انصر واحفظ و ايد جاكا د صلوات في العلمين بحق سيدنا محمد ص.م<br />
	</ltr>
	<p>Hanya aplikasi ujian online biasa yang <br />
	dihadirkan oleh Achmad ~ Pipit</p>
</header>

<!-- Nav -->
<nav id="nav">
	<ul>
		<li><a href="#intro" class="active">Perkenalan</a></li>
		<li><a href="#cta">Siap, Mulai!</a></li>
	</ul>
</nav>

<!-- Main -->
<div id="main">

	<!-- Introduction -->
	<section id="intro" class="main">
		<div class="spotlight">
			<div class="content">
				<header class="major">
					<h2>Perkenalan</h2>
				</header>
				<p>Aplikasi ini terinspirasi oleh Pak Wong.</p>
				<h3>Fitur Pertanyaan:</h3>
				<ol>
					<li>Pilihan Ganda</li>
					<li>Pilihan Banyak</li>
					<li>Uraian</li>
					<li>Benar - Salah</li>
					<li>Fakta - Opini</li>
					<li>Mencocokkan</li>
					<li>Mengurutkan</li>
				</ol>
			</div>
			<span class="image"><img src="<?=site_url()?>inspirasi.gif" alt="" /></span>
		</div>
	</section>

	<!-- Get Started -->
	<section id="cta" class="main special">
		<header class="major">
			<h2>Ayo Mulai</h2>
			<p>Silahkan mendaftar dahulu atau masuk jika sudah daftar.</p>
		</header>
		<footer class="major">
			<ul class="actions special">
				<li><a href="<?=site_url()?>masuk" class="button primary">Masuk</a></li>
				<li><a href="<?=site_url()?>daftar" class="button">Daftar</a></li>
			</ul>
		</footer>
	</section>

</div>
<?php
$this->load->view('_footer');