<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmtpProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;

class SmtpProfileController extends Controller
{
    public function index()
    {
        $profiles = SmtpProfile::orderBy('name')->get();

        return view('admin.smtp-profiles.index', compact('profiles'));
    }

    public function create()
    {
        return view('admin.smtp-profiles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'encryption' => 'required|in:tls,ssl,none',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:500',
            'from_email' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        if (!empty($data['is_default'])) {
            SmtpProfile::where('is_default', true)->update(['is_default' => false]);
        }

        SmtpProfile::create($data);

        return redirect()
            ->route('admin.smtp-profiles.index')
            ->with('success', 'SMTP profili oluşturuldu.');
    }

    public function edit(SmtpProfile $smtp_profile)
    {
        return view('admin.smtp-profiles.edit', compact('smtp_profile'));
    }

    public function update(Request $request, SmtpProfile $smtp_profile)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'encryption' => 'required|in:tls,ssl,none',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:500',
            'from_email' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if (!empty($data['is_default'])) {
            SmtpProfile::where('is_default', true)
                ->where('id', '!=', $smtp_profile->id)
                ->update(['is_default' => false]);
        }

        $smtp_profile->update($data);

        return redirect()
            ->route('admin.smtp-profiles.index')
            ->with('success', 'SMTP profili güncellendi.');
    }

    public function destroy(SmtpProfile $smtp_profile)
    {
        $smtp_profile->delete();

        return redirect()
            ->route('admin.smtp-profiles.index')
            ->with('success', 'SMTP profili silindi.');
    }

    public function sendTest(Request $request, SmtpProfile $smtp_profile)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        try {
            $transport = (new EsmtpTransportFactory())->create(new Dsn(
                $smtp_profile->encryption === 'ssl' ? 'smtps' : 'smtp',
                $smtp_profile->host,
                $smtp_profile->username,
                $smtp_profile->password,
                $smtp_profile->port
            ));

            $email = (new \Symfony\Component\Mime\Email())
                ->from(new \Symfony\Component\Mime\Address($smtp_profile->from_email, $smtp_profile->from_name))
                ->to($request->test_email)
                ->subject('Grafike CMS - SMTP Test')
                ->text('Bu bir test e-postasıdır. SMTP profili başarıyla çalışıyor.');

            $transport->send($email);

            return back()->with('success', 'Test e-postası başarıyla gönderildi.');
        } catch (\Throwable $e) {
            return back()->with('error', 'SMTP hatası: ' . $e->getMessage());
        }
    }
}
