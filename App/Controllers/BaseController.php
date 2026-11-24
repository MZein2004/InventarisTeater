<?php
// app/Controllers/BaseController.php
declare(strict_types=1);

namespace App\Controllers;

abstract class BaseController
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    }

    abstract public function index(): void;

    protected function render(string $view, array $data = []): void
    {
        $data['baseUrl'] = $this->baseUrl;
        extract($data, EXTR_SKIP);
        $file = __DIR__ . '/../../views/' . $view . '.php';
        if (!file_exists($file)) exit('View tidak ditemukan.');
        require $file;
    }

    protected function redirect(string $query): void
    {
        header("Location: {$this->baseUrl}/index.php?{$query}");
        exit;
    }

    protected function requirePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') $this->redirect('page=dashboard');
    }
}
