<?php
$this->data['page'] = 'Alamat Sekolah';
$this->data['icon'] = 'map marked alternate icon';
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
		$("#id_kabupaten").empty()
		$("[name=id_kecamatan]").empty().prop('disabled', true)
		id_provinsi = $(this).val()
		$.ajax({
			url : "<?=site_url()?>api/alamat/kabupaten?id_provinsi="+id_provinsi,
			type:'GET',
			dataType: 'json',
			success: function(response) {
				$("#id_kabupaten").attr('disabled', false)
				$.each(response,function(key, value)
				{
					$("#id_kabupaten").append('<option value=' + value.id_kabupaten + '>' + value.nama + '</option>')
				})
			}
		})
	})

	$('#id_kabupaten').on('change', function (arg) {
		$("[name=id_kecamatan]").empty()
		id_kabupaten = $(this).val()
		$.ajax({
			url : "<?=site_url()?>api/alamat/kecamatan?id_kabupaten="+id_kabupaten,
			type:'GET',
			dataType: 'json',
			success: function(response) {
				$("[name=id_kecamatan]").attr('disabled', false)
				$.each(response,function(key, value)
				{
					// console.info(value)
					$("[name=id_kecamatan]").append('<option value=' + value.id_kecamatan + '>' + value.nama + '</option>')
				})
			}
		})
	})
</script>
<?php
$this->load->view('dasbor/_footer');