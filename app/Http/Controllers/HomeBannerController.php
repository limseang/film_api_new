<?php

namespace App\Http\Controllers;

use App\Models\Advertis;
use App\Models\Artical;
use App\Models\Film;
use App\Models\HomeBanner;
use Illuminate\Http\Request;

class HomeBannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */public function index()
{
    try {
        // Load banners and their related models
        $uploadController = new UploadController();
        $homeBanners = HomeBanner::with(['ads','artical', 'films'])->orderBy('id', 'desc')->get();

        // Process each banner
        $response = $homeBanners->map(function ($banner, $key) use ($uploadController) {
            $item = null;

            // Handle the item based on the item_type
            switch ($banner->item_type) {
                case 1: // Item type 1: Ads
                    if ($banner->ads) {
                        $item = [
                            'id' => $banner->ads->id,
                            'title' => $banner->ads->name,
                            'link' => $banner->ads->link,
                            'poster' => $banner->ads->image ? $uploadController->getSignedUrl($banner->ads->image) : null,
                            'item_type' => 'ads',
                        ];
                    }
                    break;

                case 2: // Item type 2: Artical
                    if ($banner->artical) {
                        $item = [
                            'id' => $banner->artical->id,
                            'title' => $banner->artical->title,
                            'origin' => $banner->artical->origin->name,
                            'origin_khmer' => $banner->artical->origin->description,
                            'poster' => $banner->artical->image ? $uploadController->getSignedUrl($banner->artical->image) : null,
                            'item_type' => 'artical',
                        ];
                    }
                    break;

                case 3: // Item type 3: Films
                    if ($banner->films) {
                        $item = [
                            'id' => $banner->films->id,
                            'title' => $banner->films->title,
                            'release_date' => $banner->films->release_date,
                            'trailer' => $banner->films->trailer,
                            'poster' => $banner->films->poster ? $uploadController->getSignedUrl($banner->films->poster) : null,
                            'item_type' => 'films',
                        ];
                    }
                case 4: // Item type 4: Casting
                    if ($banner->ads) {
                        $item = [
                            'id' => $banner->ads->id,
                            'title' => $banner->ads->name,
                            'link' => $banner->ads->link,
                            'poster' => $banner->ads->image ? $uploadController->getSignedUrl($banner->ads->image) : null,
                            'item_type' => 'casting',
                        ];
                    }
                    break;

                default:
                    $item = ['error' => 'Invalid item type'];
            }

            // Return the processed banner data
            return [
                'banner_id' => $banner->id,
                'banner_title' => $banner->name ?? 'No Title', // Ensure title fallback
                'item' => $item, // Include only the relevant item
            ];
        });

        return $this->sendResponse($response);
    } catch (\Exception $e) {
        return $this->sendError($e->getMessage(), 500);
    }
}






    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $itemType = $request->input('item_type');
            $itemId = $request->input('item_id');

            switch ($itemType) {
                case 1:
                    $item = Advertis::find($itemId);
                    break;
                case 2:
                    $item = Artical::find($itemId);
                    break;
                case 3:
                    $item = Film::find($itemId);
                    break;
                default:
                    return $this->sendError('Invalid item type');
            }

            if (!$item) {
                return $this->sendError('Item not found');
            }

            $homeBanner = HomeBanner::create($request->all());
            return $this->sendResponse($homeBanner,  );
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request,$id)
    {

        try {
            $homeBanner = HomeBanner::find($id);
            if (!$homeBanner) {
                return $this->sendError('Home banner not found');
            }

            $itemType = $request->input('item_type');
            $itemId = $request->input('item_id');

            switch ($itemType) {
                case 1:
                    $item = Advertis::find($itemId);
                    break;
                case 2:
                    $item = Artical::find($itemId);
                    break;
                case 3:
                    $item = Film::find($itemId);
                    break;
                default:
                    return $this->sendError('Invalid item type');
            }

            if (!$item) {
                return $this->sendError('Item not found');
            }

            $homeBanner->update($request->all());
            return response()->json($homeBanner);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }


    public function destroy($id)
    {
        try{
            $homeBanner = HomeBanner::find($id);
            if (!$homeBanner) {
                return $this->sendError('Home banner not found');
            }

            $homeBanner->delete();
            return $this->sendResponse( 'Home banner deleted');
        }
        catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }
}
