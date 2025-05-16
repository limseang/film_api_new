<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artical;
use App\Models\BookMark;
use App\Models\CategoryArtical;
use App\Models\Comment;
use App\Models\Country;
use App\Models\Distributor;
use App\Models\Farvorite;
use App\Models\Film;
use App\Models\Like;
use App\Models\Origin;
use App\Models\Rate;
use App\Models\Tag;
use App\Models\Type;
use App\Models\UserLogin;
use App\Models\video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PushNotificationService;
use Illuminate\Support\Str;
use Exception;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Artical::with(['origin', 'category', 'type'])->orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.artical.index', compact('articles'));
    }

    public function create()
    {
        $types = Type::all();
        $categories = CategoryArtical::all();
        $origins = Origin::all();
        return view('admin.artical.create', compact('types', 'categories', 'origins'));
    }

    public function store(Request $request)
    {
        try {
            $cloudController = new UploadController();
            $artical = new Artical();
            $artical->title = $request->title;
            $artical->description = $request->description;
            $artical->origin_id = $request->origin_id;
            $artical->category_id = $request->category_id;
            $artical->type_id = $request->type_id;
            if ($request->hasFile('image')) {
                $artical->image = $cloudController->UploadFile($request->file('image'));
            }
            $artical->save();

            $categoryArtical = new CategoryArtical();
            $categoryArtical->artical_id = $artical->id;
            $categoryArtical->category_id = $request->category_id;
            $categoryArtical->save();

            $type = $artical->type->name;
            $user = UserLogin::all();
            foreach ($user as $item){
                $data = [
                    'token' => $item->fcm_token,
                    'title' => 'New '.$type.' Article',
                    'body' => $artical->title,
                    'data' => [
                        'id' => $artical->id,
                        'type' => '1',
                    ]
                ];
                PushNotificationService::pushNotification($data);
            }
            return redirect()->route('artical.index')->with('success', 'Article created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $article = Artical::findOrFail($id);
        $types = Type::all();
        $categories = CategoryArtical::all();
        $origins = Origin::all();
        return view('admin.artical.edit', compact('article', 'types', 'categories', 'origins'));
    }

    public function update(Request $request, $id)
    {
        try {
            $article = Artical::findOrFail($id);
            $cloudController = new UploadController();
            
            $article->title = $request->title;
            $article->description = $request->description;
            $article->origin_id = $request->origin_id;
            $article->category_id = $request->category_id;
            $article->type_id = $request->type_id;
            
            if ($request->hasFile('image')) {
                $article->image = $cloudController->UploadFile($request->file('image'));
            }
            
            $article->save();

            // Update category
            CategoryArtical::where('artical_id', $id)->update(['category_id' => $request->category_id]);

            return redirect()->route('artical.index')->with('success', 'Article updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $article = Artical::findOrFail($id);
            $article->delete();
            return redirect()->route('artical.index')->with('success', 'Article deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function status($id)
    {
        try {
            $article = Artical::findOrFail($id);
            $article->status = !$article->status;
            $article->save();
            return redirect()->route('artical.index')->with('success', 'Article status updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $article = Artical::withTrashed()->findOrFail($id);
            $article->restore();
            return redirect()->route('artical.index')->with('success', 'Article restored successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            $cloudController = new UploadController();
            $image = $cloudController->UploadFile($request->file('image'));
            return response()->json(['url' => $image]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 