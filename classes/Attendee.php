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
    return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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

    //create attendee in the database
    public function create(): bool
    {
        $sql = "INSERT INTO attendees
                (
                    first_name,
                    last_name,
                    email,
                    phone
                )
                VALUES
                (
                    :first_name,
                    :last_name,
                    :email,
                    :phone
                )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':first_name' => $this->firstName,
            ':last_name' => $this->lastName,
            ':email' => $this->email,
            ':phone' => $this->phone
        ]);
    }

    //read all attendees from the database
    public function getAllAttendees(string $search = ''): array
    {
    $sql = "SELECT
                id,
                first_name,
                last_name,
                email,
                phone,
                created_at
            FROM attendees";

    $params = [];

    if ($search !== '') {

        $sql .= " WHERE first_name LIKE :first_name_search
                    OR last_name LIKE :last_name_search
                    OR email LIKE :email_search";

        $searchValue = '%' . $search . '%';

        $params[':first_name_search'] = $searchValue;
        $params[':last_name_search'] = $searchValue;
        $params[':email_search'] = $searchValue;
        }

        $sql .= " ORDER BY last_name ASC, first_name ASC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    //read a single attendee by ID from the database
    public function getAttendeeById(int $id): ?array
    {
        $sql = "SELECT
                    id,
                    first_name,
                    last_name,
                    email,
                    phone,
                    created_at
                FROM attendees
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        $attendee = $stmt->fetch();

        if ($attendee === false) {
            return null;
        }

        return $attendee;
    }

    //read a single attendee by email from the database
    public function getAttendeeByEmail(string $email): ?array
    {
        $sql = "SELECT
                id,
                first_name,
                last_name,
                email,
                phone,
                created_at
            FROM attendees
            WHERE email = :email";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        $attendee = $stmt->fetch();

        if ($attendee === false) {
        return null;
        }

        return $attendee;
    }

    //update an attendee in the database
    public function update(): bool
    {
        $sql = "UPDATE attendees
                SET
                    first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    phone = :phone
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':first_name' => $this->firstName,
            ':last_name' => $this->lastName,
            ':email' => $this->email,
            ':phone' => $this->phone,
            ':id' => $this->id
        ]);
    }

    //delete an attendee from the database
    public function delete(): bool
    {
        $sql = "DELETE FROM attendees
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $this->id
        ]);
    }

}