<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class EmailTemplateController
{
    public function index(): Response
    {
        return Inertia::render('EmailTemplates/Index', [
            'templates' => EmailTemplate::orderBy('name')->get()
        ]);
    }

    public function edit(EmailTemplate $emailTemplate): Response
    {
        return Inertia::render('EmailTemplates/Form', [
            'template' => $emailTemplate
        ]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $emailTemplate->update($data);

        return redirect()->route('email-templates.index')->with('success', 'Email template updated successfully.');
    }

    public function preview(EmailTemplate $emailTemplate)
    {
        if (app()->bound('debugbar')) {
            app('debugbar')->disable();
        }

        $body = $emailTemplate->body;
        
        $fakeData = [
            'app_name' => config('app.name'),
            'user_name' => 'John Doe',
            'reset_url' => url('/password/reset/token'),
            'verify_url' => url('/email/verify/token'),
            'brand_name' => config('app.name'),
            'footer_brand' => '© ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.',
        ];

        foreach ($fakeData as $key => $val) {
            $body = preg_replace('/\{\{\s*' . preg_quote($key, '/') . '\s*\}\}/i', e($val), $body);
        }
        
        return response($body)->header('Content-Security-Policy', "default-src 'none'; style-src 'unsafe-inline';");
    }
}
