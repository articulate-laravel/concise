<?php
declare(strict_types=1);

namespace App\Entities;

use App\Components\AuthCredentials;
use App\Components\Timestamps;
use Illuminate\Contracts\Auth\Authenticatable;

class User implements Authenticatable
{
    protected int $id;

    protected string $name;

    protected AuthCredentials $auth;

    protected Timestamps $timestamps;

    /**
     * @return \App\Components\AuthCredentials
     */
    public function getAuth(): AuthCredentials
    {
        return $this->auth;
    }

    /**
     * @param \App\Components\AuthCredentials $auth
     *
     * @return User
     */
    public function setAuth(AuthCredentials $auth): User
    {
        $this->auth = $auth;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return User
     */
    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasId(): bool
    {
        return isset($this->id);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \App\Components\Timestamps
     */
    public function getTimestamps(): Timestamps
    {
        return $this->timestamps;
    }

    /**
     * @param \App\Components\Timestamps $timestamps
     *
     * @return User
     */
    public function setTimestamps(Timestamps $timestamps): User
    {
        $this->timestamps = $timestamps;
        return $this;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return int
     */
    public function getAuthIdentifier(): int
    {
        return $this->getId();
    }

    /**
     * Get the name of the password attribute for the user.
     *
     * @return string
     */
    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return $this->getAuth()->getPassword();
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken(): string
    {
        return $this->getAuth()->getRememberToken();
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     *
     * @return void
     */
    public function setRememberToken($value): void
    {
        $this->getAuth()->setRememberToken($value);
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }
}
