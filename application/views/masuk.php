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
		<h1>Masuk ke Halaman Dasbor</h1>
		<form method="post">
			<input type="hidden" name="recaptcha_response" id="recaptchaResponse">
			<?php
			if ($error = $this->session->error) {
				?>
				<div class="alert alert-danger" role="alert">
					<?=$error?>
				</div>
				<?php
			}
			?>
			<div class="mb-3">
				<label for="inputKS" class="form-label">Kode Sekolah</label>
				<input name="ks" type="text" class="form-control" id="inputKS" aria-describedby="kSHelp" autocomplete="off" maxlength="25" autofocus="">
				<div id="kSHelp" class="form-text">Masukkan kode sekolah. Misal: smpn1songgon.</div>
			</div>
			<div class="mb-3">
				<label for="inputEmail" class="form-label">Alamat Email</label>
				<input name="email" type="email" class="form-control" id="inputEmail" aria-describedby="emailHelp" autocomplete="off" maxlength="255">
				<div id="emailHelp" class="form-text">Masukkan Alamat Email.</div>
			</div>
			<div class="mb-3">
				<label for="inputPassword" class="form-label">Password</label>
				<input name="password" type="password" class="form-control" id="inputPassword" autocomplete="off">
			</div>
			<button type="submit" class="button primary">Masuk</button>
		</form>
	</section>
</div>
<script src="https://www.google.com/recaptcha/api.js?render=<?=$this->config->item('recaptcha_sitekey', 'recaptcha')?>"></script>
<script>
	grecaptcha.ready(function () {
		grecaptcha.execute('<?=$this->config->item('recaptcha_sitekey', 'recaptcha')?>', { action: 'contact' }).then(function (token) {
			var recaptchaResponse = document.getElementById('recaptchaResponse');
			recaptchaResponse.value = token;
		});
	});
</script>
<?php
$this->load->view('_footer');