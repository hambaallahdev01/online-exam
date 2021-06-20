</div>
<!-- /.conainer-fluid -->
</main>

</div>


<script type="text/javascript">


	var save_method = 'add';
	$(document).ready(function() {
		$('#pencarianDatatable').keyup(function(){
			dataTable.search($(this).val()).draw() ;
		});

		$('.select2').select2({
			width: '100%'
		});
		$('.input-daterange').datepicker({
			locale: {
				format: 'YYYY-MM-DD'
			},
		});

	});

	function hide_modal(){
		$('#myModal').modal('hide');
	}

	function status_class(status)
	{
		class_ = '';
		if (status==1) {
			class_ = 'active';
		}else if(status==2){
			class_ = 'actived';
		}
		return class_;
	}
	function to_rupiah(val)
	{
		val += '';
		x = val.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		x3 = x1 + x2;
		x3 = x3.replaceAll(',', '~').replaceAll('.', '^').replaceAll('~', '.').replaceAll('^', ',')
		return "Rp"+x3;
	}
	function from_rupiah(value) {
		return value.toString().replace(/\./g, "").replace("Rp", "");
	}

	function to_input_number(value) {
		return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	function reload_table(){
		dataTable.ajax.reload(null,false);
	}
	function get_obj(obj, key)
	{
		if (obj && obj[key] && typeof(obj[key]) !== 'undefined' && (typeof(obj[key] != null))) {
			return obj[key]
		}else{
			return 0
		}
	}
	function get_int(obj, key)
	{
		if (obj && obj[key] && typeof(obj[key]) !== 'undefined' && (typeof(obj[key] != null))) {
			return parseInt(obj[key])
		}else{
			return 0
		}
	}
	function get_obj_(obj, key)
	{
		if (obj && obj[key] && typeof(obj[key]) !== 'undefined' && (typeof(obj[key] != null))) {
			return obj[key]
		}else{
			return 0
		}
	}
	function is_defined(variable)
	{
		if (variable && typeof(variable) !== 'undefined' && (typeof(variable != null))) {
			return true
		}else{
			return false
		}
	}

	function back(){
		window.location = "<?php echo site_url('admin/daftar-order')?>";
	}

	function form_reset(){
		$('#formData')[0].reset();
	}

	function authenticate_required_field() {
		var required_field = []
		$('#formData').find('input').each(function(){
			if($(this).prop('required')){
				if (!$(this).val()) {
					$(this).focus()
					$(this).parent().addClass('has-danger')
					required_field.push($(this).parent().find('label').text())
				}else{
					$(this).parent().removeClass('has-danger')
				}
			}
		});
		$('#formData').find('select').each(function(){
			if($(this).prop('required')){
				if (!$(this).val()) {
					$(this).parent().addClass('has-danger')
					required_field.push($(this).parent().find('label').text())
				}else{
					$(this).parent().removeClass('has-danger')
				}
			}
		});
		if (required_field.length>0) {
			open_alert_manual('Field '+required_field.join(', ')+' harus diisi!')
			return false
		}else{
			return true
		}
	}
	var getUrlParameter = function getUrlParameter(sParam) {
		var sPageURL = decodeURIComponent(window.location.search.substring(1)),
		sURLVariables = sPageURL.split('&'),
		sParameterName,
		i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : sParameterName[1];
			}
		}
	};
	var dataTable;
	$(function() {
		jQuery.fn.exists = function(){return this.length>0;}
		if ($('#master-datatable').exists()) {
			var dtSettings = {
				"destroy":true,
				"processing":true,
				"serverSide":true,
				"order":[],
				"ajax":{
					url:dt_url,
					type:"POST"
				},
				"language": {
					"lengthMenu": "Tampilkan _MENU_ data per halaman",
					"paginate": {
						"next": '<i class="fa fa-angle-right"></i>',
						"previous": '<i class="fa fa-angle-left"></i>'
					},
					"info": "_START_ - _END_ dari _TOTAL_",
				},
				"dom":'<"row" <"col-md-12"lr>> <"row"<"col-md-12"t>> <"row"<"col-md-6"i><"col-md-6"p>>'
			}
			if (typeof(aoColumnDefs) !== 'undefined') {
				dtSettings.aoColumnDefs = aoColumnDefs
			}
			if (typeof(pageLength) !== 'undefined') {
				dtSettings.pageLength = pageLength
			}
			if (typeof(lengthMenu) !== 'undefined') {
				dtSettings.lengthMenu = lengthMenu
			}
			dataTable = $('#master-datatable').DataTable(dtSettings);
		}
		$.ajaxSetup({
			beforeSend:function(){
				$("#ajaxloading").show();
			},
			complete:function(){
				$("#ajaxloading").hide();
			}
		});
	})
