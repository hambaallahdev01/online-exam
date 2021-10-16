<?php
$this->data['page'] = 'Kelas';
$this->data['icon'] = 'chalkboard teacher icon';
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
								<label>Jenjang</label>
								<select name="jenjang" autofocus>
									<?php
									for ($i=1; $i < 13; $i++) { 
									?>
										<option><?=$i?></option>
									<?php
									}
									?>
								</select>
							</div>
							<div class="field">
								<label>Nama Kelas</label>
								<input type="text" name="nama_kelas">
							</div>
							<div class="field">
								<label>Misal kelas "3 Pattimura", maka Jenjang isi: 3, Nama Kelas isi: Pattimura. Atau jika hanya kelas 1, maka isi Jenjang: 1, Nama Kelas: 1</label>
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