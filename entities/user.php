<?php

class User
{
    /**
     * A `User` class mapping with `users` table
     *
     * - $id: Integer
     * - $username: String
     * - $email: String
     * - $password: String
     *
     * */

    private $id;
    private $username;
    private $email;
    private $password;

    public function __construct($user)
    {
        $this->id = $user["id"];
        $this->username = $user["username"];
        $this->email = $user["email"];
        $this->password = $user["password"];
    }

    public function __destruct()
    {
    }

    /**
     * Verify provided password with hashed password
     *
     * @param string $password User's password
     *
     * @return bool
     */
    public function verify_password($password)
    {
        return password_verify($password, $this->password);
    }

    /**
     * Return user ID
     *
     * @return integer
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Return username
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * Set new username
     *
     * @param string $username New username
     */
    public function set_username($username)
    {
        $this->username = $username;
    }

    /**
     * Return email
     *
     * @return string
     */
    public function email()
    {
        return $this->email;
    }

    /**
     * Set new email
     *
     * @param string $email New email
     */
    public function set_email($email)
    {
        $this->email = $email;
    }
}
