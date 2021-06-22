<?php
$this->data['page'] = 'Pengaturan Akun';
$this->load->view('dasbor/_header.php', $this->data);
?>
<div class="sixteen wide computer sixteen wide phone centered column">
	<div class="ui raised segment">
		<div class="content">
			<div class="ui centered grid">
				<div class="row">
					<div class="sixteen wide computer column">
						<form class="ui form" method="post" enctype="multipart/form-data">
							<div class="field">
								<label>Nama Sekolah</label>
								<input type="text" name="ns" value="<?=$nama_sekolah?>">
							</div>
							<div class="field">
								<label>Email</label>
								<input type="email" name="email" value="<?=$email?>">
							</div>
							<div class="field">
								<label>Ganti Logo - Pilih hanya jika ingin mengganti</label>
								<input type="file" name="logo">
							</div>
							<div class="field">
								<label>Jika tidak terjadi perubahan, silahkan keluar dasbor, dan masuk dasbor lagi.</label>
							</div>
							<button class="ui button" type="submit">Simpan</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$this->load->view('dasbor/_footer');