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
		<h1>Mendaftarkan Sekolah - Sukses</h1>
		<?php
		if ($nama_sekolah = $this->session->nama_sekolah) {
			?>
			<div class="alert alert-success" role="alert">
				Terima kasih <?=$nama_sekolah?> telah mendaftar, selanjutnya silahkan klik tombol di bawah, untuk masuk ke dasbor.
			</div>
			<?php
		}else{
			redirect('daftar');
		}
		?>
		<a class="button primary" href="<?=site_url()?>masuk">Masuk</a>
	</section>
</div>
<?php
$this->load->view('_footer');