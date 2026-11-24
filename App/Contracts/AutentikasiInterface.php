<?php
// app/Contracts/AutentikasiInterface.php
declare(strict_types=1);

namespace App\Contracts;

interface AutentikasiInterface
{
    public function login(string $email, string $password): ?array;
    public function logout(): void;
    public function updateProfil(string $idPengguna, array $data): bool;
}
