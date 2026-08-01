<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Illuminate\Http\Request;

class PayslipVerifyController extends Controller
{
    /**
     * Generate the same deterministic doc ID used in payslip-pdf.blade.php.
     * Must stay in sync with the Blade template.
     */
    public static function generateDocId(Payroll $payroll): string
    {
        $hash = strtoupper(substr(hash('sha256',
            $payroll->id .
            $payroll->employee_id .
            $payroll->year .
            $payroll->month .
            $payroll->total_salary .
            $payroll->pay_date
        ), 0, 12));

        return 'SCR-' . str_pad($payroll->id, 5, '0', STR_PAD_LEFT) . '-' . $hash;
    }

    /**
     * Show the verification form (public — no auth required).
     */
    public function index()
    {
        return view('verify.index');
    }

    /**
     * Verify a submitted Doc ID against the database.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'doc_id' => ['required', 'string', 'regex:/^SCR-\d{5}-[A-F0-9]{12}$/'],
        ], [
            'doc_id.regex' => 'Format Doc ID tidak valid. Contoh: SCR-00042-A3F9C1D82E4B',
        ]);

        $docId = strtoupper(trim($request->doc_id));

        // Extract the payroll ID from the doc ID (positions 4–8)
        // Format: SCR-{05d}-{12hex}
        $parts = explode('-', $docId);
        // $parts = ['SCR', '00042', 'A3F9C1D82E4B']
        if (count($parts) !== 3) {
            return back()->withInput()->with('result', [
                'valid'   => false,
                'doc_id'  => $docId,
                'message' => 'Format Doc ID tidak dikenali.',
            ]);
        }

        $payrollId = (int) $parts[1];

        $payroll = Payroll::with([
            'employee:id,name,employee_code,department_id,position_id',
            'employee.department:id,name',
            'employee.position:id,name',
        ])->where('status', 'paid')->find($payrollId);

        if (! $payroll) {
            return back()->withInput()->with('result', [
                'valid'   => false,
                'doc_id'  => $docId,
                'message' => 'Dokumen tidak ditemukan dalam sistem.',
            ]);
        }

        $expected = self::generateDocId($payroll);

        if (! hash_equals($expected, $docId)) {
            return back()->withInput()->with('result', [
                'valid'   => false,
                'doc_id'  => $docId,
                'message' => 'Doc ID tidak cocok — dokumen mungkin telah dimodifikasi.',
            ]);
        }

        return back()->withInput()->with('result', [
            'valid'        => true,
            'doc_id'       => $docId,
            'employee'     => $payroll->employee?->name ?? '-',
            'employee_code'=> $payroll->employee?->employee_code ?? '-',
            'department'   => $payroll->employee?->department?->name ?? '-',
            'position'     => $payroll->employee?->position?->name ?? '-',
            'period'       => $payroll->monthName(),
            'pay_date'     => $payroll->pay_date?->translatedFormat('d F Y') ?? '-',
            'total_salary' => 'Rp ' . number_format($payroll->total_salary, 0, ',', '.'),
            'status'       => ucfirst($payroll->status),
            'verified_at'  => now()->translatedFormat('d F Y, H:i:s'),
        ]);
    }
}
