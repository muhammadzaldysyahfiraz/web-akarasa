<?php

namespace App\Http\Controllers;

use App\Models\Penanam;
use App\Services\DashboardReportService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LaporanController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return redirect()->route('user.dashboard');
    }

    public function pdf(Request $request, DashboardReportService $report): Response
    {
        $penanam = $request->filled('penanam_id')
            ? Penanam::query()->findOrFail($request->integer('penanam_id'))
            : null;

        $options = new Options;
        $options->setDefaultFont('Helvetica');
        $document = new Dompdf($options);
        $document->loadHtml(
            view('laporan.pdf', ['report' => $report->summary($penanam)])->render(),
            'UTF-8'
        );
        $document->setPaper('A4');
        $document->render();

        $filename = $penanam
            ? 'laporan-kebun-' . Str::slug($penanam->nama) . '.pdf'
            : 'laporan-kebun-toga.pdf';

        return response($document->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}