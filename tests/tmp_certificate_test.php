<?php
$project = dirname(__DIR__);
require $project.'/vendor/autoload.php';

$app = require_once $project.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\ExaminationHub\Models\GeneralExamSubmission;
use Illuminate\Http\Request;

$submission = GeneralExamSubmission::whereNotNull('submitted_at')->with('assignment')->first();
if (! $submission) {
    echo "NO_SUBMISSION\n";
    exit(0);
}

$email = $submission->participant_email;
echo "SUBMISSION: {$submission->id}\n";
echo "submitted_at: ".($submission->submitted_at?->toDateTimeString() ?? 'null')."\n";
echo "isSubmitted(): ".($submission->isSubmitted() ? 'true' : 'false')."\n";
echo "assignment->canShowResults(): ".(($submission->assignment->canShowResults() ?? false) ? 'true' : 'false')."\n";
echo "assignment->show_score_breakdown: ".($submission->assignment->show_score_breakdown ? 'true' : 'false')."\n";

$request = Request::create('/', 'GET', ['email' => $email]);
$controller = new App\ExaminationHub\Controllers\ParticipantResultsController();

try {
    $res = $controller->certificate($request, $submission);
    echo 'CERTIFICATE_RESPONSE_TYPE: '.(is_object($res) ? get_class($res) : gettype($res))."\n";
    if ($res instanceof Illuminate\Http\RedirectResponse) {
        echo 'CERT_REDIRECT_TO: '.$res->headers->get('Location')."\n";
    }
} catch (\Throwable $e) {
    echo "CERT_EXCEPTION: ".$e->getMessage()."\n".$e->getTraceAsString()."\n";
}

try {
    $res2 = $controller->certificatePdf($request, $submission);
    if (is_object($res2)) {
        echo 'PDF_RETURN_CLASS: '.get_class($res2)."\n";
        if (method_exists($res2, 'getStatusCode')) {
            echo 'PDF_STATUS: '.$res2->getStatusCode()."\n";
        }
        if ($res2 instanceof Illuminate\Http\RedirectResponse) {
            echo 'PDF_REDIRECT_TO: '.$res2->headers->get('Location')."\n";
        }
    } else {
        echo 'PDF_RETURN_TYPE: '.gettype($res2)."\n";
    }
} catch (\Throwable $e) {
    echo "PDF_EXCEPTION: ".$e->getMessage()."\n".$e->getTraceAsString()."\n";
}