</script>


<!-- Bootstrap and necessary plugins -->
<script src="<?php echo base_url('assets/template/bower_components/tether/dist/js/tether.min.js');?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/template/bower_components/bootstrap/dist/js/bootstrap.min.js');?>" type="text/javascript"></script>
<!-- <script src="<?php echo base_url('assets/template/bower_components/pace/pace.min.js');?>" type="text/javascript"></script> -->

<!-- datatable -->
<script src="<?php echo base_url('assets/js/jquery.dataTables.min.js');?>" type="text/javascript" charset="utf-8"></script>

<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js" type="text/javascript" charset="utf-8"></script>

<!-- clockpicker -->
<script src="<?php echo base_url('assets/js/bootstrap-clockpicker.min.js');?>" type="text/javascript" charset="utf-8"></script>

<!-- datepicker -->
<script src="<?php echo base_url('assets/js/bootstrap-datepicker.min.js');?>" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo base_url('assets/js/bootstrap-datepicker.id.min.js')?>" type="text/javascript" charset="utf-8"></script>

<!-- daterangepicker -->
<script src="<?php echo base_url('assets/daterangepicker/moment.min.js');?>" type="text/javascript" charset="utf-8"></script>

<script src="<?php echo base_url('assets/daterangepicker/daterangepicker.js');?>" type="text/javascript" charset="utf-8"></script>

<script src="<?php echo base_url('assets/fullcalendar/fullcalendar.js');?>"></script>

<!-- select2 -->
<script src="<?php echo base_url('assets/select2/js/select2.min.js');?>" type="text/javascript" charset="utf-8"></script>

<!-- GenesisUI main scripts -->
<script src="<?php echo base_url('assets/template/js/app.js');?>" type="text/javascript"></script>

<!-- Plugins and scripts required by this views -->

<script type="text/javascript">

function tambah_notifikasi(payload) {
	$('#total-notifikasi').text(parseInt($('#total-notifikasi').text())+1)
	$('#list-notifikasi').prepend('<a class="dropdown-item" href="'+payload.notification.click_action+'"><i class="fa fa-tasks"></i> '+payload.notification.body+'</a>')
}

function konfirmasi_hapus() {
	if (confirm('Anda yakin menghapus?')) {
		return true
	} else {
		return false
	}
}

function removeA(arr) {
	var what, a = arguments, L = a.length, ax;
	while (L > 1 && arr.length) {
		what = a[--L];
		while ((ax= arr.indexOf(what)) !== -1) {
			arr.splice(ax, 1);
		}
	}
	return arr;
}

</script>
<div class="modal informasi" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<p class="modal-title" id="myModalLabel">Informasi</p>
			</div>
			<div class="modal-body">
				<p id="info"></p>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function open_alert_manual(pesan='Aksi Berhasil', milidetik=2000) {
		$(".informasi").modal('show').one('shown.bs.modal', function(event){
			$(this).find('p#info').text(pesan)
		});
		window.setTimeout(function() {
			$(".informasi").modal('hide');
		}, milidetik);
	}
</script>
<div class="loader" id="ajaxloading"><div class="loader__figure"></div></div>

</body>
</html>
