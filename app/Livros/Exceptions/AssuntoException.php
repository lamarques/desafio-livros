<?php

namespace App\Livros\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AssuntoException extends Exception
{
    protected int $status;
    protected array $context;

    public function __construct(
        string $message,
        int $status = 422,
        array $context = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->status  = $status;
        $this->context = $context;
    }

    /** Fábricas semânticas úteis */
    public static function notFound(int $codAs): self
    {
        return new self("Assunto {$codAs} não encontrado.", 404, ['codAs' => $codAs]);
    }

    public static function alreadyExists(string $descricao): self
    {
        return new self("Assunto '{$descricao}' já existe.", 409, ['descricao' => $descricao]);
    }

    public static function invalid(string $message, array $errors = []): self
    {
        // $errors pode ser ['Descricao' => ['tamanho máximo 20']] etc.
        return new self($message, 422, ['errors' => $errors]);
    }

    public function status(): int
    {
        return $this->status;
    }

    public function context(): array
    {
        return $this->context;
    }

    /** Chamado automaticamente pelo Handler para logging */
    public function report(): void
    {
        Log::warning($this->getMessage(), [
            'status'    => $this->status,
            'context'   => $this->context,
            'exception' => $this,
        ]);
    }

    /** Chamado automaticamente pelo Handler para resposta HTTP */
    public function render(Request $request): JsonResponse|RedirectResponse
    {
        $payload = [
            'message' => $this->getMessage(),
            'status'  => $this->status,
            'code'    => $this->getCode(),
        ];
        if (!empty($this->context)) {
            $payload['context'] = $this->context;
        }

        // APIs / AJAX
        if ($request->expectsJson()) {
            return response()->json($payload, $this->status);
        }

        // Web: redireciona de volta com flash e (se houver) erros de validação
        $redirect = back();

        if (isset($this->context['errors']) && is_array($this->context['errors'])) {
            $redirect = $redirect->withErrors($this->context['errors']);
        }

        // Preserva input em métodos que não são GET/HEAD
        if (!in_array(strtoupper($request->getMethod()), ['GET', 'HEAD'], true)) {
            $redirect = $redirect->withInput();
        }

        return $redirect->with('error', $this->getMessage());
    }
}
