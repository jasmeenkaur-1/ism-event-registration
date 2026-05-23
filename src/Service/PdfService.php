<?php
namespace App\Service;

use App\Entity\Registration;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class PdfService
{
    public function generateTicket(Registration $registration): string
    {
        $qrCode = new QrCode($registration->getTicketNumber());
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $qrBase64 = base64_encode($result->getString());

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; background: #fff; }
                .header { background: #1c2b4a; color: white; padding: 25px; text-align: center; }
                .header h1 { color: #f0a500; margin: 0; font-size: 20px; }
                .header p { margin: 5px 0 0 0; font-size: 12px; color: #ccc; }
                .orange-bar { background: #f0a500; height: 5px; }
                .body { padding: 20px 0; }
                .ticket-number { font-size: 22px; font-weight: bold; color: #1c2b4a; background: #f5f5f5; padding: 10px 20px; display: inline-block; margin: 15px 0; }
                .info-row { margin: 10px 0; font-size: 13px; color: #333; }
                .info-row strong { color: #1c2b4a; display: block; }
                .qr-section { text-align: center; margin-top: 25px; }
                .qr-section p { font-size: 11px; color: #888; margin-top: 8px; }
                .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #eee; color: #888; font-size: 11px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>International School of Management</h1>
                <p>Data Science Institute Presentation Roadshow 2026</p>
            </div>
            <div class="orange-bar"></div>
            <div class="body">
                <p style="font-size:12px; color:#888; text-transform:uppercase; letter-spacing:1px;">Your Ticket</p>
                <div class="ticket-number">' . $registration->getTicketNumber() . '</div>
                <div class="info-row"><strong>Name</strong>' . $registration->getFirstName() . ' ' . $registration->getLastName() . '</div>
                <div class="info-row"><strong>Email</strong>' . $registration->getEmail() . '</div>
                <div class="info-row"><strong>Company</strong>' . ($registration->getCompany() ?? 'Not provided') . '</div>
                <div class="info-row"><strong>Meal Preference</strong>' . ucfirst($registration->getMealPreference()) . '</div>
                <div class="info-row"><strong>Event Location</strong>ISM Campus Hamburg, Brooktorkai 22, 20457 Hamburg</div>
                <div class="info-row"><strong>Date</strong>15 June 2026 at 10:00 AM</div>
                <div class="qr-section">
                    <img src="data:image/png;base64,' . $qrBase64 . '" width="160" height="160">
                    <p>Please scan this QR code at check-in</p>
                </div>
            </div>
            <div class="footer">
                <p>International School of Management | International. Individual. Inspiring. | en.ism.de</p>
            </div>
        </body>
        </html>';

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}