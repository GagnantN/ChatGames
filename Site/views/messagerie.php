<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '../../bdd/db.php');

// Récupération de l'utilisateur connecté
$utilisateur_id = $_SESSION['user']['id'] ?? null;

if (!$utilisateur_id) {
    die("Utilisateur non connecté.");
}

// Conversation active ?
$activeId = $_GET['id'] ?? null;

// 1. Charger les conversations
$stmt = $dbh->prepare("
    SELECT c.id, 
           c.user1,
           c.user2,
           u.pseudo, 
           u.imageProfil AS image,
           (SELECT content FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) AS last_message, 
           (SELECT content FROM messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) AS last_message_time,
           (SELECT COUNT(*) FROM messages WHERE conversation_id = c.id AND sender_id != ? AND lu = 0) AS unread_count
    FROM conversations c
    JOIN utilisateur u ON u.id_utilisateur = IF(c.user1 = ?, c.user2, c.user1)
    WHERE c.user1 = ? OR c.user2 = ?
");

$stmt->execute([$utilisateur_id, $utilisateur_id, $utilisateur_id, $utilisateur_id]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Charger les messages de la conversation active
$messages = [];
if ($activeId) {
    $stmt = $dbh->prepare("
        SELECT m.*, m.sender_id = ? AS from_me 
        FROM messages m 
        WHERE m.conversation_id = ? 
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([$utilisateur_id, $activeId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="containerBody">
    <div class="headerAccueil">
        <h1>Messagerie</h1>
        <a href="index.php?page=profil">
            <img src="Site/assets/images/<?= htmlspecialchars($_SESSION['user']['imageProfil']) ?>" >
            <?= htmlspecialchars($_SESSION['user']['pseudo'] ?? '') ?>
        </a>
    </div>
</div>

<div class="messagerie-container">
    <!-- Colonne gauche : Liste des discussions -->
    <div class="liste-conversations">
        <?php foreach ($conversations as $conv): ?>
            <a href="index.php?page=messagerie&id=<?= $conv['id'] ?>" class="conversation-item <?= $conv['id'] == $activeId ? 'active' : '' ?>">
                <img src="Site/assets/images/<?= $conv['image'] ?>" class="avatar">
                <div>
                    <div class="pseudo"><?= htmlspecialchars($conv['pseudo']) ?></div>
                    <div class="last-message"><?= htmlspecialchars($conv['last_message']) ?></div>
                </div>
                    <?php if ($conv['unread_count']): ?>
                        <span class="badge"><?= $conv['unread_count'] ?></span>
                    <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Colonne droite : Chat actif -->
    <div class="chat-conversation">
        <div class="messages">
            <?php foreach ($messages as $msg): ?>
                <?php if ($msg['type'] === 'system'): ?>
                    <div class="message-system">
                        <p><?= htmlspecialchars($msg['content']) ?></p>
                        <?php if (empty($msg['response'])): ?>
                            <form method="POST" action="index.php?page=repondreDemandeAmi">
                                <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
                                <button name="reponse" value="oui">✅ Oui</button>
                                <button name="reponse" value="non">❌ Non</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="message <?= $msg['from_me'] ? 'me' : 'them' ?>">
                        <?php if ($msg['type'] === 'image'): ?>
                            <img src="<?= htmlspecialchars($msg['content']) ?>" class="message-image">
                        <?php else: ?>
                            <div class="content"><?= htmlspecialchars($msg['content']) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if ($activeId): // Juste avant le formulaire dans messagerie.php
            $destinataire_id = null;
            if ($activeId) {
                $stmt = $dbh->prepare("
                    SELECT user1, user2 FROM conversations WHERE id = ?
                ");
                $stmt->execute([$activeId]);
                $conv = $stmt->fetch();

                if ($conv) {
                    $destinataire_id = ($conv['user1'] == $utilisateur_id) ? $conv['user2'] : $conv['user1'];
                }
            }
        ?>
            
        <form class="send-form" method="POST" action="index.php?page=envoyerMessage" enctype="multipart/form-data">
            <input type="hidden" name="destinataire_id" value="<?= $destinataire_id ?>">
            
            <div class="input-bar">
                <label for="fileInput" class="emoji">📎</label>
                <input type="file" id="fileInput" name="image" style="display:none">
                
                <div class="emoji" onclick="insertEmoji('😊')">😊</div>
                <div class="emoji" onclick="insertEmoji('🔥')">🔥</div>
                <div class="emoji" onclick="insertEmoji('❤️')">❤️</div>
                <div class="emoji" onclick="insertEmoji('👍')">👍</div>
                <div class="emoji" onclick="insertEmoji('😂')">😂</div>

                <input type="text" name="message" placeholder="Tapez un message..." class="message-input">
                <button type="submit">📨</button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>
