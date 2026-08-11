<?php

use App\Facade\RouteParser;
use App\Facade\Session;
use DI\Container;
use Jaxon\Config\Config;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;
use Slim\Middleware\Session as SessionMiddleware;

use function Jaxon\jaxon;

return function(App $app) {
    /** @var Container */
    $container = $app->getContainer();

    // Middleware to initialize the session.
    /** @var Config */
    $config = $container->get(Config::class);
    $startMiddleware = new SessionMiddleware($config->getOption('session'));

    // Middleware to check the user session.
    $checkMiddleware = function(Request $request, RequestHandler $handler) use($app): Response {
        if (Session::exists('user')) {
            return $handler->handle($request);
        }

        // No session opened, redirect to the login page.
        $jaxon = jaxon();
        if ($jaxon->canProcessRequest()) {
            $response = $jaxon->ajaxResponse();
            $response->redirect(RouteParser::urlFor('auth_login'));
            return $response->toPsr();
        }

        $response = $app->getResponseFactory()->createResponse();
        return $response->withStatus(302)
            ->withHeader('Location', RouteParser::urlFor('auth_login'));
    };

    // Middleware to start a new user session.
    $loginMiddleware = function(Request $request, RequestHandler $handler) use($app): Response {
        $container = $app->getContainer();
        $response = $app->getResponseFactory()->createResponse();

        // Validate the inputs.
        $params = $request->getParsedBody();
        $email = $params['email'];
        $password = $params['password'];
        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'The email address is invalid.';
        }
        /*
         * Password verification
         * - minimum length should be 8.
         * - at least one uppercase letter,
         * - at least one lowercase letter,
         * - at least one digit,
         * - at least one special character.
         */
        // $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        // if (!preg_match($passwordPattern, $password)) {
        //     $errors['password'] = 'The password is invalid';
        // }
        /*
         * Password verification
         * - minimum length should be 8.
         * - at least one letter.
         */
        $passwordPattern = '/^(?=.*[A-Za-z]).{8,}$/';
        if (!preg_match($passwordPattern, $password)) {
            $errors['password'] = 'The password is invalid.';
        }
        if (count($errors) > 0) {
            Session::set('errors', $errors);
            return $response->withStatus(302)
                ->withHeader('Location', RouteParser::urlFor('auth_login'));
        }

        // Find the user.
        /** @var Config */
        $config = $container->get(Config::class);
        $users = array_filter($config->getOption('users'),
            fn(array $u) => $u['email'] === $email);
        if (isset($users[0]) && password_verify($password, $users[0]['password'])) {
            Session::id(true); // Reset the session id.
            Session::set('user', (object)$users[0]);
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        Session::set('errors', [
            'email' => 'The user authentication failed.',
        ]);
        return $response->withStatus(302)
            ->withHeader('Location', RouteParser::urlFor('auth_login'));
    };

    // Middleware to close the user session.
    $logoutMiddleware = function(Request $request, RequestHandler $handler): Response {
        Session::destroy();
        return $handler->handle($request);
    };

    // The auth routes.
    $redirectHandler = fn(Request $request, Response $response): Response =>
        $response->withHeader('Location', RouteParser::urlFor('auth_login'));
    $app->post('/login', $redirectHandler)
        ->add($loginMiddleware)
        ->add($startMiddleware)
        ->setName('auth_login');
    $app->redirect('/logout', '/login', 302)
        ->add($logoutMiddleware)
        ->add($startMiddleware)
        ->setName('auth_logout');

    return [$startMiddleware, $checkMiddleware];
};
