<?php

namespace App\Http\Controllers;

use App\Services\DashboardReportService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class LaporanController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return redirect()->route('user.dashboard');
    }

    public function pdf(DashboardReportService $report): Response
    {
        $options = new Options;
        $options->setDefaultFont('Helvetica');
        $document = new Dompdf($options);
        $document->loadHtml(view('laporan.pdf', ['report' => $report->summary()])->render(), 'UTF-8');
        $document->setPaper('A4');
        $document->render();

        return response($document->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="laporan-kebun-toga.pdf"',
        ]);
    }
}
