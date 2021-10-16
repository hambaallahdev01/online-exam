<?php
$this->data['page'] = 'Siswa';
$this->data['icon'] = 'chalkboard icon';
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
								<label>Nama Siswa</label>
								<input type="text" name="nama_siswa" autofocus>
							</div>
							<div class="three fields">
								<div class="field">
									<label>NIS</label>
									<input type="text" name="nis">
								</div>
								<div class="field">
									<label>Email</label>
									<input type="email" name="email">
								</div>
								<div class="field">
									<label>Password</label>
									<input type="password" name="password">
								</div>
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