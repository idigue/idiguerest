<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once 'dbcontroller.php'; // Include the database controller
class ProjectProposalAPI
{
    private $db_handle;
    private $baseUrl;

    public function __construct()
    {
        $this->db_handle = new DBController();
        $this->baseUrl = 'https://www.idigue.com';
    }

    public function getApiInfo()
    {
        $this->sendResponse(200, [
            'api_name' => 'Project Proposal REST API',
            'version' => '1.0',
            'base_url' => $this->baseUrl,
            'endpoints' => [
                'GET ' . $this->baseUrl . '/api/proposals' => 'Get all proposals',
                'GET ' . $this->baseUrl . '/api/proposals/{id}' => 'Get single proposal',
                'POST ' . $this->baseUrl . '/api/proposals' => 'Create proposal',
                'PUT ' . $this->baseUrl . '/api/proposals/{id}' => 'Update proposal',
                'DELETE ' . $this->baseUrl . '/api/proposals/{id}' => 'Delete proposal',
                'POST ' . $this->baseUrl . '/api/proposals/reset' => 'Reset all proposals'
            ]
        ]);
    }

    public function handleRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));
        var_dump($method);
        // Extract ID if present (e.g., /api/proposals/123)
        $id = isset($segments[2]) ? (int)$segments[2] : null;

        // Check for reset endpoint (e.g., /api/proposals/reset)
        $action = isset($segments[2]) && !is_numeric($segments[2]) ? $segments[2] : null;
        var_dump($id);
        var_dump($action);
        switch ($method) {
            case 'GET':
                if ($action === 'info') {
                    $this->getApiInfo();
                } elseif ($id) {
                    $this->getProposal($id);
                } else {
                    $this->getAllProposals();
                }
                break;
            case 'POST':
                if ($action === 'reset') {
                    // $this->resetProposals();
                } else {
                    // $this->createProposal();
                }
                break;

            default:
                $this->sendResponse(405, ['error' => 'Method not allowed']);
        }
    }

    private function getAllProposals()
    {
        $proposals = $this->db_handle->runQuery("SELECT * FROM proposals ORDER BY timecreated DESC");

        $this->sendResponse(200, [
            'data' => $proposals
        ]);
    }

    private function getProposal($id)
    {
        $proposal = $this->db_handle->runQuery("SELECT * FROM proposals WHERE id = " . $id);
        if ($proposal && count($proposal) > 0) {
            $this->sendResponse(200, ['data' => $proposal[0]]);
        } else {
            $this->sendResponse(404, ['error' => 'Proposal not found']);
        }
    }
    private function sendResponse($statusCode, $data)
    {
        http_response_code($statusCode);
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit();
    }
}

// Initialize and handle the request
try {
    $api = new ProjectProposalAPI();
    $api->handleRequest();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error'], JSON_PRETTY_PRINT);
}
