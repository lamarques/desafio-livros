<?php

namespace Livros\Exceptions;

use App\Livros\Exceptions\AssuntoException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AssuntoExceptionTest extends TestCase
{
    public function testFactoriesPreenchemMensagemStatusEContexto(): void
    {
        $e1 = AssuntoException::notFound(7);
        $this->assertSame('Assunto 7 não encontrado.', $e1->getMessage());
        $this->assertSame(404, $e1->status());
        $this->assertSame(['codAs' => 7], $e1->context());

        $e2 = AssuntoException::alreadyExists('Matemática');
        $this->assertSame("Assunto 'Matemática' já existe.", $e2->getMessage());
        $this->assertSame(409, $e2->status());
        $this->assertSame(['descricao' => 'Matemática'], $e2->context());

        $e3 = AssuntoException::invalid('Dados inválidos.', ['Descricao' => ['máx 20']]);
        $this->assertSame('Dados inválidos.', $e3->getMessage());
        $this->assertSame(422, $e3->status());
        $this->assertSame(['errors' => ['Descricao' => ['máx 20']]], $e3->context());
    }

    public function testStatusEContextRetornamValoresDefinidosNoConstrutor(): void
    {
        $e = new AssuntoException('msg', 418, ['any' => 1], 99);
        $this->assertSame(418, $e->status());
        $this->assertSame(['any' => 1], $e->context());
        $this->assertSame(99, $e->getCode());
        $this->assertSame('msg', $e->getMessage());
    }

    public function testReportRegistraWarningComContexto(): void
    {
        Log::spy();

        $e = new AssuntoException('erro X', 422, ['k' => 'v']);
        $e->report();

        Log::shouldHaveReceived('warning')->once()->withArgs(function ($message, $context) use ($e) {
            // Mensagem e chaves esperadas
            return $message === 'erro X'
                && isset($context['status'], $context['context'], $context['exception'])
                && $context['status'] === 422
                && $context['context'] === ['k' => 'v']
                && $context['exception'] === $e;
        });
    }

    public function testRenderComoJsonRetornaJsonResponseComPayload(): void
    {
        $e = AssuntoException::invalid('Descrição é obrigatória.', [
            'Descricao' => ['Campo obrigatório.'],
        ]);

        $request = Request::create('/assuntos', 'POST', [], [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $response = $e->render($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(422, $response->getStatusCode());

        $json = $response->getData(true);
        $this->assertSame('Descrição é obrigatória.', $json['message']);
        $this->assertSame(422, $json['status']);
        $this->assertSame(0, $json['code']);
        $this->assertArrayHasKey('context', $json);
        $this->assertSame(['Descricao' => ['Campo obrigatório.']], $json['context']['errors']);
    }

    public function testRenderComoRedirectAdicionaErroComErrosDeValidacaoEPreservaInput(): void
    {
        // Sessão e previous URL para o back()
        $session = $this->app['session.store'];
        $session->start();
        $session->setPreviousUrl('http://localhost/form');

        $e = \App\Livros\Exceptions\AssuntoException::invalid('Falha de validação.', [
            'Descricao' => ['máx 20'],
        ]);

        // Request POST com input
        $request = \Illuminate\Http\Request::create('/assuntos', 'POST', ['Descricao' => 'Muito grande']);
        $request->setLaravelSession($session);

        // 🔑 Torna este request o "request" global da app
        $this->app->instance('request', $request);

        $response = $e->render($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertSame('http://localhost/form', $response->getTargetUrl());
        $this->assertSame('Falha de validação.', $response->getSession()->get('error'));

        $errors = $response->getSession()->get('errors');
        $this->assertTrue($errors->has('Descricao'));
        $this->assertSame(['máx 20'], $errors->get('Descricao'));

        // Agora o old input está presente
        $old = $response->getSession()->getOldInput();
        $this->assertSame('Muito grande', $old['Descricao']);
    }

    public function testRenderComoRedirectSemErrorsNoContextoMantemApenasFlashError(): void
    {
        $session = $this->app['session.store'];
        $session->start();
        $session->setPreviousUrl('http://localhost/list');

        $e = new \App\Livros\Exceptions\AssuntoException('Erro simples.', 400);

        // GET não preserva input
        $request = \Illuminate\Http\Request::create('/assuntos', 'GET');
        $request->setLaravelSession($session);

        $response = $e->render($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertSame('http://localhost/list', $response->getTargetUrl());
        $this->assertSame('Erro simples.', $response->getSession()->get('error'));

        // Sem old input em GET
        $this->assertSame([], $response->getSession()->getOldInput());

        // Sem withErrors()
        $errors = $response->getSession()->get('errors');
        if ($errors !== null) {
            $this->assertFalse($errors->any());
        }
    }
}
