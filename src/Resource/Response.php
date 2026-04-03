<?php

namespace Myerscode\Utilities\Web\Resource;

use JsonException;

readonly class Response
{
    public function __construct(private int $code, private string $content = '', private array $headers = [])
    {
    }

    public function code(): int
    {
        return $this->code;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function dom(): Dom
    {
        return new Dom($this->content());
    }

    /**
     * Get a single header value by name (case-insensitive)
     */
    public function header(string $name): ?string
    {
        $name = strtolower($name);

        foreach ($this->headers as $key => $value) {
            if (strtolower((string) $key) === $name) {
                if (is_array($value)) {
                    $first = $value[0] ?? null;

                    return is_string($first) ? $first : null;
                }

                return is_string($value) ? $value : null;
            }
        }

        return null;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Check if the response is a 4xx client error
     */
    public function isClientError(): bool
    {
        return $this->code >= 400 && $this->code < 500;
    }

    /**
     * Check if the Content-Type header indicates JSON
     */
    public function isJson(): bool
    {
        $contentType = $this->header('content-type');

        if ($contentType === null) {
            return false;
        }

        return str_contains($contentType, 'application/json');
    }

    /**
     * Check if the response is a 3xx redirect
     */
    public function isRedirect(): bool
    {
        return $this->code >= 300 && $this->code < 400;
    }

    /**
     * Check if the response is a 5xx server error
     */
    public function isServerError(): bool
    {
        return $this->code >= 500 && $this->code < 600;
    }

    /**
     * Check if the response is a 2xx success
     */
    public function isSuccessful(): bool
    {
        return $this->code >= 200 && $this->code < 300;
    }

    /**
     * Decode the response content as JSON
     *
     * @return array<mixed>
     *
     * @throws JsonException
     */
    public function json(): array
    {
        /** @var array<mixed> $decoded */
        $decoded = json_decode($this->content, true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }

    /**
     * Get the response as an array
     *
     * @return array{code: int, content: string, headers: array<mixed>}
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'content' => $this->content,
            'headers' => $this->headers,
        ];
    }
}
