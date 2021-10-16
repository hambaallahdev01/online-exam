<?php
$this->data['page'] = 'Mata Pelajaran';
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
							<div class="two fields">
								<div class="field">
									<label>Nama Mata Pelajaran</label>
									<input type="text" name="mapel" placeholder="Bahasa Indonesia" autofocus>
								</div>
								<div class="field">
									<label>Kode Mata Pelajaran</label>
									<input type="text" name="kode" placeholder="BI">
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