<?php
declare(strict_types=1);

namespace App\Components;

use Carbon\CarbonInterface;

class AuthCredentials
{
    protected string $email;

    protected string $password;

    protected ?string $rememberToken;

    protected ?CarbonInterface $emailVerifiedAt;

    public function __construct(
        string           $email,
        string           $password,
        ?string          $rememberToken = null,
        ?CarbonInterface $emailVerifiedAt = null,
    )
    {
        $this->email           = $email;
        $this->password        = $password;
        $this->rememberToken   = $rememberToken;
        $this->emailVerifiedAt = $emailVerifiedAt;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return AuthCredentials
     */
    public function setEmail(string $email): AuthCredentials
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return \Carbon\CarbonInterface|null
     */
    public function getEmailVerifiedAt(): ?CarbonInterface
    {
        return $this->emailVerifiedAt;
    }

    /**
     * @param \Carbon\CarbonInterface|null $emailVerifiedAt
     *
     * @return AuthCredentials
     */
    public function setEmailVerifiedAt(?CarbonInterface $emailVerifiedAt): AuthCredentials
    {
        $this->emailVerifiedAt = $emailVerifiedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return AuthCredentials
     */
    public function setPassword(string $password): AuthCredentials
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRememberToken(): ?string
    {
        return $this->rememberToken;
    }

    /**
     * @param string|null $rememberToken
     *
     * @return AuthCredentials
     */
    public function setRememberToken(?string $rememberToken): AuthCredentials
    {
        $this->rememberToken = $rememberToken;
        return $this;
    }
}
