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
        $request->validate([
            'receipt' => 'required|string',
        ]);

        $receiptData = $request->input('receipt');

        // Step 2: Send the receipt to Apple's servers for validation
        $response = $this->sendReceiptToApple($receiptData);

        // Step 3: Handle the response and return appropriate result
        return $this->handleAppleResponse($response);
    }

    private function sendReceiptToApple($receiptData)
    {
        $postData = [
            'receipt-data' => base64_encode($receiptData),
            'password' => base64_encode('0c5e8bbd617e4665963964d5649dcc9a'),
        ];

        Log::info('Sending receipt to Apple', ['postData' => $postData]);

        // Try verifying with the production URL first
        $response = $this->callAppleApi($this->appleSandboxUrl, $postData);

        // If 21007 is returned, retry with the sandbox URL
        if (isset($response['status']) && $response['status'] == 21007) {
            Log::info('Switching to sandbox URL due to status 21007');
            $response = $this->callAppleApi($this->appleSandboxUrl, $postData);
        }

        return $response;
    }

    private function callAppleApi($url, $postData)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $postData);

            Log::info('Raw response from Apple', ['response' => $response->body()]);

            return json_decode($response->body(), true);
        } catch (\Exception $e) {
            Log::error('Error communicating with Apple', ['error' => $e->getMessage()]);

            return [
                'status' => 21199,
                'message' => 'Internal data access error.',
            ];
        }
    }

    private function handleAppleResponse($response)
    {
        if (isset($response['status']) && $response['status'] == 0) {
            return response()->json([
                'success' => true,
                'message' => 'Subscription is valid.',
                'data' => $response,
            ], 200);
        } else {
            Log::error('Apple subscription validation failed', [
                'response' => $response,
                'error_code' => $response['status'] ?? 'Unknown',
            ]);

            return response()->json([
                'success' => false,
                'message' => $response['message'] ?? 'Subscription validation failed.',
                'error_code' => $response['status'] ?? 'Unknown',
            ], 400);
        }
    }


}
