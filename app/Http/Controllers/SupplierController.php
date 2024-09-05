<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::all();
        return response()->json([
            'message' => 'successfully',
            'data' => $suppliers
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
      try{
          $supplier = new Supplier();
          $supplier->name = $request->name;
        //generate supplier_code

          $supplier->status = $request->status;
          $supplier->description = $request->description;
          $supplier->callback = $request->callback;
          $supplier->supplier_code = 'SUP'.rand(1000, 9999);
          $supplier->save();
          return $this->sendResponse($supplier, );
      }
        catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function detail($id)
    {
        try{
            $supplier = Supplier::find($id);
            if(!$supplier){
                return response()->json([
                    'message' => 'Supplier not found',
                ], 404);
            }
            return $this->sendResponse($supplier, );
        }
        catch (\Exception $e){
            return $this->sendError( $e->getMessage());
        }
        //
    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try{
            $supplier = Supplier::find($id);
            if(!$supplier){
                return $this->sendError('Supplier not found');
            }
            $supplier->name = $request->name;
            $supplier->supplier_code = $request->supplier_code;
            $supplier->status = $request->status;
            $supplier->description = $request->description;
            $supplier->save();
            return $this->sendResponse($supplier, 'Supplier updated successfully.');
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }
        //

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $supplier = Supplier::find($id);
            if(!$supplier){
                return response()->json([
                    'message' => 'Supplier not found',
                ], 404);
            }
            $supplier->delete();
            return $this->sendResponse($supplier, );
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }
        //
    }

}
