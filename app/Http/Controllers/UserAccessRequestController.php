<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccessRequestFormRequest;
use App\Jobs\HandleAccessRequestSubmissionJob;
use App\Models\AccessRequest;
use App\Services\AccessRequestSummaryBuilder;
use App\Services\AuditLogger;
use App\Support\RequestNumberIssuer;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class UserAccessRequestController extends Controller
{
    private function getOrReserveRequestNumberPreview(Request $request): string
    {
        $existing = $request->session()->get('request_number_preview');
        if (is_string($existing) && trim($existing) !== '') {
            return $existing;
        }

        $requestNumber = RequestNumberIssuer::reserveNext();
        $request->session()->put('request_number_preview', $requestNumber);

        return $requestNumber;
    }

    public function landing(): View
    {
        return view('landing');
    }

    public function form(): View
    {
        /** @var Request $request */
        $request = request();
        $requestNumberPreview = $this->getOrReserveRequestNumberPreview($request);

        return view('user-request-form', [
            'requestNumberPreview' => $requestNumberPreview,
        ]);
    }

    public function verifyCaptcha(Request $request): RedirectResponse
    {
        $token = (string) $request->input('g-recaptcha-response', '');

        if ($token === '') {
            return redirect()->route('landing')->with('error', 'Please complete the CAPTCHA to continue.');
        }

        $secret = (string) config('services.recaptcha.secret_key', '');
        if ($secret === '') {
            return redirect()->route('landing')->with('error', 'CAPTCHA is not configured (missing secret key).');
        }

        try {
            $resp = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);

            $data = $resp->json();
            $ok = (bool) data_get($data, 'success', false);
        } catch (\Throwable $e) {
            report($e);
            $ok = false;
        }

        if (! $ok) {
            return redirect()->route('landing')->with('error', 'CAPTCHA verification failed. Please try again.');
        }

        $request->session()->put('landing_captcha_verified', true);

        return redirect()->route('request.form');
    }

    public function submit(AccessRequestFormRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (($validated['access_type'] ?? null) === 'Temporary' && empty($validated['access_end_date'])) {
            return redirect()
                ->route('request.form')
                ->withInput()
                ->with('error', 'End Date is required for Temporary access.');
        }

        $systems = is_array($request->systems) ? array_values(array_filter($request->systems)) : [];

        $sessionPreview = $request->session()->get('request_number_preview');
        $fromInput = isset($validated['resource_request_number']) && is_string($validated['resource_request_number'])
            ? trim($validated['resource_request_number'])
            : '';
        $validated['request_number'] = (is_string($sessionPreview) && trim($sessionPreview) !== '')
            ? $sessionPreview
            : ($fromInput !== '' ? $fromInput : '');

        if ($validated['request_number'] === '') {
            $validated['request_number'] = RequestNumberIssuer::reserveNext();
        }

        $summary = AccessRequestSummaryBuilder::fromValidated($request, $validated);

        try {
            $accessRequest = AccessRequest::query()->create([
                'request_number' => $validated['request_number'] ?? null,
                'full_name' => $validated['full_name'],
                'email' => $validated['email'] ?? null,
                'mobile_no' => $validated['mobile_no'] ?? null,
                'coop_name' => $validated['coop_name'] ?? null,
                'branch' => $validated['branch'] ?? null,
                'request_date' => $validated['request_date'] ?? null,
                'status' => 'pending',
                'systems' => $systems,
                'summary' => $summary,
            ]);

            $actor = 'Public user';
            $email = trim((string) ($validated['email'] ?? ''));
            if ($email !== '') {
                $actor = 'Public user <'.$email.'>';
            }
            AuditLogger::log(
                $request,
                'form.submitted',
                sprintf('Submitted access request %s.', (string) ($validated['request_number'] ?? '')),
                AccessRequest::class,
                $accessRequest->id,
                ['request_number' => $validated['request_number'] ?? ''],
                $actor,
            );
        } catch (\Throwable $e) {
            report($e);
        }

        $request->session()->forget('request_number_preview');

        $submittedAt = now()->toDateTimeString();
        $jobPayload = array_merge($validated, ['systems' => $systems]);
        dispatch(new HandleAccessRequestSubmissionJob($jobPayload, $submittedAt));

        $request->session()->put('request_summary', $summary);

        return redirect()->route('success');
    }

    public function success(): View
    {
        $summary = session('request_summary');
        if (is_array($summary) && ! empty($summary)) {
            session()->put('request_summary', $summary);
        }

        return view('success', [
            'summary' => $summary,
        ]);
    }

    public function successPdf(Request $request): Response
    {
        $summary = session('request_summary');

        if (! is_array($summary) || empty($summary)) {
            return redirect()->route('success');
        }

        $actor = 'Public user';
        $em = trim((string) ($summary['Email Address'] ?? ''));
        if ($em !== '' && $em !== '-') {
            $actor = 'Public user <'.$em.'>';
        }
        AuditLogger::log(
            $request,
            'form.pdf.downloaded',
            'Public user downloaded PDF of submitted access request.',
            null,
            null,
            ['request_number' => $summary['Request Number'] ?? ''],
            $actor,
        );

        /** @var PDF $pdf */
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('success-pdf', [
            'summary' => $summary,
        ])->setPaper('a4', 'portrait');

        $filename = 'user-access-request-'.now()->format('Ymd-His').'.pdf';

        return $pdf->download($filename);
    }
}
