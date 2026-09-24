<?php

require_once __DIR__ . '/BaseModel.php';

class Attendee extends BaseModel
{
    private int $id = 0;
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $phone;

    public function __construct (
        string $firstName = '',
        string $lastName = '',
        string $email = '',
        string $phone = ''
    ) {
        parent ::__construct();
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
    }

    public function getId(): int
    {
        return $this -> id;
    }

    public function getFirstName(): string
    {
        return $this -> firstName;
    }

    public function getLastName(): string
    {
        return $this -> lastName;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getEmail(): string
    {
        return $this -> email;
    }

    public function getPhone(): string
    {
        return $this -> phone;
    }

    public function setFirstName(string $firstName): void
    {
        $this -> firstName = trim($firstName);
    }

    public function setLastName(string $lastName): void
    {
        $this -> lastName = trim($lastName);
    }

    public function setEmail(string $email): void
    {
        $this -> email = trim($email);
    }

    public function setPhone(string $phone): void
    {
        $this -> phone = trim($phone);
    }

    public function isEmailValid(): bool
    {
        return filter_var($this -> email, FILTER_VALIDATE_EMAIL) !== false;
    }

}