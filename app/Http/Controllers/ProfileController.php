<?php

namespace App\Http\Controllers;

use App\Models\CountryDetails;
use App\Models\QueuedbUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    // public function index()
    // {
    //     // Retrieve the logged-in user
    //     $user = Auth::user();

    //     // Alternatively, if you have a separate model for profiles
    //     // $profile = $user->profile;

    //     // Return the user data to the view
    //     return view('auth-profile.profile', [
    //         'user' => $user
    //     ]);
    // }
    public function index()
    {
        // Retrieve the logged-in user
        $user = auth()->user();

        // Retrieve country names from the database
        $countries = CountryDetails::pluck('countryName', 'countryName');

        // Retrieve telephone prefixes from the database
        $telephonePrefix = CountryDetails::pluck('telephonePrefix');

        // Return the user data, country names, and telephone prefixes to the view
        return view('auth-profile.profile', [
            'user' => $user,
            'countries' => $countries,
            'telephonePrefix' => $telephonePrefix,
        ]);
    }

    public function update(Request $request, QueuedbUser $user)
    {
        try {
            $data = $request->validate([
                'company_name' => 'required|string|max:250',
                'company_address' => 'required',
                'company_address2' => 'nullable', // Add your validation rules for company_address2 if needed
                'company_zip' => 'required|string|max:10', // Add your validation rules for zip if needed
                'country' => 'required|string|max:255', // Add your validation rules for country if needed
                'company_person_name' => 'required|string|max:250',
                'company_person_mobile' => 'required|string|max:20',
                'telephonePrefix' => 'required',
            ], [
                'company_name.required' => 'The company name is required.',
                'company_name.string' => 'The company name must be a string.',
                'company_name.max' => 'The company name must not exceed :max characters.',
                'company_address.required' => 'The company address is required.',
                'company_person_name.required' => 'The person name is required.',
                'company_person_name.string' => 'The person name must be a string.',
                'company_person_name.max' => 'The person name must not exceed :max characters.',
                'company_person_mobile.required' => 'The person mobile is required.',
                'company_person_mobile.string' => 'The person mobile must be a string.',
                'company_person_mobile.max' => 'The person mobile must not exceed :max characters.',
                'telephonePrefix.required' => 'The telephone prefix is required.',
                'company_zip.required' => 'zip is required.',
                'country.required' => 'Country name is required.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $user->update($data);

        Session::flash('success', '<i class="fa fa-check-circle"></i> Profile updated successfully');

        // For error message with an icon
        Session::flash('error', '<i class="fa fa-exclamation-circle"></i> Profile not updated');

        // return redirect('profile')->with('success', 'Profile updated successfully');
        return redirect()->back();
        // return redirect('profile');
    }
}
