<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    public function index()
    {
      try{
          $path = public_path('countries.json'); // Path to your JSON file.
          $countries = json_decode(file_get_contents($path), true);

          return response()->json($countries);
      }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Countries retrieved failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{

            $json = public_path('countries.json');
            $data = json_decode(file_get_contents($json), true);
            DB::table('countries')->insert($data);
            return response()->json([
                'message' => 'Countries created successfully',
                'data' => $data
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Country created failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }


    public function destroy($id)
    {
        try{
            $country = Country::find($id);
            $country->delete();

            return response()->json([
                'message' => 'Country deleted successfully',
                'data' => $country
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Country deleted failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function getById($id)
    {
        try{
            $uploadController = new UploadController();
            $country = Country::find($id);
            if ($country->flag != null){
                $country->flag = $uploadController->getSignedUrl($country->flag);
            }
            else{
                $country->flag = null;
            }
            return response()->json([
                'message' => 'Country retrieved successfully',
                'data' => $country
            ], 200);
        }
        catch (\Exception $e){
            return response()->json([
                'message' => 'Country retrieved failed',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
