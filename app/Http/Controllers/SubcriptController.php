<?php

namespace App\Http\Controllers;

use App\Models\crf;
use App\Models\Subcript;
use App\Models\Supplier;
use Firebase\JWT\JWT;
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

    /**
     * Verify the iOS subscription receipt with Apple.
     */
    public function verifySubscription(Request $request)
    {
        // Step 1: Validate the request
        $request->validate([
            'receipt' => 'required|string',  // Expecting Base64-encoded receipt
        ]);

        // Step 2: Get the Base64-encoded receipt
        $encodedReceipt = $request->input('receipt');

        // Step 3: Send the receipt to Apple's servers for validation
        $response = $this->sendReceiptToApple($encodedReceipt);

        // Step 4: Handle the response and return appropriate result
        return $this->handleAppleResponse($response);
    }

    /**
     * Send the receipt data to Apple's servers for verification.
     */
    function generateAppleJWT()
    {
        $privateKey = file_get_contents(storage_path('storage/app/AuthKey_Y86Q74HSM8.p8'));
        $keyId = 'Y86Q74HSM8'; // Your Apple Key ID
        $issuerId = 'VZU47BRDUA'; // Your Apple Developer Team ID

        $now = time();
        $token = [
            'iss' => $issuerId,
            'iat' => $now,
            'exp' => $now + 3600, // Token expiration (1 hour)
            'aud' => 'appstoreconnect-v1',
            'sub' => $issuerId,
        ];

        $jwt = JWT::encode($token, $privateKey, 'ES256', $keyId);

        return $jwt;
    }

    function fetchSubscriptionData($transactionId)
    {
        // Call the JWT generation function
        $jwt = $this->generateAppleJWT();

        // Send a GET request to the Apple API with the JWT in the Authorization header
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $jwt,
        ])->get("https://api.appstoreconnect.apple.com/v1/transactions/{$transactionId}");

        // Handle the response (optional error checking)
        if ($response->successful()) {
            return $response->json();  // Return the JSON response
        } else {
            return $response->body();  // Return error details
        }
    }
}
