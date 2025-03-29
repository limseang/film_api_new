<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DataTables\TagDataTable;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use App\Constant\RolePermissionConstant;
use Exception;

class TagController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(TagDataTable $dataTable)
    {
        if(!authorize(RolePermissionConstant::PERMISSION_TAG_VIEW)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.tag')]];
        return $dataTable->render('tag.index', $data);
    }

    public function create()
    {
        if(!authorize(RolePermissionConstant::PERMISSION_TAG_CREATE)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('tag.index'), 'page' => __('sma.tag')], ['link' => '#', 'page' => __('sma.add')]];
        return view('tag.create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:tags,name',
            'description' => 'nullable|max:255',
            'status' => 'required|in:1,2',
            'type' => 'required',
        ]);
        try{
            DB::beginTransaction();
            $tag = new Tag();
            $tag->name = $request->name;
            $tag->description = $request->description;
            $tag->status = $request->status;
            $tag->type = $request->type;
            $tag->save();
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('tag.'.$pageDirection)->with($notification);
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
        if(!authorize(RolePermissionConstant::PERMISSION_TAG_EDIT)){
            return redirect()->back()->with('error', authorizeMessage());
        }
        $data['tag'] = Tag::find($id);
        if(!$data['tag']){
            $notification = [
                'type' => 'error',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' =>  trans('sma.the_not_exist')
            ];
            return redirect()->route('tag.index')->with($notification);
        }
        $data['bc']   = [['link' => route('dashboard'), 'page' => __('global.icon_home')], ['link' => route('tag.index'), 'page' => __('sma.tag')], ['link' => '#', 'page' => __('sma.edit')]];
        return view('tag.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|unique:tags,name,'.$id,
            'status' => 'required|in:1,2',
            'desctription' => 'nullable|max:255',
            'type' => 'required',
        ]);

        try{
            DB::beginTransaction();
            $tag = Tag::find($id);
                if(!$tag){
                    $notification = [
                        'type' => 'error',
                        'icon' => trans('global.icon_error'),
                        'title' => trans('global.title_error_exception'),
                        'text' => trans('sma.the_not_exist'),
                    ];
                    return redirect()->route('tag.index')->with($notification);
                }
                $tag->name = $request->name;
                $tag->description = $request->description;
                $tag->status = $request->status;
                $tag->type = $request->type;
                $tag->save();
                
                DB::commit();
                $notification = [
                    'type' => 'success',
                    'icon' => trans('global.icon_success'),
                    'title' => trans('global.title_updated'),
                    'text' => trans('sma.update_successfully'),
                ];
                return redirect()->route('tag.index')->with($notification);
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
            if(!authorize(RolePermissionConstant::PERMISSION_TAG_EDIT)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $tag = Tag::find($id);
            if(!$tag){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('tag.index')->with($notification);
            }
            $tag->status = $tag->status == 1 ? 2 : 1;
            $tag->save();
            $notification = [
               'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('tag.index')->with($notification);
        }

        public function destroy($id)
        {
            if(!authorize(RolePermissionConstant::PERMISSION_TAG_DELETE)){
                return redirect()->back()->with('error', authorizeMessage());
            }
            $tag = Tag::find($id);
            if(!$tag){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->route('tag.index')->with($notification);
            }
            $totalUsed = $tag->artical()->count() + $tag->film()->count();
            if($totalUsed> 0){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.cant_delete_tag_being_used'),
                ];
                return redirect()->route('tag.index')->with($notification);
            }
            $tag->delete();   
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('tag.index')->with($notification);
        }
}
