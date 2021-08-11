<?php
$this->data['page'] = 'Tahun Pelajaran';
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
									<label>Tanggal Awal Ganjil</label>
									<div class="ui calendar" id="rangestart">
										<div class="ui input left icon">
											<i class="calendar icon"></i>
											<input type="date" name="tanggal_awal_ganjil">
										</div>
									</div>
								</div>
								<div class="field">
									<label>Tanggal Akhir Ganjil</label>
									<div class="ui calendar" id="rangeend">
										<div class="ui input left icon">
											<i class="calendar icon"></i>
											<input type="date" name="tanggal_akhir_ganjil">
										</div>
									</div>
								</div>
							</div>
							<div class="two fields">
								<div class="field">
									<label>Tanggal Awal Genap</label>
									<div class="ui calendar" id="rangestart">
										<div class="ui input left icon">
											<i class="calendar icon"></i>
											<input type="date" name="tanggal_awal_genap">
										</div>
									</div>
								</div>
								<div class="field">
									<label>Tanggal Akhir Genap</label>
									<div class="ui calendar" id="rangeend">
										<div class="ui input left icon">
											<i class="calendar icon"></i>
											<input type="date" name="tanggal_akhir_genap">
										</div>
									</div>
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
<script type="text/javascript">
	$('[name=tahun_awal]').on('change', function(){
		$('#tahun_akhir').val(parseInt($(this).val())+1)
	})
</script>
<?php
$this->load->view('dasbor/_footer');