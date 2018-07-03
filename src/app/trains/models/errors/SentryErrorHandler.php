<?php
namespace trains\models\errors;
/**
 * Sentry Error Handler.
 *
 * Handles the sentry errors we're seeing coming through.
 */
class SentryErrorHandler {

    public function __invoke($request, $response, $exception) {
        $this->makeSentryNoticeRequest($exception);
        $this->showErrorMessageForDevelopment($exception);
        return $this->showGenericErrorJSON($response);
    }

    public static function initialiseBaseErrors() {
        $client = new \Raven_Client('http://8d7f7539def84a55acdf47164f64d5da:8200543f63ab4d61afbc66a59780dba6@159.65.85.168:9000/2');
        $error_handler = new \Raven_ErrorHandler($client);
        $error_handler->registerExceptionHandler();
        $error_handler->registerErrorHandler();
        $error_handler->registerShutdownFunction();
    }

   private function makeSentryNoticeRequest($exception) {
       $client = new \Raven_Client('http://8d7f7539def84a55acdf47164f64d5da:8200543f63ab4d61afbc66a59780dba6@159.65.85.168:9000/2');
       $client->captureMessage($exception);
   }

   private function showErrorMessageForDevelopment($exception) {
       if ($GLOBALS['isShowingErrors']) {
           $html = sprintf('<div><strong>Type:</strong> %s</div>', get_class($exception));

           if (($code = $exception->getCode())) {
               $html .= sprintf('<div><strong>Code:</strong> %s</div>', $code);
           }

           if (($message = $exception->getMessage())) {
               $html .= sprintf('<div><strong>Message:</strong> %s</div>', htmlentities($message));
           }

           if (($file = $exception->getFile())) {
               $html .= sprintf('<div><strong>File:</strong> %s</div>', $file);
           }

           if (($line = $exception->getLine())) {
               $html .= sprintf('<div><strong>Line:</strong> %s</div>', $line);
           }

           if (($trace = $exception->getTraceAsString())) {
               $html .= '<h2>Trace</h2>';
               $html .= sprintf('<pre>%s</pre>', htmlentities($trace));
           }
           print $html;
           exit;
       }
   }

   private function showGenericErrorJSON($response) {
       return $response
           ->withStatus(500)
           ->withHeader('Content-Type', 'application/json')
           ->withJSON(
                [
                   "error"=>"There was a problem loading this page",
                ]
           );
   }
}
