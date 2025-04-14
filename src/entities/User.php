<?php

namespace app\entities;

class User
{
    public int $id = 0;
    public string $created_at = '';
    public string $updated_at = '';
    public string $email = '';
    public ?string $phone = null;
    public string $fullname = '';
    public string $password = '';
    public string $username = '';
    public ?string $title = null;
    public string $company = '';
    public string $dob = '';
    public ?string $address = null;
    public ?string $avatar = null;

    const TABLE_NAME = 'users';

    public function decodeHtmlSpecialChars()
    {
        $this->fullname = html_entity_decode($this->fullname, ENT_QUOTES);
        $this->title = $this->title ? html_entity_decode($this->title, ENT_QUOTES) : null;
        $this->company = html_entity_decode($this->company, ENT_QUOTES);
        $this->address = $this->address ? html_entity_decode($this->address, ENT_QUOTES | ENT_HTML5, 'UTF-8') : null;
    }
}
