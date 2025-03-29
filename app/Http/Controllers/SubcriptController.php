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

    public function getApps($transactionId)
    {
        // Key ID, Private Key, and Issuer ID from App Store Connect API
        $apple_key_id = 'NM6QRRGT5K';  // Your Key ID from App Store Connect

        // Your Apple Private Key (.p8 file content)
        $apple_key = <<<EOD
-----BEGIN PRIVATE KEY-----
MIGTAgEAMBMGByqGSM49AgEGCCqGSM49AwEHBHkwdwIBAQQgr9gY1hXg1SWPcvfg
v794xalFutgRF3M9vvIyvPnFVRKgCgYIKoZIzj0DAQehRANCAASKd8AgVRH3+6o3
3KSqgQU8cEGquyXs5D8ElH5K1NBEnbHv6ATfC5av8y+zWYmWgSKp5KvZWLsU/jMj
ZFLR0/dM
-----END PRIVATE KEY-----
EOD;

        $apple_issuer_id = '710b826a-8de4-4ca1-8a02-80f67cf863cd';  // Your Issuer ID from App Store Connect

        // Create JWT payload array
        $payload_array = [
            'iss' => $apple_issuer_id,       // Issuer ID from App Store Connect
            'aud' => 'appstoreconnect-v1',   // Audience is always appstoreconnect-v1
            'iat' => time(),                 // Issued at time
            'exp' => time() + (60 * 5)       // Expiration time (5 minutes)
        ];

        // Create JWT payload header
        $payload_header = [
            "kid" => $apple_key_id,    // Key ID from App Store Connect
            "typ" => "JWT"
        ];

        // Generate JWT token using ES256 algorithm
        $jwtToken = JWT::encode($payload_array, $apple_key, 'ES256', $apple_key_id, $payload_header);

        // Log or print the JWT token (optional for debugging)
        Log::info('Generated Apple JWT: ' . $jwtToken);

        // Make the API request to App Store Connect using the JWT token
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $jwtToken
        ])->get('https://api.storekit.itunes.apple.com/inApps/v1/history/{transactionId}} ');

        // Handle the response
        if ($response->successful()) {
            return response()->json($response->json());  // Return the API response as JSON
        } else {
            return response()->json([
                'error' => 'Failed to fetch apps from App Store Connect',
                'details' => $response->body()
            ], $response->status());
        }
    }
}
