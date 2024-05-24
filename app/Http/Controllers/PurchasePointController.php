<?php

namespace App\Http\Controllers;

use App\Models\PurchasePoint;
use Illuminate\Http\Request;

class PurchasePointController extends Controller
{

    public function create(Request $request)
    {
        try{
            $purchasePoint = new PurchasePoint();
            $purchasePoint->point = $request->point;
            $purchasePoint->price = $request->price;
            $purchasePoint->payment_method = $request->payment_method;
            $purchasePoint->payment_id = $request->payment_id;
            $purchasePoint->status = $request->status;
            $purchasePoint->save();
            return $this->sendResponse($purchasePoint);
        }
        catch (\Exception $e) {
            return $this->sendError('Purchase Point creation failed!', 500);
        }
    }

    public function index()
    {
        $purchasePoints = PurchasePoint::all();
        return $this->sendResponse($purchasePoints);
    }

    public function buy
    public function destroy(PurchasePoint $purchasePoint)
    {
        //
    }
}
