<?php

namespace Livros\Presentation\Http\Requests;

use App\Livros\Presentation\Http\Requests\LivroCreateRequest;
use Tests\TestCase;

class LivroCreateRequestTest extends TestCase
{
    public function testPrepareForValidationReindexaArraysDeAutorIDEAssuntoID(): void
    {
        $req = LivroCreateRequest::create('/fake', 'POST', [
            'AutorID'   => [5 => 10, 9 => 11],
            'AssuntoID' => ['a' => 1, 1 => 2, 7 => 3],
        ]);

        $ref = new \ReflectionMethod(LivroCreateRequest::class, 'prepareForValidation');
        $ref->setAccessible(true);
        $ref->invoke($req);

        $all = $req->request->all();

        $this->assertSame([10, 11], $all['AutorID']);
        $this->assertSame([1, 2, 3], $all['AssuntoID']);
        $this->assertSame(0, array_key_first($all['AutorID']));
        $this->assertSame(0, array_key_first($all['AssuntoID']));
    }

    public function testPrepareForValidationEnvelopaValorEscalarEmArray(): void
    {
        $req = LivroCreateRequest::create('/fake', 'POST', [
            'AutorID'   => 42,
            'AssuntoID' => 7,
        ]);

        $ref = new \ReflectionMethod(LivroCreateRequest::class, 'prepareForValidation');
        $ref->setAccessible(true);
        $ref->invoke($req);

        $all = $req->request->all();

        $this->assertSame([42], $all['AutorID']);
        $this->assertSame([7], $all['AssuntoID']);
    }
}
