@extends("la.layouts.app")

@section("contentheader_description", $banner->$view_col)
@section("section", "Banners")
@section("section_url", url(config('laraadmin.adminRoute') . '/banners'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Banners Edit : ".$banner->$view_col)

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


<div class="box">
	<div class="box-header">
		
	</div>

	<div class="box-body">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				{!! Form::model($banner, ['route' => ['banners.update', $banner->id ], 'method'=>'PUT', 'id' => 'banner-edit-form']) !!}
				<div class="form-group control-all">
					<label for="title">标题 :</label>
					<input class="form-control" placeholder=" 输入标题" data-rule-maxlength="256" name="title" type="text" value="{{$banner->title}}">
				</div>

				<div class="form-group control-all">
					<label for="img_url">图片 :</label>
					<input class="form-control" placeholder="" data-rule-maxlength="256" name="img_url" type="hidden" value="" id="img_url">
					<span><img id='img_url_view' style="display: none" src='{{$banner->img_url}}' width="300px" height="150px"></span><button type="button" class="btn btn-success up-img" style="margin-left: 20px;">上传</button>
				</div>

				<div class="form-group control-all">
					<label for="jump_url">跳转地址 :</label>
					<input class="form-control" data-rule-maxlength="256" placeholder=" 输入跳转地址" name="jump_url" type="text" value="{{$banner->jump_url}}">
				</div>

				<div class="form-group control-all">
					<label for="status">是否启用 :</label>
					<select class="form-control select2-hidden-accessible work-select" data-placeholder="选择是否启用" rel="select2" name="status" tabindex="0" aria-hidden="true">
						@foreach ($status_list as $k => $v)
							<option value="{{$k}}" @if($k == $banner->status) selected @endif>{{$v}}</option>
						@endforeach
					</select>
				</div>
				<br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/banners') }}">Cancel</a></button>
					</div>
				{!! Form::close() !!}
				
			</div>
		</div>
	</div>
</div>

<form id="img-up" action="" method="post" enctype="multipart/form-data"  style="display: none;">
	<input name="_token" type="hidden" value="{{csrf_token()}}">
	<input type="file" id="up_img_url" class="form-control c-md-2" name="img" value="">
</form>

@endsection

@push('scripts')
<script>
$(function () {

    img = $("#img_url_view").attr("href");
    if ( img != "" ) {
        $("#img_url_view").show();
	}

	$("#banner-edit-form").validate({
		
	});

    $('.up-img').on("click", function () {
        $('#up_img_url').click();
    });

    $('#up_img_url').on('change', function () {
        $('.up-img').text("上传中");
        $('.up-img').attr("disabled","disabled");
        $('.up-img').removeClass("btn-success");
        $('.up-img').addClass("btn-danger");

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
                    $("#img_url").val(data.url);
                    $("#img_url_view").attr("src", data.url);
                    $("#img_url_view").show();
                } else {
                    alert(data.state);
                }
                $('.up-img').removeAttr("disabled");
                $('.up-img').text("上传");
                $('.up-img').removeClass("btn-danger");
                $('.up-img').addClass("btn-success");
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                // 状态码
                console.log(XMLHttpRequest.status);
                // 状态
                console.log(XMLHttpRequest.readyState);
                // 错误信息
                console.log(textStatus);
                alert("上传失败");
                $('.up-img').removeAttr("disabled");
                $('.up-img').text("上传");
                $('.up-img').removeClass("btn-danger");
                $('.up-img').addClass("btn-success");
            },
            contentType: false, // need
            processData: false // need
        });
    });
});
</script>
@endpush
