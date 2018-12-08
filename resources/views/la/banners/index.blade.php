@extends("la.layouts.app")

@section("contentheader_title", "Banners")
@section("contentheader_description", "Banners listing")
@section("section", "Banners")
@section("sub_section", "Listing")
@section("htmlheader_title", "Banner Listing")

@section("headerElems")
@la_access("Banners", "create")
	<button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#AddModal">Add Banners</button>
@endla_access
@endsection

@section("main-content")

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="box box-success">
	<!--<div class="box-header"></div>-->
	<div class="box-body">
		<table id="example1" class="table table-bordered">
		<thead>
		<tr class="success">
			@foreach( $listing_cols as $col )
			<th>{{ $module->fields[$col]['label'] or ucfirst($col) }}</th>
			@endforeach
			@if($show_actions)
			<th>Actions</th>
			@endif
		</tr>
		</thead>
		<tbody>
			
		</tbody>
		</table>
	</div>
</div>

@la_access("Banners", "create")
<div class="modal fade" id="AddModal" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">添加Banner</h4>
			</div>
			{!! Form::open(['action' => 'LA\BannersController@store', 'id' => 'banner-add-form']) !!}
			<div class="modal-body">
				<div class="box-body">
					<div class="form-group control-all">
						<label for="title">标题 :</label>
						<input class="form-control" placeholder=" 输入标题" data-rule-maxlength="256" name="title" type="text" value="">
					</div>

					<div class="form-group control-all">
						<label for="img_url">图片 :</label>
						<input class="form-control" placeholder="" data-rule-maxlength="256" name="img_url" type="hidden" value="" id="img_url">
						<span><img id='img_url_view' style="display: none" src='' width="300px" height="150px"></span><button type="button" class="btn btn-success up-img" style="margin-left: 20px;">上传</button>
					</div>

					<div class="form-group control-all">
						<label for="jump_url">跳转地址 :</label>
						<div class='input-group jump_url'>
							<input id= '' class="form-control" data-rule-maxlength="256" placeholder=" 输入跳转地址" name="jump_url" type="text" value="">
						</div>
					</div>

					<div class="form-group control-all">
						<label for="status">是否启用 :</label>
						<select class="form-control select2-hidden-accessible work-select" data-placeholder="选择是否启用" rel="select2" name="status" tabindex="0" aria-hidden="true">
							@foreach ($status_list as $k => $v)
								<option value="{{$k}}" >{{$v}}</option>
							@endforeach
						</select>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
				{!! Form::submit( '提交', ['class'=>'btn btn-success']) !!}
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endla_access

<form id="img-up" action="" method="post" enctype="multipart/form-data"  style="display: none;">
	<input name="_token" type="hidden" value="{{csrf_token()}}">
	<input type="file" id="up_img_url" class="form-control c-md-2" name="img" value="">
</form>


@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('la-assets/plugins/datatables/datatables.min.css') }}"/>
@endpush

@push('scripts')
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
$(function () {
	$("#example1").DataTable({
		processing: true,
        serverSide: true,
        ajax: "{{ url(config('laraadmin.adminRoute') . '/banner_dt_ajax') }}",
		language: {
			lengthMenu: "_MENU_",
			search: "_INPUT_",
			searchPlaceholder: "Search"
		},
		@if($show_actions)
		columnDefs: [ { orderable: false, targets: [-1] }],
		@endif
	});
	$("#banner-add-form").validate({
		
	});

    $('.up-img').on("click", function () {
        $('#up_img_url').click();
    });

    $('#up_img_url').on('change', function () {
        var formData = new FormData($('#img-up')[0]); // FormData is the key
        $.ajax({
            url: "{{ url(config('laraadmin.adminRoute') . '/banner_img_up') }}",  // 处理请求的PHP文件 / 接口
            type: 'POST',
            data: formData, // 发送的数据
            dataType: 'json', // 返回数据的类型
            // heads : {
            //     'content-type' : 'multipart/form-data'
            // },
            success: function (data) {
                if ( data.state == "SUCCESS" ) {
                    $("#img_url_view").attr("src", data.url);
                    $("#img_url_view").show();
                } else {
                    alert(data.state);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                // 状态码
                console.log(XMLHttpRequest.status);
                // 状态
                console.log(XMLHttpRequest.readyState);
                // 错误信息
                console.log(textStatus);
                alert("上传失败");
            },
            contentType: false, // need
            processData: false // need
        });
    });

});
</script>
@endpush