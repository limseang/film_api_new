<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DataTables\CategoryDataTable;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Constant\RolePermissionConstant;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(CategoryDataTable $dataTable)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_CATEGORY_VIEW)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.category_film')]];
        return $dataTable->render('category.index', $data);
    }

    public function create()
    {
        if(!authorize(RolePermissionConstant::PERMISSION_CATEGORY_CREATE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('category.index'), 'page' => __('sma.category_film')], ['link' => '#', 'page' => __('sma.add')]];
        return view('category.create', $data);
    }

    public function store(Request $request)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_CATEGORY_CREATE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $this->validate($request, [
            'name' => 'required|unique:types,name',
            'description' => 'nullable|max:255',
            'status' => 'required|in:1,2',
        ]);
        try{
            DB::beginTransaction();
            $category = new Category();
            $category->name = $request->name;
            $category->description = $request->description;
            $category->status = $request->status;
            $category->save();
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('category.'.$pageDirection)->with($notification);
        }catch(Exception $e){
            DB::rollBack();
            $notification = [
                'type' => 'exception',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => $e->getMessage()
            ];
            return redirect()->back()->withInput()->with($notification);
        }
    }

    public function edit($id)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_CATEGORY_EDIT)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['category'] = Category::find($id);
        if(!$data['category']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->route('category.index')->with($notification);
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('category.index'), 'page' => __('sma.category_film')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('category.edit', $data);
    }

    public function update(Request $request, $id)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_CATEGORY_EDIT)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $this->validate($request, [
            'name' => 'required|unique:categories,name,'.$id,
            'status' => 'required|in:1,2',
            'desctription' => 'nullable|max:255',
        ]);

        try{
            DB::beginTransaction();
            $category = Category::find($id);
                if(!$category){
                    $notification = [
                        'type' => 'error',
                        'icon' => trans('global.icon_error'),
                        'title' => trans('global.title_error_exception'),
                        'text' => trans('sma.the_not_exist'),
                    ];
                    return redirect()->route('type.index')->with($notification);
                }
                $category->name = $request->name;
                $category->description = $request->description;
                $category->status = $request->status;
                $category->save();
                
                DB::commit();
                $notification = [
                    'type' => 'success',
                    'icon' => trans('global.icon_success'),
                    'title' => trans('global.title_updated'),
                    'text' => trans('sma.update_successfully'),
                ];
                return redirect()->route('category.index')->with($notification);
            }catch(Exception $e){
                DB::rollBack();
                $notification = [
                    'type' => 'exception',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => $e->getMessage()
                ];
                return redirect()->back()->withInput()->with($notification);
            }
        }


        public function status($id)
        {
            if(!authorize(RolePermissionConstant::PERMISSION_CATEGORY_CHANGE_STATUS)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $category = Category::find($id);
            if(!$category){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('type.index')->with($notification);
            }
            $category->status = $category->status == 1 ? 2 : 1;
            $category->save();
            $notification = [
               'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('category.index')->with($notification);
        }

        public function destroy($id)
        {
            if(!authorize(RolePermissionConstant::PERMISSION_CATEGORY_DELETE)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $category = Category::find($id);
            if(!$category){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('type.index')->with($notification);
            }
            $totalUsed = $category->filmCategory()->count();
            if($totalUsed> 0){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.cant_delete_type_being_used'),
                ];
                return redirect()->route('category.index')->with($notification);
            }
            $category->delete();   
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('category.index')->with($notification);
        }
}
