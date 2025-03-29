<?php

namespace App\Http\Controllers;

use App\Models\ChristmasForm;
use Illuminate\Http\Request;

class ChristmasFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $christmasForm = ChristmasForm::all();
            return $this->sendResponse($christmasForm);
        }
        catch (\Exception $e){
            return $this->sendError([], 400, $e->getMessage());
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'formData.name' => 'required|string|max:255',
                'formData.email' => 'nullable|email|max:255',
                'formData.phone' => 'required|string|max:15',
                'formData.gender' => 'required|string|in:male,female,other', // Adjust the valid values as needed
                'formData.how_many' => 'required|integer|min:1', // Adjust validation rules as needed
            ]);

            // Extract form data
            $formData = $validatedData['formData'];

            // Create and save the ChristmasForm entry
            $christmasForm = new ChristmasForm();
            $christmasForm->name = $formData['name'];
            $christmasForm->email = $formData['email'] ?? ''; // Default to empty string if email is null
            $christmasForm->phone = $formData['phone'];
            $christmasForm->gender = $formData['gender'];
            $christmasForm->how_many = $formData['how_many'];
            $christmasForm->save();

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Form data saved successfully.',
                'data' => $christmasForm,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Handle general exceptions
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function detail($id)
    {
        try{
            $christmasForm = ChristmasForm::find($id);
            if(!$christmasForm){
                return $this->sendError([], 400, 'Form not found');
            }
            return $this->sendResponse($christmasForm);
        }
        catch (\Exception $e){
            return $this->sendError([], 400, $e->getMessage());
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
    public function show(ChristmasForm $christmasForm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChristmasForm $christmasForm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChristmasForm $christmasForm)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChristmasForm $christmasForm)
    {
        //
    }
}
