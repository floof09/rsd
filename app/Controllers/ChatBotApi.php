<?php
namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class ChatBotApi extends BaseController
{
    /**
     * Simple rule-based chatbot for unauthenticated login page assistance.
     * Accepts POST JSON or form data with 'message'. Returns JSON:
     * { messages: [{ role: 'bot'|'user', text: string }], suggestions?: [] }
     */
    public function respond(): ResponseInterface
    {
        // Basic rate limiting (IP + time window) to discourage spam without persistence
        $ip = $this->request->getIPAddress();
        $now = time();
        $session = session();
        $bucket = $session->get('chatbot_bucket') ?? [];
        // Clean old entries (>60s)
        $bucket = array_filter($bucket, function($ts) use ($now) { return ($now - $ts) < 60; });
        if (count($bucket) >= 25) {
            return $this->respondJson(['error' => 'Too many messages. Please wait a moment.'], 429);
        }
        $bucket[] = $now;
        $session->set('chatbot_bucket', $bucket);

        $raw = $this->request->getPost('message');
        if ($raw === null) {
            // Try JSON body
            $json = $this->request->getJSON(true);
            $raw = $json['message'] ?? '';
        }
        $message = trim((string)$raw);
        if ($message === '') {
            return $this->respondJson([
                'messages' => [ ['role' => 'bot', 'text' => 'Hi! Ask me about logging in, resetting password, or platform features.'] ],
                'suggestions' => ['How do I reset my password?', 'What roles are supported?', 'Is my data secure?']
            ]);
        }

        $lower = mb_strtolower($message);
        $reply = $this->matchIntent($lower);

        return $this->respondJson([
            'messages' => [
                ['role' => 'user', 'text' => $message],
                ['role' => 'bot', 'text' => $reply['text']],
            ],
            'suggestions' => $reply['suggestions'] ?? []
        ]);
    }

    private function matchIntent(string $lower): array
    {
        // Simple keyword intents
        $mapping = [
            'password' => [
                'text' => 'To reset your password: click "Forgot password" on the login form, enter your email, and follow the link we send. If you do not see it, check spam or contact an admin.',
                'suggestions' => ['I did not get the reset email', 'How long is the reset link valid?']
            ],
            'reset' => [
                'text' => 'Password reset: the link is valid for 30 minutes. Use a new password you have not used before.',
                'suggestions' => ['I did not get the reset email', 'Is there MFA?']
            ],
            'role' => [
                'text' => 'Roles: Admin, Interviewer, and Recruiter (pending). Each role has different dashboard access and actions.',
                'suggestions' => ['What can interviewers do?', 'What can admins do?']
            ],
            'admin' => [
                'text' => 'Admins can manage users, companies, and view all applications & system logs.',
                'suggestions' => ['What can interviewers do?', 'How to add a company?']
            ],
            'interviewer' => [
                'text' => 'Interviewers create applications, complete company-specific steps, and endorse candidates when ready.',
                'suggestions' => ['How do I endorse?', 'What is the second interview?']
            ],
            'endorse' => [
                'text' => 'Endorse: After the second interview (or required company-specific step) you can mark an application as "Approved for Endorsement". Recruiters then proceed.',
                'suggestions' => ['What requires a second interview?', 'How do I schedule another interview?']
            ],
            'secure' => [
                'text' => 'Security: data is stored with restricted access and actions are logged. Use strong passwords; sensitive fields are validated and sanitized.',
                'suggestions' => ['Are resumes encrypted?', 'How are logs stored?']
            ],
            'resume' => [
                'text' => 'Resumes: Upload PDF <= 5MB. They can be previewed inline and downloaded by authorized users only.',
                'suggestions' => ['Why PDF only?', 'Where are resumes stored?']
            ],
            'company' => [
                'text' => 'Company-specific fields appear after initial application save (Step 2). Admins define schema in the Company Form Builder.',
                'suggestions' => ['How do admins add fields?', 'Can I edit custom fields later?']
            ],
            'second interview' => [
                'text' => 'Second interview: some companies require an additional stage (e.g., IGT). Use the provided tools to record or schedule it.',
                'suggestions' => ['How do I start IGT interview?', 'When can I endorse?']
            ],
        ];

        foreach ($mapping as $key => $resp) {
            if (strpos($lower, $key) !== false) { return $resp; }
        }

        // Fallback generic response
        return [
            'text' => 'I\'m not sure about that yet. Try asking about password reset, roles, endorsement, company fields, or security.',
            'suggestions' => ['How do I reset my password?', 'What are the user roles?', 'Is the platform secure?']
        ];
    }

    private function respondJson(array $payload, int $status = 200): ResponseInterface
    {
        return $this->response->setStatusCode($status)->setContentType('application/json')->setJSON($payload);
    }
}
