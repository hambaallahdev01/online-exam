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
											<input type="date" name="tanggal_awal_ganjil" autofocus>
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
	$('[name=tanggal_awal_ganjil]').on('change', function () {
		var tanggal_awal_ganjil = new Date(this.value)
		tanggal_awal_ganjil.setDate(tanggal_awal_ganjil.getDate() + 1)
		tanggal_akhir_ganjil = get_date_value(tanggal_awal_ganjil)
		$('[name=tanggal_akhir_ganjil]').val(tanggal_akhir_ganjil);
	})

	$('[name=tanggal_akhir_ganjil]').on('change', function () {
		var tanggal_akhir_ganjil = new Date(this.value)
		tanggal_akhir_ganjil.setDate(tanggal_akhir_ganjil.getDate() + 1)
		tanggal_awal_genap = get_date_value(tanggal_akhir_ganjil)
		$('[name=tanggal_awal_genap]').val(tanggal_awal_genap);
	})

	$('[name=tanggal_awal_genap]').on('change', function () {
		var tanggal_awal_genap = new Date(this.value)
		tanggal_awal_genap.setDate(tanggal_awal_genap.getDate() + 1)
		tanggal_akhir_genap = get_date_value(tanggal_awal_genap)
		$('[name=tanggal_akhir_genap]').val(tanggal_akhir_genap);
	})

	function get_date_value(date) {
		var day = ("0" + date.getDate()).slice(-2);
		var month = ("0" + (date.getMonth() + 1)).slice(-2);
		return date.getFullYear()+"-"+(month)+"-"+(day) ;
	}


</script>
<?php
$this->load->view('dasbor/_footer');