<?php
namespace App\Http\Controllers;

use App\Contracts\Registrar;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DomainSearchController
{
    public function search(Request $request, Registrar $registrar)
    {
        $data = $request->validate(['domain'=>'required|string|max:253']);
        $domain = strtolower(trim($data['domain']));
        $domain = preg_replace('#^https?://#','',$domain);
        $domain = trim($domain,'/.');

        if (!Str::contains($domain,'.')) {
            return back()->withErrors(['domain'=>'Enter a full domain such as example.com.'])->withInput();
        }

        try {
            $result = $registrar->checkAvailability($domain);
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['domain'=>'Domain search is temporarily unavailable.'])->withInput();
        }

        return view('domain-search', compact('domain','result'));
    }
}