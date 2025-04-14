<?php 
namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    // Method to show the Terms of Use page
    public function showTermsOfUse()
    {
        // Retrieve the 'terms-of-use' page data
        $page = Page::where('slug', 'terms-of-use')->first();

        // Pass the page data to the view
        return view('frontend.termsOfUse', compact('page'));
    }

    // Method to show the Privacy Policy page
    public function showPrivacyPolicy()
    {
        // Retrieve the 'privacy-policy' page data
        $page = Page::where('slug', 'privacy-policy')->first();

        // Pass the page data to the view
        return view('frontend.privacyPolicy', compact('page'));
    }
}
