<?php
class BadgeSystem {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Check badge eligibility based on user's stats
    public function checkBadgeEligibility($userId) {
        $user = $this->getUserStats($userId);
        $availableBadges = $this->getAvailableBadges($userId);
        $newlyUnlockedBadges = [];

        foreach ($availableBadges as $badge) {
            if ($this->qualifiesForBadge($user, $badge)) {
                $this->unlockBadge($userId, $badge['id']);
                $newlyUnlockedBadges[] = $badge;
            }
        }

        return $newlyUnlockedBadges;
    }

    // Get user's current stats
    private function getUserStats($userId) {
        $sql = "SELECT u.points, u.level,
                (SELECT COUNT(*) FROM tasks WHERE ID = ? AND is_complete = 1) as completed_tasks,
                (SELECT COUNT(*) FROM user_logins WHERE user_id = ?) as login_count
                FROM users u WHERE u.ID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get badges that haven't been unlocked yet
    private function getAvailableBadges($userId) {
        $sql = "SELECT b.* FROM badges b 
                WHERE b.id NOT IN 
                (SELECT badge_id FROM user_badges WHERE user_id = ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check if user qualifies for a specific badge
    private function qualifiesForBadge($userStats, $badge) {
        return $userStats['points'] >= $badge['unlock_xp'] &&
               $userStats['login_count'] >= $badge['required_login_count'];
    }

    // Record badge unlock in the database
    private function unlockBadge($userId, $badgeId) {
        $sql = "INSERT INTO user_badges (user_id, badge_id, unlock_date) 
                VALUES (?, ?, CURRENT_TIMESTAMP)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $badgeId]);
    }

    // Get badge progress for display
    public function getBadgeProgress($userId) {
        $sql = "SELECT 
                b.*,
                CASE
                    WHEN ub.badge_id IS NOT NULL THEN 100
                    ELSE (
                        LEAST(
                            (u.points * 100 / NULLIF(b.unlock_xp, 0)),
                            ((SELECT COUNT(*) FROM user_logins WHERE user_id = ?) * 100 / 
                            NULLIF(b.required_login_count, 0))
                        )
                    )
                END as progress,
                ub.unlock_date,
                CASE 
                    WHEN ub.badge_id IS NOT NULL THEN 1 
                    ELSE 0 
                END as is_unlocked
                FROM badges b
                LEFT JOIN user_badges ub ON b.id = ub.badge_id AND ub.user_id = ?
                CROSS JOIN users u WHERE u.ID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Add this API endpoint to handle badge updates
class BadgeAPI {
    private $badgeSystem;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->badgeSystem = new BadgeSystem($db);
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? '';
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return $this->sendResponse(['error' => 'Unauthorized'], 401);
        }

        switch ($action) {
            case 'get_progress':
                return $this->sendResponse($this->badgeSystem->getBadgeProgress($userId));
            case 'check_eligibility':
                return $this->sendResponse($this->badgeSystem->checkBadgeEligibility($userId));
            default:
                return $this->sendResponse(['error' => 'Invalid action'], 400);
        }
    }

    private function sendResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}