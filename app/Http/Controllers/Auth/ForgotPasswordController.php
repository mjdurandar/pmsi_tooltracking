<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // If you want to catch any errors related to mail sending, wrap the sendResetLinkEmail method call in a try-catch block
        try {
            $response = $this->broker()->sendResetLink(
                $request->only('email')
            );
        } catch (\Exception $e) {
            // Log the error or return a response indicating the failure to send the reset link
            return response()->json(['error' => 'Failed to send reset link: ' . $e->getMessage()], 500);
        }

        // Check the response and return an appropriate JSON response or redirect response
        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }
}
