<?php
$this->data['page'] = 'Data Sekolah';
$this->data['icon'] = 'settings icon';
$this->load->view('dasbor/_header.php', $this->data);
//// id_sekolah
// kode_sekolah email nama_sekolah id_kecamatan alamat
?>
<div class="sixteen wide computer sixteen wide phone centered column">
	<div class="ui raised segment">
		<div class="content">
			<div class="ui centered grid">
				<div class="row">
					<div class="sixteen wide computer column">
						<form class="ui form" method="post" enctype="multipart/form-data">
							<div class="field">
								<label>Pilih Provinsi</label>
								<select id="id_provinsi">
									<?php
									foreach ($list_provinsi as $prov) {
									?>
										<option value="<?=$prov->id_provinsi?>"><?=$prov->nama?></option>
									<?php
									}
									?>
								</select>
							</div>
							<div class="field">
								<label>Pilih Kabupaten</label>
								<select id="id_kabupaten">
									<?php
									foreach ($list_kabupaten as $kab) {
									?>
										<option value="<?=$kab->id_kabupaten?>"><?=$kab->nama?></option>
									<?php
									}
									?>
								</select>
							</div>
							<div class="field">
								<label>Pilih Kecamatan</label>
								<select name="id_kecamatan">
									<?php
									foreach ($list_kecamatan as $kec) {
									?>
										<option value="<?=$kec->id_kecamatan?>"><?=$kec->nama?></option>
									<?php
									}
									?>
								</select>
							</div>
							<div class="field">
								<label>Alamat Sekolah</label>
								<textarea name="alamat"><?=$alamat?></textarea>
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
	$('#id_provinsi').on('change', function (arg) {
		id_provinsi = $(this).val()
		var option = $('<option></option>').attr("value", "option value").text("Text");
		$("#id_kabupaten").empty().append(option);
	})
</script>
<?php
$this->load->view('dasbor/_footer');