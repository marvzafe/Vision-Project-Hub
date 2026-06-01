<?php
// /src/modules/contact_verification/contact-service.php
require_once __DIR__ . '/contact-repository.php';

class ContactVerificationService {
    private ContactVerificationRepository $repository;

    public function __construct(ContactVerificationRepository $repository) {
        $this->repository = $repository;
    }

    public function needsPhoneNumber(string $userId): bool {
        $phone = $this->repository->getUserPhone($userId);
        return empty($phone);
    }

    public function processAndSavePhoneNumber(string $userId, string $rawDigits): array {
        // Strip any non-numeric characters just in case
        $cleanDigits = preg_replace('/[^0-9]/', '', $rawDigits);

        // Validate exactly 10 digits
        if (strlen($cleanDigits) !== 10) {
            return ['success' => false, 'error' => 'Please enter exactly 10 digits.'];
        }

        // Format to Philippine mobile format (+63)
        $formattedNumber = '+63' . $cleanDigits;

        // Save to DB
        $saved = $this->repository->updatePhoneNumber($userId, $formattedNumber);

        if ($saved) {
            // Update session if you store phone there
            $_SESSION['user_phone'] = $formattedNumber;
            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Database update failed. Please try again.'];
    }
}