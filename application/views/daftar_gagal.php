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

<!-- Main -->
<div id="main">
	<section class="main">
		<h1>Mendaftarkan Sekolah - Gagal!</h1>
		<?php
		if ($error = $this->session->error) {
			?>
			<div class="alert alert-danger" role="alert">
				<?=$error?>
			</div>
			<?php
		}else{
			redirect('daftar');
		}
		?>
		<a class="button" href="<?=site_url()?>daftar">Daftar</a>
	</section>
</div>
<?php
$this->load->view('_footer');