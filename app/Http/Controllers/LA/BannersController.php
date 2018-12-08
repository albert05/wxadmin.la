<?php
namespace App\Http\Controllers\LA;

use App\Common\Upload;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;
use Datatables;
use Collective\Html\FormFacade as Form;
use Dwij\Laraadmin\Models\Module;
use Dwij\Laraadmin\Models\ModuleFields;
use Illuminate\Support\Facades\Input;


class BannersController extends Controller
{
	public $show_action = true;
	public $view_col = 'id';
	public $listing_cols = ['id', 'title', 'img_url', 'jump_url', 'status'];
	
	public function __construct() {
		
		// Field Access of Listing Columns
		if(\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
			$this->middleware(function ($request, $next) {
				$this->listing_cols = ModuleFields::listingColumnAccessScan('Banners', $this->listing_cols);
				return $next($request);
			});
		} else {
			$this->listing_cols = ModuleFields::listingColumnAccessScan('Banners', $this->listing_cols);
		}
	}
	
	/**
	 * Display a listing of the WorkLists.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module = Module::get('Banners');

		if ( Module::hasAccess($module->id) ) {
		    $status_list = [
                0 => "否",
                1 => "是",
            ];
			return View('la.banners.index', [
				'show_actions' => $this->show_action,
				'listing_cols' => $this->listing_cols,
				'module' => $module,
                "status_list" => $status_list
			]);
		} else {
            return redirect(config('laraadmin.adminRoute')."/");
        }
	}

	/**
	 * Store a newly created worklist in database.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		if( Module::hasAccess("Banners", "create") ) {
		
			$rules = Module::validateRules("Banners", $request);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}
			
			Banner::create([
				'img_url' => $request->img_url,
                'title' => $request->title,
                'jump_url' => $request->jump_url,
                'status' => $request->status,
			]);

			return redirect()->route('banners.index');
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Display the specified worklist.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		if( Module::hasAccess("Banners", "view") ) {

			$banner = Banner::find($id);
			if ( isset($banner->id) ) {
				$module = Module::get('Banners');
				$module->row = $banner;
				
				return view('la.banners.show', [
					'module' => $module,
					'view_col' => $this->view_col,
					'no_header' => true,
					'no_padding' => "no-padding"
				])->with('banner', $banner);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("banner"),
				]);
			}
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Show the form for editing the specified worklist.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		if( Module::hasAccess("Banners", "edit") ) {
			
			$banner = Banner::find($id);
			if( isset($banner->id) ) {
				$module = Module::get('Banners');
				
				$module->row = $banner;
				
				return view('la.banners.edit', [
					'module' => $module,
					'view_col' => $this->view_col,
				])->with('banner', $banner);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("banner"),
				]);
			}
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Update the specified worklist in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		if( Module::hasAccess("Banners", "edit") ) {
			
			$rules = Module::validateRules("Banners", $request, true);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();;
			}
			
            Banner::where('id', $id)->update([
                'img_url' => $request->img_url,
                'title' => $request->title,
                'jump_url' => $request->jump_url,
                'status' => $request->status,
            ]);
        	
			return redirect()->route('banners.index');
			
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Remove the specified worklist from storage.
	 */
	public function imgup()
	{
        $file = Input::file('img');

        if ( $file->isValid() ) {
            $filename = $file->getRealPath();
            if ( file_exists($filename) ) {
                $f = new Upload();
                $f->ext = $file->getClientOriginalExtension();
                $ret = $f->upload($filename);

                return json_encode($ret);
            }
        }

        return json_encode(["state" => "无效的图片"]);
	}
	
	/**
	 * Datatable Ajax fetch
	 *
	 * @return
	 */
	public function dtajax()
	{
		$values = DB::table('banners')->select($this->listing_cols)->whereNull('deleted_at');
		$out = Datatables::of($values)->make();
		$data = $out->getData();

		$fields_popup = ModuleFields::getModuleFields('Banners');
		
		for($i=0; $i < count($data->data); $i++) {
			for ($j=0; $j < count($this->listing_cols); $j++) { 
				$col = $this->listing_cols[$j];
				if($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
					$data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
				}
//				if($col == $this->view_col) {
//					$data->data[$i][$j] = '<a href="'.url(config('laraadmin.adminRoute') . '/banners/'.$data->data[$i][0]).'">'.$data->data[$i][$j].'</a>';
//				}
			}
			
			if($this->show_action) {
				$output = '';
				if(Module::hasAccess("Banners", "edit")) {
                    $url = config('laraadmin.adminRoute') . "/banners/{$data->data[$i][0]}/edit";
                    $output .= '<a href="' . $url . '" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
				}
				$data->data[$i][] = (string)$output;
			}
		}
		$out->setData($data);
		return $out;
	}

}
