<?php
namespace App\Api\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

class ApiUser implements JWTUserInterface
{
    /**
     * @param integer $iat the date of issued (in timestamp)
     * @param integer $exp the expiration time (in timestamp)
     * @param array $roles list of user roles
     * @param string $username unique username of user
     * @param string $pseudo name of user
     * @param string $requestIp user ip
     */
    public function __construct(
        private int $iat,
        private int $exp,
        private array $roles,
        private string $username,
        private string $pseudo,
        private string $requestIp
    )
    {}

    public static function createFromPayload($username, array $payload)
    {
        return new self(
            $payload['iat'],
            $payload['exp'],
            $payload['roles'],
            $payload['username'],
            $payload['pseudo'],
            $payload['requestIp'],
        );
    }

    /**
     * Get Data Payload as Array
     *
     * @return array
     */
    public function toArray(): array
    {
        $payload['iat'] = $this->iat;
        $payload['exp'] = $this->exp;
        $payload['roles'] = $this->roles;
        $payload['username'] = $this->username;
        $payload['pseudo'] = $this->pseudo;
        $payload['requestIp'] = $this->requestIp;

        return $payload;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     * @return array|Collection<int, Role>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    /**
     * Get the value of iat
     * 
     * iat is the date of issued (in timestamp)
     */ 
    public function getIat()
    {
        return $this->iat;
    }

    /**
     * Get the value of exp
     * 
     * exp is the expiration time (in timestamp)
     */ 
    public function getExp()
    {
        return $this->exp;
    }

    /**
     * Get the value of username
     */ 
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the value of pseudo
     */ 
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Get the value of requestIp
     */ 
    public function getRequestIp()
    {
        return $this->requestIp;
    }
}