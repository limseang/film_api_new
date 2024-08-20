<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\DataTables\ArticalDataTable;
use App\Models\Artical;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\AlibabaStorage;
use App\Models\Origin;
use App\Models\Film;
use App\Models\Tag;
use App\Models\Type;

class ArticalController extends Controller
{
    use AlibabaStorage;
    public function __construct()
    {
        $this->middleware('lang');
    }

    public function index(ArticalDataTable $dataTable)
    {
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => '#', 'page' => __('sma.artical')]];
        return $dataTable->render('artical.index', $data);
    }

    public function create()
    {
        $data['origins'] = Origin::where('status',1)->get();
        $data['categories'] = Category::where('status',1)->get();
        $data['type'] = Type::where('status',1)->get();
        $data['tag'] = Tag::where('status',1)->get();
        $data['film'] = Film::all();
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('artical.index'), 'page' => __('sma.artical')], ['link' => '#', 'page' => __('global.add')]];
        return view('artical.create', $data);
    }

    public function uploadImage(Request $request)
    {
        $file = $request->file('upload');
        if(empty($file)){
            return response()->json([
                'uploaded' => 0,
                'error' => [
                    'message' => 'error'
                ]
            ]);
        }
        $folder = 'artical';
        $result = $this->uploadUrl($file, $folder);
        if ($result) {
            return response()->json([
                'uploaded' => 1,
                'fileName' => $result['path'],
                'url' => $result['url']
            ]);
        } else {
            return response()->json([
                'uploaded' => 0,
                'error' => [
                    'message' => 'error'
                ]
            ]);
        }
    }

    public function store(request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'origin_id' => 'required|exists:origins,id',
            'tag_id' => 'required|array',
            'type_id' => 'required|exists:types,id',
            'category_id' => 'required|exists:categories,id',
            'film_id' => 'nullable|exists:films,id',
        ]);
        try{
            DB::beginTransaction();
            $artical = new Artical();
            if($request->hasFile('image')){
                $avatar = $this->UploadFile($request->file('image'), 'Artical');
            }
            $artical->title = $request->title;
            $artical->origin_id = $request->origin_id;
            $artical->type_id = $request->type_id;
            $artical->category_id = $request->category_id;
            $artical->film_id = $request->film_id ?? null;
            $artical->description  = $request->description ?? '';
            $artical->image = $avatar ?? null;
            $artical->save();
            $artical->tag()->sync($request->tag_id);
            DB::commit();

            $pageDirection = $request->submit == 'Save_New' ? 'create' : 'index';
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.add_successfully'),
            ];
            return redirect()->route('artical.'.$pageDirection)->with($notification);
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
        $data['artical'] = Artical::find($id);
        if(!$data['artical']){
            $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->back()->with($notification);
            }
        $data['origins'] = Origin::where('status',1)->get();
        $data['multiTag'] = $data['artical']->tag()->pluck('tag_id')->toArray();
        $data['categories'] = Category::where('status',1)->get();
        $data['type'] = Type::where('status',1)->get();
        $data['tag'] = Tag::where('status',1)->get();
        $data['film'] = Film::all();
        $data['image'] = $this->getSignUrlNameSize($data['artical']->image);
        $data['bc']   = [['link' => route('dashboard'), 'page' =>__('global.icon_home')], ['link' => route('artical.index'), 'page' => __('global.artical')], ['link' => '#', 'page' => __('global.edit')]];
        return view('artical.edit', $data);
    }

    public function update(request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'origin_id' => 'required|exists:origins,id',
            'tag_id' => 'required|array',
            'type_id' => 'required|exists:types,id',
            'category_id' => 'required|exists:categories,id',
            'film_id' => 'nullable|exists:films,id',
            'description' => 'required'

        ]);
        try{
            DB::beginTransaction();
            $artical = Artical::find($id);
            if(!$artical){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->back()->with($notification);
            }
            if($request->hasFile('image')){
                $image = $this->UploadFile($request->file('image'), 'Aritcal');
                if($artical->image){
                    $this->deleteFile($artical->image);
                }
                $artical->image = $image;
            }
            $artical->title = $request->title;
            $artical->origin_id = $request->origin_id;
            $artical->type_id = $request->type_id;
            $artical->category_id = $request->category_id;
            $artical->film_id = $request->film_id ?? null;
            $artical->description  = $request->description ?? '';
            $artical->save();
            $artical->tag()->sync($request->tag_id);
            DB::commit();
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.update_successfully'),
            ];
            return redirect()->route('artical.index')->with($notification);
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

    public function destroy($id)
    {
        try{
            $artical = Artical::find($id);
            if(!$artical){
                $notification = [
                    'type' => 'error',
                    'icon' => trans('global.icon_error'),
                    'title' => trans('global.title_error_exception'),
                    'text' => trans('sma.the_not_exist'),
                ];
                return redirect()->back()->with($notification);
            }
            $artical->delete();
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_updated'),
                'text' => trans('sma.delete_successfully'),
            ];
            return redirect()->back()->with($notification);
        }catch(Exception $e){
            $notification = [
                'type' => 'exception',
                'icon' => trans('global.icon_error'),
                'title' => trans('global.title_error_exception'),
                'text' => $e->getMessage()
            ];
            return redirect()->back()->withInput()->with($notification);
        }
    }

    public function restore($id){
        $artical = Artical::withTrashed()->find($id);
        if($artical){
            $artical->restore();
            $notification = [
                'type' => 'success',
                'icon' => trans('global.icon_success'),
                'title' => trans('global.title_restored'),
                'text' => trans('sma.restored_successfully'),
            ];
            return redirect()->back()->with($notification);
        }
        $notification = [
            'type' => 'errror',
            'icon' => trans('global.icon_error'),
            'title' => trans('global.title_error_exception'),
            'text' =>  trans('sma.the_not_exist'),
        ];
        return redirect()->back()->with($notification);
    }


}
