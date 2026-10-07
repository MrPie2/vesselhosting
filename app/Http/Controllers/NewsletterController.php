<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsletterController
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:190'],
        ]);

        $exists = DB::table('newsletter_subscribers')
            ->where('email', $data['email'])
            ->exists();

        if (!$exists) {
            DB::table('newsletter_subscribers')->insert([
                'email' => $data['email'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('newsletter_success', 'You are subscribed. We will keep you updated.');
    }
}
