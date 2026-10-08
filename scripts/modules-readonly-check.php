<?php
// Read-only authenticated checks: no test users or operations in the live DB.
require dirname(__DIR__).'/vendor/autoload.php';
use App\Entity\User;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
$kernel = new App\Kernel('prod', false);
$kernel->boot();
$user = $kernel->getContainer()->get('doctrine')->getManager()->getRepository(User::class)->findOneBy(['is_root' => true, 'is_active' => true]);
if (!$user) throw new RuntimeException('No active administrator for read-only checks');
$jwt = $kernel->getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($user);
foreach (['/api/v1/tasks/bootstrap', '/api/v1/tasks/feedback', '/api/v1/tasks/audit', '/api/v1/accounting/bootstrap', '/api/v1/accounting/audit'] as $path) {
    $request = Request::create($path, server: ['HTTP_AUTHORIZATION' => 'Bearer '.$jwt]);
    $response = $kernel->handle($request);
    if ($response->getStatusCode() !== 200 || !is_array(json_decode($response->getContent(), true))) throw new RuntimeException($path.' HTTP '.$response->getStatusCode());
    echo 'PASS API '.$path."\n";
    $kernel->terminate($request, $response);
}
foreach (['/tasks', '/tasks/table', '/tasks/duties', '/tasks/profile', '/tasks/ranking', '/tasks/admin', '/accounting', '/home/users'] as $path) {
    $session = new Session(new MockArraySessionStorage());
    $session->start();
    $session->set('_security_main', serialize(new UsernamePasswordToken($user, 'main', $user->getRoles())));
    $request = Request::create($path, cookies: [$session->getName() => $session->getId()]);
    $request->setSession($session);
    $response = $kernel->handle($request);
    if ($response->getStatusCode() !== 200) throw new RuntimeException($path.' HTTP '.$response->getStatusCode());
    foreach (['Репостер', 'Сайт', 'Статьи', 'Партнёрки', 'Статистика', 'Прозвон', 'Базы', 'AI'] as $placeholder) {
        if (str_contains($response->getContent(), 'data-tip="'.$placeholder.'"')) throw new RuntimeException('Placeholder module in main menu: '.$placeholder);
    }
    echo 'PASS PAGE '.$path."\n";
    $kernel->terminate($request, $response);
}
echo "PASS: production API and page rendering\n";
