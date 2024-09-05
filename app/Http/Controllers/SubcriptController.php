<?php

namespace App\Http\Controllers;

use App\Models\crf;
use App\Models\Subcript;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubcriptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $subcripts = Subcript::all();
            return $this->sendResponse($subcripts, );
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try{
            $subcript = new Subcript();
           $subcript->supplier_code = $request->supplier_code;
           if($subcript->supplier_code) {
               $supplier = Supplier::where('supplier_code', $subcript->supplier_code)->first();
               if (!$supplier) {
                   return $this->sendError('Supplier not found');
               }
           }

            $subcript->name = $request->name;
            $subcript->duration = $request->duration;
            $subcript->price = $request->price;
            $subcript->description = $request->description;
            $subcript->status = $request->status;
            $subcript->supplier_code = $request->supplier_code;
            $subcript->uuid = rand(1000000000, 9999999999);
            $subcript->save();
            //random 10 digit uuid
            return $this->sendResponse($subcript, );
        }
        catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
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
    public function show(crf $crf)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(crf $crf)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, crf $crf)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $subcript = Subcript::find($id);
            $subcript->delete();
            return $this->sendResponse($subcript);
        }
        catch (\Exception $e){
            return $this->sendError($e->getMessage());
        }
    }

    private $appleSandboxUrl = 'https://sandbox.itunes.apple.com/verifyReceipt';
    private $appleProductionUrl = 'https://buy.itunes.apple.com/verifyReceipt';

    public function verifySubscription(Request $request)
    {
        // Step 1: Validate the request
        $request->validate([
            'receipt' => 'required|string',
        ]);

        $receiptData = $request->input('receipt');

        // Step 2: Send the receipt to Apple's servers for validation
        $response = $this->sendReceiptToApple($receiptData);

        // Step 3: Handle the response and check if the subscription is valid
        return $this->handleAppleResponse($response);
    }

    private function sendReceiptToApple($receiptData)
    {
        $postData = json_encode([
            'receipt-data' => $receiptData,
            'password' => '7f3ca98c91d643fe93fc5f796f8d73bc', // Fetch shared secret from config
        ]);

        // First, try verifying with the production URL
        $response = $this->callAppleApi($this->appleSandboxUrl, $postData);

        if (isset($response['status']) && $response['status'] == 21007) {
            // 21007 means the receipt is from the sandbox environment, so try the sandbox URL
            $response = $this->callAppleApi($this->appleSandboxUrl, $postData);
        }

        return $response;
    }

    private function handleAppleResponse($response)
    {
        // Check if the response is a JsonResponse and decode it
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $response = $response->getData(true); // Convert JsonResponse to array
        }

        if (isset($response['status']) && $response['status'] == 0) {
            // The receipt is valid, check the latest subscription status
            return response()->json([
                'success' => true,
                'message' => 'Subscription is valid.',
                'data' => $response,  // Make sure the receipt key exists in the response
            ], 200);
        } else {
            // Log the response details for debugging
            Log::error('Apple subscription validation failed', [
                'response' => $response,
                'error_code' => $response['status'] ?? 'Unknown',
            ]);

            // The receipt is invalid or there was an error
            return response()->json([
                'success' => false,
                'message' => 'Subscription validation failed.',
                'error_code' => $response['status'] ?? 'Unknown',
            ], 400);
        }
    }




}
