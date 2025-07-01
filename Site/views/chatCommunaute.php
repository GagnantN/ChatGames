<?php
// Connexion et session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '../../bdd/db.php');

$idUtilisateur = $_SESSION['user']['id'] ?? null;
$idCommunaute = $_GET['id'] ?? null;

if (!$idUtilisateur || !$idCommunaute) {
    die("Paramètres manquants.");
}

// Récupère les messages de la communauté
$stmt = $dbh->prepare("
    SELECT m.*, u.pseudo 
    FROM messages_communaute m 
    JOIN utilisateur u ON u.id_utilisateur = m.id_utilisateur
    WHERE id_communaute = ?
    ORDER BY date_envoi ASC
");
$stmt->execute([$idCommunaute]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="containerBody">
    <div class="commu-chat-wrapper">
        <div class="commu-messages-section">
            <?php foreach ($messages as $msg): ?>
                <?php if ($msg['type'] === 'system'): ?>
                    <div class="commu-system-message">
                        <?= htmlspecialchars($msg['contenu']) ?>
                    </div>
                <?php elseif ($msg['id_utilisateur'] == $idUtilisateur): ?>
                    <div class="commu-message-right">
                        <span class="commu-sender">Me</span>
                        <div class="commu-message-bubble right"><?= htmlspecialchars($msg['contenu']) ?></div>
                    </div>
                <?php else: ?>
                    <div class="commu-message-left">
                        <span class="commu-sender"><?= htmlspecialchars($msg['pseudo']) ?></span>
                        <div class="commu-message-bubble left"><?= htmlspecialchars($msg['contenu']) ?></div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <form class="commu-chat-form" method="POST" action="index.php?page=envoyerMessageCommunaute">
            <input type="hidden" name="id_communaute" value="<?= $idCommunaute ?>">
            <div class="commu-chat-input-wrapper">
                <button type="button" class="commu-icon">😊</button>
                <button type="button" class="commu-icon">📎</button>
                <input type="text" name="message" class="commu-chat-input" placeholder="Type a message or send a voice note">
                <button type="submit" class="commu-send-button">➤</button>
            </div>
        </form>
    </div>
</div>