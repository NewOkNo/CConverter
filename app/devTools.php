<?php

namespace DevTools;

class DotEnv
{
    /**
     * The directory where the .env file can be located.
     *
     * @var string
     */
    protected $path;


    public function __construct(string $path)
    {
        if(!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
        }
        $this->path = $path;
    }

    public function load() :void
    {
        if (!is_readable($this->path)) {
            throw new \RuntimeException(sprintf('%s file is not readable', $this->path));
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
/*
class Memcached {
    public __construct(string $persistent_id = ?){}
    public add(string $key, mixed $value, int $expiration = ?): bool
    public addByKey(
        string $server_key,
        string $key,
        mixed $value,
        int $expiration = ?
    ): bool
    public addServer(string $host, int $port, int $weight = 0): bool
    public addServers(array $servers): bool
    public append(string $key, string $value): bool
    public appendByKey(string $server_key, string $key, string $value): bool
    public cas(
        float $cas_token,
        string $key,
        mixed $value,
        int $expiration = ?
    ): bool
    public casByKey(
        float $cas_token,
        string $server_key,
        string $key,
        mixed $value,
        int $expiration = ?
    ): bool
    public decrement(
        string $key,
        int $offset = 1,
        int $initial_value = 0,
        int $expiry = 0
    ): int|false
    public decrementByKey(
        string $server_key,
        string $key,
        int $offset = 1,
        int $initial_value = 0,
        int $expiry = 0
    ): int|false
    public delete(string $key, int $time = 0): bool
    public deleteByKey(string $server_key, string $key, int $time = 0): bool
    public deleteMulti(array $keys, int $time = 0): array
    public deleteMultiByKey(string $server_key, array $keys, int $time = 0): bool
    public fetch(): array
    public fetchAll(): array|false
    public flush(int $delay = 0): bool
    public get(string $key, callable $cache_cb = ?, int $$flags = ?): mixed
    public getAllKeys(): array|false
    public getByKey(
        string $server_key,
        string $key,
        callable $cache_cb = ?,
        int $flags = ?
    ): mixed
    public getDelayed(array $keys, bool $with_cas = ?, callable $value_cb = ?): bool
    public getDelayedByKey(
        string $server_key,
        array $keys,
        bool $with_cas = ?,
        callable $value_cb = ?
    ): bool
    public getMulti(array $keys, int $flags = ?): mixed
    public getMultiByKey(string $server_key, array $keys, int $flags = ?): array|false
    public getOption(int $option): mixed
    public getResultCode(): int
    public getResultMessage(): string
    public getServerByKey(string $server_key): array
    public getServerList(): array
    public getStats(): array|false
    public getVersion(): array
    public increment(
        string $key,
        int $offset = 1,
        int $initial_value = 0,
        int $expiry = 0
    ): int|false
    public incrementByKey(
        string $server_key,
        string $key,
        int $offset = 1,
        int $initial_value = 0,
        int $expiry = 0
    ): int|false
    public isPersistent(): bool
    public isPristine(): bool
    public prepend(string $key, string $value): bool
    public prependByKey(string $server_key, string $key, string $value): bool
    public quit(): bool
    public replace(string $key, mixed $value, int $expiration = ?): bool
    public replaceByKey(
        string $server_key,
        string $key,
        mixed $value,
        int $expiration = ?
    ): bool
    public resetServerList(): bool
    public set(string $key, mixed $value, int $expiration = ?): bool
    public setByKey(
        string $server_key,
        string $key,
        mixed $value,
        int $expiration = ?
    ): bool
    public setMulti(array $items, int $expiration = ?): bool
    public setMultiByKey(string $server_key, array $items, int $expiration = ?): bool
    public setOption(int $option, mixed $value): bool
    public setOptions(array $options): bool
    public setSaslAuthData(string $username, string $password): void
    public touch(string $key, int $expiration): bool
    public touchByKey(string $server_key, string $key, int $expiration): bool
}*/

?>