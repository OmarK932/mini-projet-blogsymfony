<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

$autoload = dirname(__DIR__).'/vendor/autoload.php';
require $autoload;

// Load .env to provide required env vars (DEFAULT_URI etc.)
if (file_exists(dirname(__DIR__).'/.env')) {
    (new \Symfony\Component\Dotenv\Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

$kernel = new Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();

// Initialize a session-aware request to GET the login page
$request = Request::create('/login', 'GET');
// Use container session if available
if ($container->has('session')) {
    $session = $container->get('session');
    $request->setSession($session);
}

$response = $kernel->handle($request);
$content = $response->getContent();

// Extract CSRF token from the form input
$token = null;
if (preg_match('/name="_csrf_token"\s+value="([^"]+)"/', $content, $m)) {
    $token = $m[1];
}

if (!$token) {
    echo "CSRF token not found in login page.\n";
    exit(1);
}

// TODO: set these credentials to a real user in your DB
// Try to fetch a user from the database to get a real email if available
try {
    $em = $container->get('doctrine')->getManager();
    $userRepo = $em->getRepository(\App\Entity\User::class);
    $user = $userRepo->findOneBy([]);
    if ($user) {
        $email = $user->getEmail();
    } else {
        $email = 'test@example.com';
    }
} catch (\Throwable $e) {
    $email = 'test@example.com';
}

$password = 'wrongpass';

$postRequest = Request::create('/login', 'POST', [
    'email' => $email,
    'password' => $password,
    '_csrf_token' => $token,
]);

if (isset($session)) {
    $postRequest->setSession($session);
}

$response2 = $kernel->handle($postRequest);

$status = $response2->getStatusCode();
echo "POST /login returned status: $status\n";
// show if redirected
if ($response2->isRedirect()) {
    echo "Redirect to: " . $response2->headers->get('Location') . "\n";
}

// Show part of response for debugging
$body = $response2->getContent();
$snippet = substr(strip_tags($body), 0, 800);
echo "Response snippet:\n" . $snippet . "\n";

$kernel->shutdown();
